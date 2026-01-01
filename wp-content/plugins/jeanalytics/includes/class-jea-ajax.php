<?php
/**
 * AJAX处理类
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_Ajax {

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
        // 前端追踪AJAX
        add_action('wp_ajax_jea_track_pageview', array($this, 'track_pageview'));
        add_action('wp_ajax_nopriv_jea_track_pageview', array($this, 'track_pageview'));

        add_action('wp_ajax_jea_track_event', array($this, 'track_event'));
        add_action('wp_ajax_nopriv_jea_track_event', array($this, 'track_event'));

        add_action('wp_ajax_jea_track_exit', array($this, 'track_exit'));
        add_action('wp_ajax_nopriv_jea_track_exit', array($this, 'track_exit'));

        add_action('wp_ajax_jea_heartbeat', array($this, 'heartbeat'));
        add_action('wp_ajax_nopriv_jea_heartbeat', array($this, 'heartbeat'));

        // 后台数据AJAX
        add_action('wp_ajax_jea_get_dashboard_data', array($this, 'get_dashboard_data'));
        add_action('wp_ajax_jea_get_realtime_data', array($this, 'get_realtime_data'));
        add_action('wp_ajax_jea_get_chart_data', array($this, 'get_chart_data'));
        add_action('wp_ajax_jea_export_data', array($this, 'export_data'));
    }

    /**
     * 验证nonce
     */
    private function verify_nonce() {
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : '';
        if (!wp_verify_nonce($nonce, 'jea_track')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
            exit;
        }
    }

    /**
     * 获取请求数据
     */
    private function get_data() {
        $data = isset($_POST['data']) ? $_POST['data'] : '';
        return json_decode(stripslashes($data), true);
    }

    /**
     * 追踪页面访问
     */
    public function track_pageview() {
        $this->verify_nonce();

        global $wpdb;

        $data = $this->get_data();

        if (empty($data)) {
            wp_send_json_error(array('message' => 'No data'));
            exit;
        }

        $ip = JEA_Tracker::get_user_ip();
        $ua_info = JEA_Tracker::parse_user_agent();
        $is_bot = JEA_Tracker::is_bot() ? 1 : 0;

        // 获取或创建访客记录
        $visitor_hash = hash('sha256', $ip . '|' . sanitize_text_field($data['visitorId']));
        $visitors_table = JEA_Database::get_table('visitors');

        $visitor = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $visitors_table WHERE visitor_hash = %s",
            $visitor_hash
        ));

        $geo_data = JEA_GeoIP::lookup($ip);

        $now = current_time('mysql');

        if ($visitor) {
            // 更新访客
            $wpdb->update(
                $visitors_table,
                array(
                    'last_visit' => $now,
                    'visit_count' => $visitor->visit_count + 1,
                ),
                array('id' => $visitor->id)
            );
            $visitor_id = $visitor->id;
        } else {
            // 创建新访客
            $wpdb->insert(
                $visitors_table,
                array(
                    'visitor_hash' => $visitor_hash,
                    'first_visit' => $now,
                    'last_visit' => $now,
                    'visit_count' => 1,
                    'ip_address' => $ip,
                    'country' => $geo_data['country'],
                    'country_code' => $geo_data['country_code'],
                    'city' => $geo_data['city'],
                    'region' => $geo_data['region'],
                    'latitude' => $geo_data['latitude'],
                    'longitude' => $geo_data['longitude'],
                    'browser' => $ua_info['browser'],
                    'browser_version' => $ua_info['browser_version'],
                    'os' => $ua_info['os'],
                    'os_version' => $ua_info['os_version'],
                    'device_type' => $ua_info['device_type'],
                    'device_brand' => $ua_info['device_brand'],
                    'device_model' => $ua_info['device_model'],
                    'screen_width' => intval($data['screenWidth']),
                    'screen_height' => intval($data['screenHeight']),
                    'language' => sanitize_text_field($data['language']),
                    'timezone' => sanitize_text_field($data['timezone']),
                    'is_bot' => $is_bot,
                    'user_id' => get_current_user_id(),
                )
            );
            $visitor_id = $wpdb->insert_id;
        }

        // 处理会话
        $session_id = sanitize_text_field($data['sessionId']);
        $sessions_table = JEA_Database::get_table('sessions');

        $session = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $sessions_table WHERE session_id = %s",
            $session_id
        ));

        $referrer = esc_url_raw($data['referrer']);
        $referrer_domain = sanitize_text_field($data['referrerDomain']);
        $referrer_type = JEA_Tracker::parse_referrer_type($referrer);

        if (!$session) {
            // 创建新会话
            $wpdb->insert(
                $sessions_table,
                array(
                    'session_id' => $session_id,
                    'visitor_id' => $visitor_id,
                    'start_time' => $now,
                    'end_time' => $now,
                    'pageviews' => 1,
                    'entry_page' => esc_url_raw($data['pageUrl']),
                    'exit_page' => esc_url_raw($data['pageUrl']),
                    'referrer' => $referrer,
                    'referrer_domain' => $referrer_domain,
                    'referrer_type' => $referrer_type,
                    'utm_source' => sanitize_text_field($data['utmSource']),
                    'utm_medium' => sanitize_text_field($data['utmMedium']),
                    'utm_campaign' => sanitize_text_field($data['utmCampaign']),
                    'is_bounce' => 1,
                )
            );
        } else {
            // 更新会话
            $wpdb->update(
                $sessions_table,
                array(
                    'end_time' => $now,
                    'pageviews' => $session->pageviews + 1,
                    'exit_page' => esc_url_raw($data['pageUrl']),
                    'is_bounce' => 0,
                    'duration' => strtotime($now) - strtotime($session->start_time),
                ),
                array('id' => $session->id)
            );
        }

        // 记录页面访问
        $pageviews_table = JEA_Database::get_table('pageviews');

        $wpdb->insert(
            $pageviews_table,
            array(
                'visitor_id' => $visitor_id,
                'session_id' => $session_id,
                'page_url' => esc_url_raw($data['pageUrl']),
                'page_title' => sanitize_text_field($data['pageTitle']),
                'page_type' => sanitize_text_field($data['pageType']),
                'post_id' => intval($data['postId']),
                'referrer' => $referrer,
                'referrer_domain' => $referrer_domain,
                'referrer_type' => $referrer_type,
                'utm_source' => sanitize_text_field($data['utmSource']),
                'utm_medium' => sanitize_text_field($data['utmMedium']),
                'utm_campaign' => sanitize_text_field($data['utmCampaign']),
                'utm_term' => sanitize_text_field($data['utmTerm']),
                'utm_content' => sanitize_text_field($data['utmContent']),
                'entry_page' => !empty($data['isEntryPage']) ? 1 : 0,
                'created_at' => $now,
            )
        );

        // 更新实时数据
        $this->update_realtime($visitor_hash, $session_id, $data, $ua_info, $geo_data);

        wp_send_json_success(array('message' => 'Tracked'));
    }

    /**
     * 追踪事件
     */
    public function track_event() {
        $this->verify_nonce();

        global $wpdb;

        $data = $this->get_data();

        if (empty($data)) {
            wp_send_json_error(array('message' => 'No data'));
            exit;
        }

        $visitor_hash = hash('sha256', JEA_Tracker::get_user_ip() . '|' . sanitize_text_field($data['visitorId']));
        $visitors_table = JEA_Database::get_table('visitors');

        $visitor = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $visitors_table WHERE visitor_hash = %s",
            $visitor_hash
        ));

        if (!$visitor) {
            wp_send_json_error(array('message' => 'Visitor not found'));
            exit;
        }

        $events_table = JEA_Database::get_table('events');

        $wpdb->insert(
            $events_table,
            array(
                'visitor_id' => $visitor->id,
                'session_id' => sanitize_text_field($data['sessionId']),
                'event_category' => sanitize_text_field($data['category']),
                'event_action' => sanitize_text_field($data['action']),
                'event_label' => sanitize_text_field($data['label']),
                'event_value' => floatval($data['value']),
                'page_url' => esc_url_raw($data['pageUrl']),
                'created_at' => current_time('mysql'),
            )
        );

        wp_send_json_success(array('message' => 'Event tracked'));
    }

    /**
     * 追踪退出
     */
    public function track_exit() {
        global $wpdb;

        $data = $this->get_data();

        if (empty($data)) {
            wp_send_json_error();
            exit;
        }

        $session_id = sanitize_text_field($data['sessionId']);
        $time_on_page = intval($data['timeOnPage']);
        $scroll_depth = intval($data['scrollDepth']);

        // 更新页面访问的退出信息
        $pageviews_table = JEA_Database::get_table('pageviews');

        $wpdb->query($wpdb->prepare(
            "UPDATE $pageviews_table
             SET exit_page = 1, time_on_page = %d, scroll_depth = %d
             WHERE session_id = %s AND page_url = %s
             ORDER BY id DESC LIMIT 1",
            $time_on_page,
            $scroll_depth,
            $session_id,
            esc_url_raw($data['pageUrl'])
        ));

        // 更新会话
        $sessions_table = JEA_Database::get_table('sessions');

        $wpdb->query($wpdb->prepare(
            "UPDATE $sessions_table
             SET end_time = %s, exit_page = %s, duration = TIMESTAMPDIFF(SECOND, start_time, %s)
             WHERE session_id = %s",
            current_time('mysql'),
            esc_url_raw($data['pageUrl']),
            current_time('mysql'),
            $session_id
        ));

        wp_send_json_success();
    }

    /**
     * 心跳
     */
    public function heartbeat() {
        $this->verify_nonce();

        $data = $this->get_data();

        if (empty($data)) {
            wp_send_json_error();
            exit;
        }

        $ip = JEA_Tracker::get_user_ip();
        $visitor_hash = hash('sha256', $ip . '|' . sanitize_text_field($data['visitorId']));

        $ua_info = JEA_Tracker::parse_user_agent();
        $geo_data = JEA_GeoIP::lookup($ip);

        $this->update_realtime($visitor_hash, sanitize_text_field($data['sessionId']), $data, $ua_info, $geo_data);

        wp_send_json_success();
    }

    /**
     * 更新实时数据
     */
    private function update_realtime($visitor_hash, $session_id, $data, $ua_info, $geo_data) {
        global $wpdb;

        $realtime_table = JEA_Database::get_table('realtime');

        $wpdb->query($wpdb->prepare(
            "INSERT INTO $realtime_table
             (visitor_hash, session_id, page_url, page_title, referrer, country, country_code, city, device_type, browser, os, last_activity)
             VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
             ON DUPLICATE KEY UPDATE
             page_url = VALUES(page_url),
             page_title = VALUES(page_title),
             last_activity = VALUES(last_activity)",
            $visitor_hash,
            $session_id,
            esc_url_raw($data['pageUrl']),
            isset($data['pageTitle']) ? sanitize_text_field($data['pageTitle']) : '',
            isset($data['referrer']) ? esc_url_raw($data['referrer']) : '',
            $geo_data['country'],
            $geo_data['country_code'],
            $geo_data['city'],
            $ua_info['device_type'],
            $ua_info['browser'],
            $ua_info['os'],
            current_time('mysql')
        ));

        // 清理5分钟前的实时数据
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $realtime_table WHERE last_activity < %s",
            date('Y-m-d H:i:s', strtotime('-5 minutes'))
        ));
    }

    /**
     * 获取仪表板数据
     */
    public function get_dashboard_data() {
        check_ajax_referer('jea_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
            exit;
        }

        $range = isset($_POST['range']) ? sanitize_text_field($_POST['range']) : '7days';

        $stats = new JEA_Stats();
        $data = $stats->get_dashboard_stats($range);

        wp_send_json_success($data);
    }

    /**
     * 获取实时数据
     */
    public function get_realtime_data() {
        check_ajax_referer('jea_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
            exit;
        }

        global $wpdb;

        $realtime_table = JEA_Database::get_table('realtime');

        // 获取5分钟内的活跃访客
        $visitors = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $realtime_table
             WHERE last_activity > %s
             ORDER BY last_activity DESC",
            date('Y-m-d H:i:s', strtotime('-5 minutes'))
        ));

        // 汇总统计
        $total = count($visitors);
        $by_country = array();
        $by_device = array();
        $by_page = array();

        foreach ($visitors as $v) {
            // 按国家
            $country = $v->country_code ?: 'Unknown';
            if (!isset($by_country[$country])) {
                $by_country[$country] = array(
                    'code' => $country,
                    'name' => $v->country ?: 'Unknown',
                    'count' => 0
                );
            }
            $by_country[$country]['count']++;

            // 按设备
            $device = $v->device_type ?: 'desktop';
            if (!isset($by_device[$device])) {
                $by_device[$device] = 0;
            }
            $by_device[$device]++;

            // 按页面
            $page = $v->page_url;
            if (!isset($by_page[$page])) {
                $by_page[$page] = array(
                    'url' => $page,
                    'title' => $v->page_title,
                    'count' => 0
                );
            }
            $by_page[$page]['count']++;
        }

        // 排序
        usort($by_country, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        usort($by_page, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        wp_send_json_success(array(
            'total' => $total,
            'visitors' => array_slice($visitors, 0, 50),
            'by_country' => array_slice(array_values($by_country), 0, 10),
            'by_device' => $by_device,
            'by_page' => array_slice(array_values($by_page), 0, 10),
        ));
    }

    /**
     * 获取图表数据
     */
    public function get_chart_data() {
        check_ajax_referer('jea_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
            exit;
        }

        $chart_type = isset($_POST['chart']) ? sanitize_text_field($_POST['chart']) : 'visitors';
        $range = isset($_POST['range']) ? sanitize_text_field($_POST['range']) : '7days';

        $stats = new JEA_Stats();
        $data = $stats->get_chart_data($chart_type, $range);

        wp_send_json_success($data);
    }

    /**
     * 导出数据
     */
    public function export_data() {
        check_ajax_referer('jea_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
            exit;
        }

        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'visitors';
        $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : 'csv';
        $range = isset($_POST['range']) ? sanitize_text_field($_POST['range']) : '30days';

        $export = new JEA_Export();
        $result = $export->export($type, $format, $range);

        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(array('message' => 'Export failed'));
        }
    }
}
