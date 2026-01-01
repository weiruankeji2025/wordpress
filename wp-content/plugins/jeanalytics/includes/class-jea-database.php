<?php
/**
 * 数据库管理类
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_Database {

    /**
     * 数据库版本
     */
    const DB_VERSION = '1.0.0';

    /**
     * 创建数据表
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // 访客表 - 存储独立访客信息
        $table_visitors = $wpdb->prefix . 'jea_visitors';
        $sql_visitors = "CREATE TABLE IF NOT EXISTS $table_visitors (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            visitor_hash VARCHAR(64) NOT NULL,
            first_visit DATETIME NOT NULL,
            last_visit DATETIME NOT NULL,
            visit_count INT(11) UNSIGNED DEFAULT 1,
            ip_address VARCHAR(45),
            country VARCHAR(100),
            country_code VARCHAR(10),
            city VARCHAR(100),
            region VARCHAR(100),
            latitude DECIMAL(10, 8),
            longitude DECIMAL(11, 8),
            browser VARCHAR(100),
            browser_version VARCHAR(50),
            os VARCHAR(100),
            os_version VARCHAR(50),
            device_type VARCHAR(50),
            device_brand VARCHAR(100),
            device_model VARCHAR(100),
            screen_width INT(11),
            screen_height INT(11),
            language VARCHAR(20),
            timezone VARCHAR(100),
            is_bot TINYINT(1) DEFAULT 0,
            user_id BIGINT(20) UNSIGNED DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY visitor_hash (visitor_hash),
            KEY ip_address (ip_address),
            KEY country_code (country_code),
            KEY device_type (device_type),
            KEY browser (browser),
            KEY os (os),
            KEY first_visit (first_visit),
            KEY last_visit (last_visit),
            KEY is_bot (is_bot)
        ) $charset_collate;";

        // 页面访问表 - 存储每次页面访问
        $table_pageviews = $wpdb->prefix . 'jea_pageviews';
        $sql_pageviews = "CREATE TABLE IF NOT EXISTS $table_pageviews (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            visitor_id BIGINT(20) UNSIGNED NOT NULL,
            session_id VARCHAR(64) NOT NULL,
            page_url VARCHAR(2048) NOT NULL,
            page_title VARCHAR(500),
            page_type VARCHAR(50),
            post_id BIGINT(20) UNSIGNED DEFAULT 0,
            referrer VARCHAR(2048),
            referrer_domain VARCHAR(255),
            referrer_type VARCHAR(50),
            utm_source VARCHAR(255),
            utm_medium VARCHAR(255),
            utm_campaign VARCHAR(255),
            utm_term VARCHAR(255),
            utm_content VARCHAR(255),
            entry_page TINYINT(1) DEFAULT 0,
            exit_page TINYINT(1) DEFAULT 0,
            time_on_page INT(11) UNSIGNED DEFAULT 0,
            scroll_depth INT(3) UNSIGNED DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY visitor_id (visitor_id),
            KEY session_id (session_id),
            KEY post_id (post_id),
            KEY page_type (page_type),
            KEY referrer_type (referrer_type),
            KEY created_at (created_at),
            KEY entry_page (entry_page),
            KEY exit_page (exit_page)
        ) $charset_collate;";

        // 会话表 - 存储访问会话
        $table_sessions = $wpdb->prefix . 'jea_sessions';
        $sql_sessions = "CREATE TABLE IF NOT EXISTS $table_sessions (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            session_id VARCHAR(64) NOT NULL,
            visitor_id BIGINT(20) UNSIGNED NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME,
            duration INT(11) UNSIGNED DEFAULT 0,
            pageviews INT(11) UNSIGNED DEFAULT 1,
            entry_page VARCHAR(2048),
            exit_page VARCHAR(2048),
            referrer VARCHAR(2048),
            referrer_domain VARCHAR(255),
            referrer_type VARCHAR(50),
            utm_source VARCHAR(255),
            utm_medium VARCHAR(255),
            utm_campaign VARCHAR(255),
            is_bounce TINYINT(1) DEFAULT 1,
            is_converted TINYINT(1) DEFAULT 0,
            conversion_value DECIMAL(10, 2) DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY session_id (session_id),
            KEY visitor_id (visitor_id),
            KEY start_time (start_time),
            KEY referrer_type (referrer_type),
            KEY is_bounce (is_bounce)
        ) $charset_collate;";

        // 事件表 - 存储自定义事件
        $table_events = $wpdb->prefix . 'jea_events';
        $sql_events = "CREATE TABLE IF NOT EXISTS $table_events (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            visitor_id BIGINT(20) UNSIGNED NOT NULL,
            session_id VARCHAR(64) NOT NULL,
            event_category VARCHAR(100) NOT NULL,
            event_action VARCHAR(100) NOT NULL,
            event_label VARCHAR(255),
            event_value DECIMAL(10, 2),
            page_url VARCHAR(2048),
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY visitor_id (visitor_id),
            KEY session_id (session_id),
            KEY event_category (event_category),
            KEY event_action (event_action),
            KEY created_at (created_at)
        ) $charset_collate;";

        // 实时数据表 - 存储实时访客
        $table_realtime = $wpdb->prefix . 'jea_realtime';
        $sql_realtime = "CREATE TABLE IF NOT EXISTS $table_realtime (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            visitor_hash VARCHAR(64) NOT NULL,
            session_id VARCHAR(64) NOT NULL,
            page_url VARCHAR(2048) NOT NULL,
            page_title VARCHAR(500),
            referrer VARCHAR(2048),
            country VARCHAR(100),
            country_code VARCHAR(10),
            city VARCHAR(100),
            device_type VARCHAR(50),
            browser VARCHAR(100),
            os VARCHAR(100),
            last_activity DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY visitor_session (visitor_hash, session_id),
            KEY last_activity (last_activity)
        ) $charset_collate;";

        // 每日统计汇总表
        $table_stats_daily = $wpdb->prefix . 'jea_stats_daily';
        $sql_stats_daily = "CREATE TABLE IF NOT EXISTS $table_stats_daily (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            stat_date DATE NOT NULL,
            visitors INT(11) UNSIGNED DEFAULT 0,
            new_visitors INT(11) UNSIGNED DEFAULT 0,
            returning_visitors INT(11) UNSIGNED DEFAULT 0,
            pageviews INT(11) UNSIGNED DEFAULT 0,
            sessions INT(11) UNSIGNED DEFAULT 0,
            bounces INT(11) UNSIGNED DEFAULT 0,
            total_duration INT(11) UNSIGNED DEFAULT 0,
            avg_duration INT(11) UNSIGNED DEFAULT 0,
            avg_pages_per_session DECIMAL(5, 2) DEFAULT 0,
            bounce_rate DECIMAL(5, 2) DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY stat_date (stat_date)
        ) $charset_collate;";

        // 页面统计汇总表
        $table_stats_pages = $wpdb->prefix . 'jea_stats_pages';
        $sql_stats_pages = "CREATE TABLE IF NOT EXISTS $table_stats_pages (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            stat_date DATE NOT NULL,
            page_url VARCHAR(2048) NOT NULL,
            page_title VARCHAR(500),
            post_id BIGINT(20) UNSIGNED DEFAULT 0,
            pageviews INT(11) UNSIGNED DEFAULT 0,
            unique_visitors INT(11) UNSIGNED DEFAULT 0,
            entries INT(11) UNSIGNED DEFAULT 0,
            exits INT(11) UNSIGNED DEFAULT 0,
            bounces INT(11) UNSIGNED DEFAULT 0,
            total_time INT(11) UNSIGNED DEFAULT 0,
            avg_time INT(11) UNSIGNED DEFAULT 0,
            avg_scroll_depth INT(3) UNSIGNED DEFAULT 0,
            PRIMARY KEY (id),
            KEY stat_date (stat_date),
            KEY post_id (post_id),
            KEY pageviews (pageviews)
        ) $charset_collate;";

        // 来源统计汇总表
        $table_stats_referrers = $wpdb->prefix . 'jea_stats_referrers';
        $sql_stats_referrers = "CREATE TABLE IF NOT EXISTS $table_stats_referrers (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            stat_date DATE NOT NULL,
            referrer_domain VARCHAR(255),
            referrer_type VARCHAR(50),
            visitors INT(11) UNSIGNED DEFAULT 0,
            sessions INT(11) UNSIGNED DEFAULT 0,
            pageviews INT(11) UNSIGNED DEFAULT 0,
            bounces INT(11) UNSIGNED DEFAULT 0,
            total_duration INT(11) UNSIGNED DEFAULT 0,
            PRIMARY KEY (id),
            KEY stat_date (stat_date),
            KEY referrer_type (referrer_type),
            KEY visitors (visitors)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql_visitors);
        dbDelta($sql_pageviews);
        dbDelta($sql_sessions);
        dbDelta($sql_events);
        dbDelta($sql_realtime);
        dbDelta($sql_stats_daily);
        dbDelta($sql_stats_pages);
        dbDelta($sql_stats_referrers);

        update_option('jea_db_version', self::DB_VERSION);
    }

    /**
     * 删除数据表
     */
    public static function drop_tables() {
        global $wpdb;

        $tables = array(
            $wpdb->prefix . 'jea_visitors',
            $wpdb->prefix . 'jea_pageviews',
            $wpdb->prefix . 'jea_sessions',
            $wpdb->prefix . 'jea_events',
            $wpdb->prefix . 'jea_realtime',
            $wpdb->prefix . 'jea_stats_daily',
            $wpdb->prefix . 'jea_stats_pages',
            $wpdb->prefix . 'jea_stats_referrers',
        );

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }

        delete_option('jea_db_version');
    }

    /**
     * 获取表名
     */
    public static function get_table($table) {
        global $wpdb;
        return $wpdb->prefix . 'jea_' . $table;
    }

    /**
     * 清理过期数据
     */
    public static function cleanup_old_data($days = 365) {
        global $wpdb;

        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        // 清理过期的页面访问记录
        $wpdb->query($wpdb->prepare(
            "DELETE FROM " . self::get_table('pageviews') . " WHERE created_at < %s",
            $date
        ));

        // 清理过期的会话记录
        $wpdb->query($wpdb->prepare(
            "DELETE FROM " . self::get_table('sessions') . " WHERE start_time < %s",
            $date
        ));

        // 清理过期的事件记录
        $wpdb->query($wpdb->prepare(
            "DELETE FROM " . self::get_table('events') . " WHERE created_at < %s",
            $date
        ));

        // 清理实时数据(5分钟前)
        $realtime_date = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $wpdb->query($wpdb->prepare(
            "DELETE FROM " . self::get_table('realtime') . " WHERE last_activity < %s",
            $realtime_date
        ));
    }
}
