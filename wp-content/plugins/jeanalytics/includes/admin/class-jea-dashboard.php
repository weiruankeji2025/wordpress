<?php
/**
 * 仪表板类(辅助功能)
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_Dashboard {

    /**
     * 格式化时长
     */
    public static function format_duration($seconds) {
        if ($seconds < 60) {
            return $seconds . '秒';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $secs = $seconds % 60;
            return $minutes . '分' . ($secs > 0 ? $secs . '秒' : '');
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return $hours . '时' . ($minutes > 0 ? $minutes . '分' : '');
        }
    }

    /**
     * 格式化数字
     */
    public static function format_number($number) {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return number_format($number);
    }

    /**
     * 获取变化图标
     */
    public static function get_change_icon($change) {
        if ($change > 0) {
            return '<svg class="jea-icon-up" viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M7 14l5-5 5 5H7z"/></svg>';
        } elseif ($change < 0) {
            return '<svg class="jea-icon-down" viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M7 10l5 5 5-5H7z"/></svg>';
        }
        return '<span class="jea-icon-neutral">—</span>';
    }

    /**
     * 获取设备图标
     */
    public static function get_device_icon($type) {
        $icons = array(
            'desktop' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M20 3H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h6v2H8v2h8v-2h-2v-2h6a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm0 12H4V5h16v10z"/></svg>',
            'mobile' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M16 1H8C6.34 1 5 2.34 5 4v16c0 1.66 1.34 3 3 3h8c1.66 0 3-1.34 3-3V4c0-1.66-1.34-3-3-3zm-2 20h-4v-1h4v1zm3.25-3H6.75V4h10.5v14z"/></svg>',
            'tablet' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M19.25 19c0 .23-.02.46-.05.68-.06.44-.16.87-.3 1.28-.01.03-.02.06-.04.09-.11.31-.25.61-.41.89-.55 1-.99 1.06-2.95 1.06H8.5c-1.96 0-2.4-.05-2.95-1.06-.16-.28-.3-.58-.41-.89-.02-.03-.03-.06-.04-.09-.14-.41-.24-.84-.3-1.28-.03-.22-.05-.45-.05-.68V5c0-.24.02-.47.05-.7.06-.43.16-.86.3-1.27.01-.03.02-.06.04-.09.11-.31.25-.61.41-.89C6.1 1.05 6.54 1 8.5 1h7c1.96 0 2.4.05 2.95 1.06.16.28.3.58.41.89.02.03.03.06.04.09.14.41.24.84.3 1.27.03.23.05.46.05.69v14zM10 19h4v1h-4v-1zM18 5H6v12h12V5z"/></svg>',
        );

        return isset($icons[$type]) ? $icons[$type] : $icons['desktop'];
    }

    /**
     * 获取浏览器图标
     */
    public static function get_browser_icon($browser) {
        $browser = strtolower($browser);

        if (strpos($browser, 'chrome') !== false) {
            return '🌐';
        } elseif (strpos($browser, 'firefox') !== false) {
            return '🦊';
        } elseif (strpos($browser, 'safari') !== false) {
            return '🧭';
        } elseif (strpos($browser, 'edge') !== false) {
            return '🌊';
        } elseif (strpos($browser, 'opera') !== false) {
            return '🔴';
        }

        return '🌐';
    }

    /**
     * 获取国家旗帜
     */
    public static function get_country_flag($code) {
        if (empty($code) || strlen($code) !== 2) {
            return '🌍';
        }

        $code = strtoupper($code);
        $offset = 127397;

        $flag = '';
        for ($i = 0; $i < 2; $i++) {
            $flag .= mb_chr(ord($code[$i]) + $offset);
        }

        return $flag;
    }

    /**
     * 获取来源类型图标
     */
    public static function get_referrer_icon($type) {
        $icons = array(
            'direct' => '🔗',
            'search' => '🔍',
            'social' => '📱',
            'referral' => '🌐',
            'email' => '📧',
            'internal' => '🏠',
        );

        return isset($icons[$type]) ? $icons[$type] : '🔗';
    }

    /**
     * 截断URL显示
     */
    public static function truncate_url($url, $max_length = 50) {
        $path = parse_url($url, PHP_URL_PATH);

        if (strlen($path) > $max_length) {
            return '...' . substr($path, -($max_length - 3));
        }

        return $path ?: '/';
    }
}
