<?php
/**
 * 访客追踪类
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_Tracker {

    /**
     * 单例实例
     */
    private static $instance = null;

    /**
     * 设置
     */
    private $settings;

    /**
     * 获取单例实例
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 构造函数
     */
    private function __construct() {
        $this->settings = get_option('jea_settings', array());
        $this->init_hooks();
    }

    /**
     * 初始化钩子
     */
    private function init_hooks() {
        // 只在前端加载追踪脚本
        if (!is_admin()) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_tracking_script'));
            add_action('wp_footer', array($this, 'output_tracking_data'), 100);
        }
    }

    /**
     * 加载追踪脚本
     */
    public function enqueue_tracking_script() {
        if ($this->should_track()) {
            wp_enqueue_script(
                'jea-tracker',
                JEA_PLUGIN_URL . 'assets/js/tracker.js',
                array(),
                JEA_VERSION,
                true
            );

            wp_localize_script('jea-tracker', 'jeaConfig', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('jea_track'),
                'sessionTimeout' => 30, // 分钟
                'heartbeatInterval' => 15, // 秒
                'scrollDepthMarks' => array(25, 50, 75, 100),
            ));
        }
    }

    /**
     * 输出追踪数据
     */
    public function output_tracking_data() {
        if (!$this->should_track()) {
            return;
        }

        global $post;

        $page_data = array(
            'pageUrl' => $this->get_current_url(),
            'pageTitle' => wp_get_document_title(),
            'pageType' => $this->get_page_type(),
            'postId' => is_singular() && $post ? $post->ID : 0,
            'referrer' => isset($_SERVER['HTTP_REFERER']) ? esc_url($_SERVER['HTTP_REFERER']) : '',
        );

        // 提取UTM参数
        $utm_params = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content');
        foreach ($utm_params as $param) {
            if (isset($_GET[$param])) {
                $page_data[$param] = sanitize_text_field($_GET[$param]);
            }
        }

        echo '<script type="text/javascript">var jeaPageData = ' . wp_json_encode($page_data) . ';</script>';
    }

    /**
     * 是否应该追踪
     */
    private function should_track() {
        // 检查是否是管理员且设置不追踪管理员
        if (current_user_can('manage_options') && isset($this->settings['track_admin']) && $this->settings['track_admin'] !== 'yes') {
            return false;
        }

        // 检查是否追踪已登录用户
        if (is_user_logged_in() && isset($this->settings['track_logged_users']) && $this->settings['track_logged_users'] !== 'yes') {
            return false;
        }

        // 检查排除IP
        if (!empty($this->settings['exclude_ips'])) {
            $exclude_ips = array_map('trim', explode("\n", $this->settings['exclude_ips']));
            $user_ip = $this->get_user_ip();
            if (in_array($user_ip, $exclude_ips)) {
                return false;
            }
        }

        // 不追踪机器人(可选)
        if ($this->is_bot()) {
            return false;
        }

        return true;
    }

    /**
     * 获取当前URL
     */
    private function get_current_url() {
        $protocol = is_ssl() ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取页面类型
     */
    private function get_page_type() {
        if (is_front_page()) {
            return 'frontpage';
        } elseif (is_home()) {
            return 'blog';
        } elseif (is_single()) {
            return 'post';
        } elseif (is_page()) {
            return 'page';
        } elseif (is_category()) {
            return 'category';
        } elseif (is_tag()) {
            return 'tag';
        } elseif (is_author()) {
            return 'author';
        } elseif (is_archive()) {
            return 'archive';
        } elseif (is_search()) {
            return 'search';
        } elseif (is_404()) {
            return '404';
        }
        return 'other';
    }

    /**
     * 获取用户IP
     */
    public static function get_user_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        );

        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // 处理多个IP的情况
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * 检测是否是机器人
     */
    public static function is_bot() {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }

        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        $bots = array(
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
            'mj12bot', 'ahrefsbot', 'semrushbot', 'dotbot', 'rogerbot',
            'bot', 'spider', 'crawler', 'scraper', 'curl', 'wget',
            'python', 'java', 'perl', 'ruby', 'headless', 'phantom'
        );

        foreach ($bots as $bot) {
            if (strpos($user_agent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 解析User Agent
     */
    public static function parse_user_agent($user_agent = null) {
        if (is_null($user_agent)) {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }

        $result = array(
            'browser' => 'Unknown',
            'browser_version' => '',
            'os' => 'Unknown',
            'os_version' => '',
            'device_type' => 'desktop',
            'device_brand' => '',
            'device_model' => '',
        );

        if (empty($user_agent)) {
            return $result;
        }

        // 检测设备类型
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod|webOS|BlackBerry|Opera Mini|IEMobile/i', $user_agent)) {
            if (preg_match('/iPad|Tablet|Tab/i', $user_agent)) {
                $result['device_type'] = 'tablet';
            } else {
                $result['device_type'] = 'mobile';
            }
        }

        // 检测操作系统
        $os_patterns = array(
            '/windows nt 10/i' => array('Windows', '10'),
            '/windows nt 6.3/i' => array('Windows', '8.1'),
            '/windows nt 6.2/i' => array('Windows', '8'),
            '/windows nt 6.1/i' => array('Windows', '7'),
            '/windows nt 6.0/i' => array('Windows', 'Vista'),
            '/windows nt 5.1/i' => array('Windows', 'XP'),
            '/macintosh|mac os x/i' => array('macOS', ''),
            '/mac_powerpc/i' => array('Mac OS', '9'),
            '/linux/i' => array('Linux', ''),
            '/ubuntu/i' => array('Ubuntu', ''),
            '/iphone/i' => array('iOS', ''),
            '/ipad/i' => array('iPadOS', ''),
            '/android/i' => array('Android', ''),
            '/webos/i' => array('webOS', ''),
            '/chromeos/i' => array('Chrome OS', ''),
        );

        foreach ($os_patterns as $pattern => $os) {
            if (preg_match($pattern, $user_agent)) {
                $result['os'] = $os[0];
                $result['os_version'] = $os[1];
                break;
            }
        }

        // 提取Android版本
        if ($result['os'] === 'Android' && preg_match('/Android\s([0-9\.]+)/i', $user_agent, $matches)) {
            $result['os_version'] = $matches[1];
        }

        // 提取iOS版本
        if (in_array($result['os'], array('iOS', 'iPadOS')) && preg_match('/OS\s([0-9_]+)/i', $user_agent, $matches)) {
            $result['os_version'] = str_replace('_', '.', $matches[1]);
        }

        // 提取macOS版本
        if ($result['os'] === 'macOS' && preg_match('/Mac OS X\s([0-9_\.]+)/i', $user_agent, $matches)) {
            $result['os_version'] = str_replace('_', '.', $matches[1]);
        }

        // 检测浏览器
        $browser_patterns = array(
            '/edge\/([\d\.]+)/i' => 'Edge',
            '/edg\/([\d\.]+)/i' => 'Edge',
            '/opr\/([\d\.]+)/i' => 'Opera',
            '/opera\/([\d\.]+)/i' => 'Opera',
            '/chrome\/([\d\.]+)/i' => 'Chrome',
            '/safari\/([\d\.]+)/i' => 'Safari',
            '/firefox\/([\d\.]+)/i' => 'Firefox',
            '/msie\s([\d\.]+)/i' => 'Internet Explorer',
            '/trident.*rv:([\d\.]+)/i' => 'Internet Explorer',
            '/samsung/i' => 'Samsung Browser',
            '/ucbrowser\/([\d\.]+)/i' => 'UC Browser',
        );

        foreach ($browser_patterns as $pattern => $browser) {
            if (preg_match($pattern, $user_agent, $matches)) {
                $result['browser'] = $browser;
                $result['browser_version'] = isset($matches[1]) ? $matches[1] : '';

                // Safari需要特殊处理
                if ($browser === 'Safari') {
                    // 检查是否是Chrome伪装的Safari
                    if (preg_match('/chrome/i', $user_agent)) {
                        continue;
                    }
                    if (preg_match('/version\/([\d\.]+)/i', $user_agent, $v)) {
                        $result['browser_version'] = $v[1];
                    }
                }
                break;
            }
        }

        // 检测设备品牌和型号
        if (preg_match('/iPhone/i', $user_agent)) {
            $result['device_brand'] = 'Apple';
            $result['device_model'] = 'iPhone';
        } elseif (preg_match('/iPad/i', $user_agent)) {
            $result['device_brand'] = 'Apple';
            $result['device_model'] = 'iPad';
        } elseif (preg_match('/Samsung|SM-|GT-/i', $user_agent)) {
            $result['device_brand'] = 'Samsung';
            if (preg_match('/(SM-[A-Z0-9]+|GT-[A-Z0-9]+)/i', $user_agent, $m)) {
                $result['device_model'] = $m[1];
            }
        } elseif (preg_match('/Huawei|HUAWEI/i', $user_agent)) {
            $result['device_brand'] = 'Huawei';
        } elseif (preg_match('/Xiaomi|Redmi|Mi\s/i', $user_agent)) {
            $result['device_brand'] = 'Xiaomi';
        } elseif (preg_match('/OPPO/i', $user_agent)) {
            $result['device_brand'] = 'OPPO';
        } elseif (preg_match('/vivo/i', $user_agent)) {
            $result['device_brand'] = 'Vivo';
        } elseif (preg_match('/OnePlus/i', $user_agent)) {
            $result['device_brand'] = 'OnePlus';
        } elseif (preg_match('/Pixel/i', $user_agent)) {
            $result['device_brand'] = 'Google';
            $result['device_model'] = 'Pixel';
        }

        return $result;
    }

    /**
     * 解析来源类型
     */
    public static function parse_referrer_type($referrer) {
        if (empty($referrer)) {
            return 'direct';
        }

        $referrer_host = parse_url($referrer, PHP_URL_HOST);
        $site_host = parse_url(home_url(), PHP_URL_HOST);

        // 内部流量
        if ($referrer_host === $site_host) {
            return 'internal';
        }

        // 搜索引擎
        $search_engines = array(
            'google' => 'google',
            'bing' => 'bing',
            'yahoo' => 'yahoo',
            'baidu' => 'baidu',
            'yandex' => 'yandex',
            'duckduckgo' => 'duckduckgo',
            'sogou' => 'sogou',
            'so.com' => '360',
            'shenma' => 'shenma',
        );

        foreach ($search_engines as $engine => $name) {
            if (stripos($referrer_host, $engine) !== false) {
                return 'search';
            }
        }

        // 社交媒体
        $social_networks = array(
            'facebook', 'twitter', 'instagram', 'linkedin', 'pinterest',
            'youtube', 'tiktok', 'reddit', 'weibo', 'wechat', 'qq.com',
            'whatsapp', 'telegram', 'snapchat', 't.co', 'fb.com'
        );

        foreach ($social_networks as $network) {
            if (stripos($referrer_host, $network) !== false) {
                return 'social';
            }
        }

        // 邮件
        $email_domains = array('mail', 'outlook', 'gmail', 'yahoo', 'newsletter');
        foreach ($email_domains as $email) {
            if (stripos($referrer_host, $email) !== false) {
                return 'email';
            }
        }

        return 'referral';
    }

    /**
     * 生成访客哈希
     */
    public static function generate_visitor_hash() {
        $components = array(
            self::get_user_ip(),
            isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '',
        );

        return hash('sha256', implode('|', $components));
    }

    /**
     * 生成会话ID
     */
    public static function generate_session_id() {
        return hash('sha256', uniqid(mt_rand(), true) . self::get_user_ip() . microtime(true));
    }
}
