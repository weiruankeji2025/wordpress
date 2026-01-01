<?php
/**
 * GeoIP地理位置查询类
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_GeoIP {

    /**
     * 缓存
     */
    private static $cache = array();

    /**
     * 查询IP地理位置
     */
    public static function lookup($ip) {
        // 默认结果
        $default = array(
            'country' => '',
            'country_code' => '',
            'city' => '',
            'region' => '',
            'latitude' => 0,
            'longitude' => 0,
        );

        // 验证IP
        if (!filter_var($ip, FILTER_VALIDATE_IP) || self::is_private_ip($ip)) {
            return $default;
        }

        // 检查缓存
        if (isset(self::$cache[$ip])) {
            return self::$cache[$ip];
        }

        // 尝试使用免费API
        $result = self::query_ip_api($ip);

        if (!$result) {
            $result = self::query_ipinfo($ip);
        }

        if (!$result) {
            $result = $default;
        }

        // 缓存结果
        self::$cache[$ip] = $result;

        return $result;
    }

    /**
     * 检查是否是私有IP
     */
    private static function is_private_ip($ip) {
        $private_ranges = array(
            '10.0.0.0|10.255.255.255',
            '172.16.0.0|172.31.255.255',
            '192.168.0.0|192.168.255.255',
            '127.0.0.0|127.255.255.255',
            '0.0.0.0|0.255.255.255',
        );

        $ip_long = ip2long($ip);

        foreach ($private_ranges as $range) {
            list($start, $end) = explode('|', $range);
            if ($ip_long >= ip2long($start) && $ip_long <= ip2long($end)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 使用ip-api.com查询
     */
    private static function query_ip_api($ip) {
        $url = "http://ip-api.com/json/{$ip}?fields=status,country,countryCode,regionName,city,lat,lon";

        $response = wp_remote_get($url, array(
            'timeout' => 5,
            'sslverify' => false,
        ));

        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data) || $data['status'] !== 'success') {
            return null;
        }

        return array(
            'country' => isset($data['country']) ? $data['country'] : '',
            'country_code' => isset($data['countryCode']) ? $data['countryCode'] : '',
            'city' => isset($data['city']) ? $data['city'] : '',
            'region' => isset($data['regionName']) ? $data['regionName'] : '',
            'latitude' => isset($data['lat']) ? $data['lat'] : 0,
            'longitude' => isset($data['lon']) ? $data['lon'] : 0,
        );
    }

    /**
     * 使用ipinfo.io查询
     */
    private static function query_ipinfo($ip) {
        $url = "https://ipinfo.io/{$ip}/json";

        $response = wp_remote_get($url, array(
            'timeout' => 5,
        ));

        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data) || isset($data['error'])) {
            return null;
        }

        $latitude = 0;
        $longitude = 0;

        if (!empty($data['loc'])) {
            $loc = explode(',', $data['loc']);
            $latitude = floatval($loc[0]);
            $longitude = floatval($loc[1]);
        }

        return array(
            'country' => isset($data['country']) ? self::get_country_name($data['country']) : '',
            'country_code' => isset($data['country']) ? $data['country'] : '',
            'city' => isset($data['city']) ? $data['city'] : '',
            'region' => isset($data['region']) ? $data['region'] : '',
            'latitude' => $latitude,
            'longitude' => $longitude,
        );
    }

    /**
     * 获取国家名称
     */
    private static function get_country_name($code) {
        $countries = array(
            'CN' => '中国',
            'US' => '美国',
            'JP' => '日本',
            'KR' => '韩国',
            'GB' => '英国',
            'DE' => '德国',
            'FR' => '法国',
            'RU' => '俄罗斯',
            'CA' => '加拿大',
            'AU' => '澳大利亚',
            'IN' => '印度',
            'BR' => '巴西',
            'SG' => '新加坡',
            'HK' => '香港',
            'TW' => '台湾',
            'MY' => '马来西亚',
            'TH' => '泰国',
            'VN' => '越南',
            'ID' => '印度尼西亚',
            'PH' => '菲律宾',
            'NL' => '荷兰',
            'ES' => '西班牙',
            'IT' => '意大利',
            'SE' => '瑞典',
            'CH' => '瑞士',
            'PL' => '波兰',
            'UA' => '乌克兰',
            'TR' => '土耳其',
            'MX' => '墨西哥',
            'AR' => '阿根廷',
            'CL' => '智利',
            'CO' => '哥伦比亚',
            'ZA' => '南非',
            'AE' => '阿联酋',
            'SA' => '沙特阿拉伯',
            'IL' => '以色列',
            'NZ' => '新西兰',
        );

        return isset($countries[$code]) ? $countries[$code] : $code;
    }
}
