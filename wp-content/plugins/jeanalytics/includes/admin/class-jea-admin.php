<?php
/**
 * 管理后台类
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_Admin {

    /**
     * 单例实例
     */
    private static $instance = null;

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
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_init', array($this, 'register_settings'));

        // 仪表板小部件
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
    }

    /**
     * 添加菜单
     */
    public function add_menu() {
        // 主菜单
        add_menu_page(
            __('JE Analytics', 'jeanalytics'),
            __('流量分析', 'jeanalytics'),
            'manage_options',
            'jeanalytics',
            array($this, 'render_dashboard'),
            'dashicons-chart-area',
            30
        );

        // 子菜单
        add_submenu_page(
            'jeanalytics',
            __('仪表板', 'jeanalytics'),
            __('仪表板', 'jeanalytics'),
            'manage_options',
            'jeanalytics',
            array($this, 'render_dashboard')
        );

        add_submenu_page(
            'jeanalytics',
            __('实时访客', 'jeanalytics'),
            __('实时访客', 'jeanalytics'),
            'manage_options',
            'jeanalytics-realtime',
            array($this, 'render_realtime')
        );

        add_submenu_page(
            'jeanalytics',
            __('页面统计', 'jeanalytics'),
            __('页面统计', 'jeanalytics'),
            'manage_options',
            'jeanalytics-pages',
            array($this, 'render_pages')
        );

        add_submenu_page(
            'jeanalytics',
            __('流量来源', 'jeanalytics'),
            __('流量来源', 'jeanalytics'),
            'manage_options',
            'jeanalytics-referrers',
            array($this, 'render_referrers')
        );

        add_submenu_page(
            'jeanalytics',
            __('访客分析', 'jeanalytics'),
            __('访客分析', 'jeanalytics'),
            'manage_options',
            'jeanalytics-visitors',
            array($this, 'render_visitors')
        );

        add_submenu_page(
            'jeanalytics',
            __('设置', 'jeanalytics'),
            __('设置', 'jeanalytics'),
            'manage_options',
            'jeanalytics-settings',
            array($this, 'render_settings')
        );
    }

    /**
     * 加载资源
     */
    public function enqueue_assets($hook) {
        if (strpos($hook, 'jeanalytics') === false && $hook !== 'index.php') {
            return;
        }

        // Chart.js
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
            array(),
            '4.4.1',
            true
        );

        // 管理样式
        wp_enqueue_style(
            'jea-admin',
            JEA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            JEA_VERSION
        );

        // 管理脚本
        wp_enqueue_script(
            'jea-admin',
            JEA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'chartjs'),
            JEA_VERSION,
            true
        );

        wp_localize_script('jea-admin', 'jeaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jea_admin'),
            'i18n' => array(
                'visitors' => __('访客', 'jeanalytics'),
                'pageviews' => __('浏览量', 'jeanalytics'),
                'sessions' => __('会话', 'jeanalytics'),
                'loading' => __('加载中...', 'jeanalytics'),
                'noData' => __('暂无数据', 'jeanalytics'),
                'error' => __('加载失败，请重试', 'jeanalytics'),
            ),
        ));
    }

    /**
     * 注册设置
     */
    public function register_settings() {
        register_setting('jea_settings_group', 'jea_settings', array(
            'sanitize_callback' => array($this, 'sanitize_settings'),
        ));
    }

    /**
     * 清理设置
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        $sanitized['track_logged_users'] = isset($input['track_logged_users']) ? 'yes' : 'no';
        $sanitized['track_admin'] = isset($input['track_admin']) ? 'yes' : 'no';
        $sanitized['exclude_ips'] = sanitize_textarea_field($input['exclude_ips'] ?? '');
        $sanitized['data_retention'] = absint($input['data_retention'] ?? 365);
        $sanitized['realtime_refresh'] = absint($input['realtime_refresh'] ?? 30);
        $sanitized['dashboard_widget'] = isset($input['dashboard_widget']) ? 'yes' : 'no';

        return $sanitized;
    }

    /**
     * 渲染仪表板
     */
    public function render_dashboard() {
        include JEA_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }

    /**
     * 渲染实时访客
     */
    public function render_realtime() {
        include JEA_PLUGIN_DIR . 'templates/admin-realtime.php';
    }

    /**
     * 渲染页面统计
     */
    public function render_pages() {
        include JEA_PLUGIN_DIR . 'templates/admin-pages.php';
    }

    /**
     * 渲染流量来源
     */
    public function render_referrers() {
        include JEA_PLUGIN_DIR . 'templates/admin-referrers.php';
    }

    /**
     * 渲染访客分析
     */
    public function render_visitors() {
        include JEA_PLUGIN_DIR . 'templates/admin-visitors.php';
    }

    /**
     * 渲染设置
     */
    public function render_settings() {
        include JEA_PLUGIN_DIR . 'templates/admin-settings.php';
    }

    /**
     * 添加仪表板小部件
     */
    public function add_dashboard_widget() {
        $settings = get_option('jea_settings', array());

        if (isset($settings['dashboard_widget']) && $settings['dashboard_widget'] === 'no') {
            return;
        }

        wp_add_dashboard_widget(
            'jea_dashboard_widget',
            __('📊 网站流量概览', 'jeanalytics'),
            array($this, 'render_dashboard_widget')
        );
    }

    /**
     * 渲染仪表板小部件
     */
    public function render_dashboard_widget() {
        $stats = new JEA_Stats();
        $data = $stats->get_dashboard_stats('7days');
        $overview = $data['overview'];
        ?>
        <div class="jea-widget">
            <div class="jea-widget-stats">
                <div class="jea-widget-stat">
                    <span class="jea-widget-value"><?php echo number_format($overview['visitors']['value']); ?></span>
                    <span class="jea-widget-label"><?php _e('访客', 'jeanalytics'); ?></span>
                    <span class="jea-widget-change <?php echo $overview['visitors']['change'] >= 0 ? 'up' : 'down'; ?>">
                        <?php echo ($overview['visitors']['change'] >= 0 ? '+' : '') . $overview['visitors']['change']; ?>%
                    </span>
                </div>
                <div class="jea-widget-stat">
                    <span class="jea-widget-value"><?php echo number_format($overview['pageviews']['value']); ?></span>
                    <span class="jea-widget-label"><?php _e('浏览量', 'jeanalytics'); ?></span>
                    <span class="jea-widget-change <?php echo $overview['pageviews']['change'] >= 0 ? 'up' : 'down'; ?>">
                        <?php echo ($overview['pageviews']['change'] >= 0 ? '+' : '') . $overview['pageviews']['change']; ?>%
                    </span>
                </div>
                <div class="jea-widget-stat">
                    <span class="jea-widget-value"><?php echo $overview['bounce_rate']['value']; ?>%</span>
                    <span class="jea-widget-label"><?php _e('跳出率', 'jeanalytics'); ?></span>
                    <span class="jea-widget-change <?php echo $overview['bounce_rate']['change'] >= 0 ? 'up' : 'down'; ?>">
                        <?php echo ($overview['bounce_rate']['change'] >= 0 ? '+' : '') . $overview['bounce_rate']['change']; ?>%
                    </span>
                </div>
            </div>
            <p class="jea-widget-footer">
                <a href="<?php echo admin_url('admin.php?page=jeanalytics'); ?>"><?php _e('查看详细报告 →', 'jeanalytics'); ?></a>
            </p>
        </div>
        <style>
            .jea-widget-stats { display: flex; gap: 20px; margin-bottom: 15px; }
            .jea-widget-stat { flex: 1; text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px; }
            .jea-widget-value { display: block; font-size: 24px; font-weight: 600; color: #1e1e1e; }
            .jea-widget-label { display: block; font-size: 12px; color: #666; margin-top: 5px; }
            .jea-widget-change { display: block; font-size: 12px; margin-top: 5px; font-weight: 500; }
            .jea-widget-change.up { color: #22c55e; }
            .jea-widget-change.down { color: #ef4444; }
            .jea-widget-footer { text-align: right; margin: 0; }
        </style>
        <?php
    }
}
