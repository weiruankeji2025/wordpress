<?php
/**
 * 设置类
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_Settings {

    /**
     * 获取默认设置
     */
    public static function get_defaults() {
        return array(
            'track_logged_users' => 'yes',
            'track_admin' => 'no',
            'exclude_ips' => '',
            'data_retention' => 365,
            'realtime_refresh' => 30,
            'dashboard_widget' => 'yes',
        );
    }

    /**
     * 获取设置
     */
    public static function get($key = null) {
        $settings = get_option('jea_settings', self::get_defaults());
        $settings = wp_parse_args($settings, self::get_defaults());

        if ($key) {
            return isset($settings[$key]) ? $settings[$key] : null;
        }

        return $settings;
    }

    /**
     * 更新设置
     */
    public static function update($key, $value) {
        $settings = self::get();
        $settings[$key] = $value;
        update_option('jea_settings', $settings);
    }
}
