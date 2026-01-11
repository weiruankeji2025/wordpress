<?php
/**
 * 统计分析类
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_Stats {

    /**
     * 获取日期范围
     */
    private function get_date_range($range) {
        $end_date = current_time('Y-m-d');

        switch ($range) {
            case 'today':
                $start_date = $end_date;
                break;
            case 'yesterday':
                $start_date = date('Y-m-d', strtotime('-1 day'));
                $end_date = $start_date;
                break;
            case '7days':
                $start_date = date('Y-m-d', strtotime('-6 days'));
                break;
            case '30days':
                $start_date = date('Y-m-d', strtotime('-29 days'));
                break;
            case '90days':
                $start_date = date('Y-m-d', strtotime('-89 days'));
                break;
            case '365days':
                $start_date = date('Y-m-d', strtotime('-364 days'));
                break;
            case 'this_month':
                $start_date = date('Y-m-01');
                break;
            case 'last_month':
                $start_date = date('Y-m-01', strtotime('first day of last month'));
                $end_date = date('Y-m-t', strtotime('last day of last month'));
                break;
            case 'this_year':
                $start_date = date('Y-01-01');
                break;
            default:
                $start_date = date('Y-m-d', strtotime('-6 days'));
        }

        return array(
            'start' => $start_date . ' 00:00:00',
            'end' => $end_date . ' 23:59:59',
            'start_date' => $start_date,
            'end_date' => $end_date,
        );
    }

    /**
     * 获取上一周期日期范围(用于对比)
     */
    private function get_previous_range($range) {
        $current = $this->get_date_range($range);
        $days = (strtotime($current['end_date']) - strtotime($current['start_date'])) / 86400 + 1;

        $end_date = date('Y-m-d', strtotime($current['start_date'] . ' -1 day'));
        $start_date = date('Y-m-d', strtotime($end_date . " -{$days} days +1 day"));

        return array(
            'start' => $start_date . ' 00:00:00',
            'end' => $end_date . ' 23:59:59',
        );
    }

    /**
     * 获取仪表板统计数据
     */
    public function get_dashboard_stats($range = '7days') {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $prev_range = $this->get_previous_range($range);

        $visitors_table = JEA_Database::get_table('visitors');
        $pageviews_table = JEA_Database::get_table('pageviews');
        $sessions_table = JEA_Database::get_table('sessions');

        // 当前周期统计
        $current_stats = $this->get_period_stats($date_range);
        $previous_stats = $this->get_period_stats($prev_range);

        // 计算变化百分比
        $calc_change = function($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100 : 0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        // 获取热门页面
        $top_pages = $wpdb->get_results($wpdb->prepare(
            "SELECT page_url, page_title, COUNT(*) as views, COUNT(DISTINCT visitor_id) as visitors
             FROM $pageviews_table
             WHERE created_at BETWEEN %s AND %s
             GROUP BY page_url
             ORDER BY views DESC
             LIMIT 10",
            $date_range['start'],
            $date_range['end']
        ));

        // 获取流量来源
        $referrers = $wpdb->get_results($wpdb->prepare(
            "SELECT referrer_type,
                    CASE
                        WHEN referrer_type = 'direct' THEN '直接访问'
                        WHEN referrer_type = 'search' THEN '搜索引擎'
                        WHEN referrer_type = 'social' THEN '社交媒体'
                        WHEN referrer_type = 'referral' THEN '外部链接'
                        WHEN referrer_type = 'email' THEN '邮件'
                        ELSE '其他'
                    END as label,
                    COUNT(*) as sessions
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s
             GROUP BY referrer_type
             ORDER BY sessions DESC",
            $date_range['start'],
            $date_range['end']
        ));

        // 获取设备分布
        $devices = $wpdb->get_results($wpdb->prepare(
            "SELECT device_type,
                    CASE
                        WHEN device_type = 'desktop' THEN '桌面设备'
                        WHEN device_type = 'mobile' THEN '移动设备'
                        WHEN device_type = 'tablet' THEN '平板设备'
                        ELSE '其他'
                    END as label,
                    COUNT(*) as count
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s
             GROUP BY device_type
             ORDER BY count DESC",
            $date_range['start'],
            $date_range['end']
        ));

        // 获取浏览器分布
        $browsers = $wpdb->get_results($wpdb->prepare(
            "SELECT browser, COUNT(*) as count
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s
             GROUP BY browser
             ORDER BY count DESC
             LIMIT 10",
            $date_range['start'],
            $date_range['end']
        ));

        // 获取国家/地区分布
        $countries = $wpdb->get_results($wpdb->prepare(
            "SELECT country, country_code, COUNT(*) as count
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s AND country != ''
             GROUP BY country_code
             ORDER BY count DESC
             LIMIT 10",
            $date_range['start'],
            $date_range['end']
        ));

        // 获取操作系统分布
        $os_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT os, COUNT(*) as count
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s
             GROUP BY os
             ORDER BY count DESC
             LIMIT 10",
            $date_range['start'],
            $date_range['end']
        ));

        // 获取小时分布
        $hourly = $wpdb->get_results($wpdb->prepare(
            "SELECT HOUR(created_at) as hour, COUNT(*) as views
             FROM $pageviews_table
             WHERE created_at BETWEEN %s AND %s
             GROUP BY HOUR(created_at)
             ORDER BY hour",
            $date_range['start'],
            $date_range['end']
        ));

        // 填充24小时数据
        $hourly_data = array_fill(0, 24, 0);
        foreach ($hourly as $h) {
            $hourly_data[$h->hour] = (int)$h->views;
        }

        return array(
            'overview' => array(
                'visitors' => array(
                    'value' => $current_stats['visitors'],
                    'change' => $calc_change($current_stats['visitors'], $previous_stats['visitors']),
                    'previous' => $previous_stats['visitors'],
                ),
                'pageviews' => array(
                    'value' => $current_stats['pageviews'],
                    'change' => $calc_change($current_stats['pageviews'], $previous_stats['pageviews']),
                    'previous' => $previous_stats['pageviews'],
                ),
                'sessions' => array(
                    'value' => $current_stats['sessions'],
                    'change' => $calc_change($current_stats['sessions'], $previous_stats['sessions']),
                    'previous' => $previous_stats['sessions'],
                ),
                'bounce_rate' => array(
                    'value' => $current_stats['bounce_rate'],
                    'change' => $calc_change($current_stats['bounce_rate'], $previous_stats['bounce_rate']) * -1,
                    'previous' => $previous_stats['bounce_rate'],
                ),
                'avg_duration' => array(
                    'value' => $current_stats['avg_duration'],
                    'change' => $calc_change($current_stats['avg_duration'], $previous_stats['avg_duration']),
                    'previous' => $previous_stats['avg_duration'],
                ),
                'pages_per_session' => array(
                    'value' => $current_stats['pages_per_session'],
                    'change' => $calc_change($current_stats['pages_per_session'], $previous_stats['pages_per_session']),
                    'previous' => $previous_stats['pages_per_session'],
                ),
                'new_visitors' => array(
                    'value' => $current_stats['new_visitors'],
                    'change' => $calc_change($current_stats['new_visitors'], $previous_stats['new_visitors']),
                    'previous' => $previous_stats['new_visitors'],
                ),
                'returning_visitors' => array(
                    'value' => $current_stats['returning_visitors'],
                    'change' => $calc_change($current_stats['returning_visitors'], $previous_stats['returning_visitors']),
                    'previous' => $previous_stats['returning_visitors'],
                ),
            ),
            'top_pages' => $top_pages,
            'referrers' => $referrers,
            'devices' => $devices,
            'browsers' => $browsers,
            'countries' => $countries,
            'operating_systems' => $os_stats,
            'hourly' => $hourly_data,
            'date_range' => $date_range,
        );
    }

    /**
     * 获取周期统计
     */
    private function get_period_stats($date_range) {
        global $wpdb;

        $visitors_table = JEA_Database::get_table('visitors');
        $pageviews_table = JEA_Database::get_table('pageviews');
        $sessions_table = JEA_Database::get_table('sessions');

        // 访客数
        $visitors = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id)
             FROM $pageviews_table
             WHERE created_at BETWEEN %s AND %s",
            $date_range['start'],
            $date_range['end']
        ));

        // 页面浏览量
        $pageviews = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*)
             FROM $pageviews_table
             WHERE created_at BETWEEN %s AND %s",
            $date_range['start'],
            $date_range['end']
        ));

        // 会话数
        $sessions = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*)
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s",
            $date_range['start'],
            $date_range['end']
        ));

        // 跳出数
        $bounces = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*)
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s AND is_bounce = 1",
            $date_range['start'],
            $date_range['end']
        ));

        // 平均会话时长
        $avg_duration = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(duration)
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s AND duration > 0",
            $date_range['start'],
            $date_range['end']
        ));

        // 新访客
        $new_visitors = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*)
             FROM $visitors_table
             WHERE first_visit BETWEEN %s AND %s",
            $date_range['start'],
            $date_range['end']
        ));

        // 计算
        $sessions = (int)$sessions;
        $bounces = (int)$bounces;
        $visitors = (int)$visitors;

        $bounce_rate = $sessions > 0 ? round(($bounces / $sessions) * 100, 1) : 0;
        $pages_per_session = $sessions > 0 ? round($pageviews / $sessions, 1) : 0;
        $returning_visitors = max(0, $visitors - (int)$new_visitors);

        return array(
            'visitors' => $visitors,
            'pageviews' => (int)$pageviews,
            'sessions' => $sessions,
            'bounces' => $bounces,
            'bounce_rate' => $bounce_rate,
            'avg_duration' => (int)$avg_duration,
            'pages_per_session' => $pages_per_session,
            'new_visitors' => (int)$new_visitors,
            'returning_visitors' => $returning_visitors,
        );
    }

    /**
     * 获取图表数据
     */
    public function get_chart_data($type, $range) {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $pageviews_table = JEA_Database::get_table('pageviews');
        $sessions_table = JEA_Database::get_table('sessions');

        // 生成日期序列
        $start = new DateTime($date_range['start_date']);
        $end = new DateTime($date_range['end_date']);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end->modify('+1 day'));

        $labels = array();
        $data = array();

        foreach ($period as $date) {
            $labels[] = $date->format('m-d');
            $data[$date->format('Y-m-d')] = array(
                'visitors' => 0,
                'pageviews' => 0,
                'sessions' => 0,
            );
        }

        // 获取每日数据
        $daily_visitors = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(DISTINCT visitor_id) as count
             FROM $pageviews_table
             WHERE created_at BETWEEN %s AND %s
             GROUP BY DATE(created_at)",
            $date_range['start'],
            $date_range['end']
        ));

        $daily_pageviews = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count
             FROM $pageviews_table
             WHERE created_at BETWEEN %s AND %s
             GROUP BY DATE(created_at)",
            $date_range['start'],
            $date_range['end']
        ));

        $daily_sessions = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(start_time) as date, COUNT(*) as count
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s
             GROUP BY DATE(start_time)",
            $date_range['start'],
            $date_range['end']
        ));

        // 填充数据
        foreach ($daily_visitors as $row) {
            if (isset($data[$row->date])) {
                $data[$row->date]['visitors'] = (int)$row->count;
            }
        }

        foreach ($daily_pageviews as $row) {
            if (isset($data[$row->date])) {
                $data[$row->date]['pageviews'] = (int)$row->count;
            }
        }

        foreach ($daily_sessions as $row) {
            if (isset($data[$row->date])) {
                $data[$row->date]['sessions'] = (int)$row->count;
            }
        }

        // 构建数据集
        $datasets = array(
            'visitors' => array(),
            'pageviews' => array(),
            'sessions' => array(),
        );

        foreach ($data as $date => $values) {
            $datasets['visitors'][] = $values['visitors'];
            $datasets['pageviews'][] = $values['pageviews'];
            $datasets['sessions'][] = $values['sessions'];
        }

        return array(
            'labels' => $labels,
            'datasets' => $datasets,
        );
    }

    /**
     * 获取热门页面详情
     */
    public function get_top_pages($range, $limit = 50) {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $pageviews_table = JEA_Database::get_table('pageviews');

        return $wpdb->get_results($wpdb->prepare(
            "SELECT
                page_url,
                page_title,
                COUNT(*) as pageviews,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(entry_page) as entries,
                SUM(exit_page) as exits,
                AVG(time_on_page) as avg_time,
                AVG(scroll_depth) as avg_scroll
             FROM $pageviews_table
             WHERE created_at BETWEEN %s AND %s
             GROUP BY page_url
             ORDER BY pageviews DESC
             LIMIT %d",
            $date_range['start'],
            $date_range['end'],
            $limit
        ));
    }

    /**
     * 获取来源详情
     */
    public function get_referrers($range, $limit = 50) {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $sessions_table = JEA_Database::get_table('sessions');

        // 获取有来源域名的流量
        $external_referrers = $wpdb->get_results($wpdb->prepare(
            "SELECT
                referrer_domain,
                referrer_type,
                referrer,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(is_bounce) as bounces,
                AVG(duration) as avg_duration,
                AVG(pageviews) as avg_pages
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s AND referrer_domain != '' AND referrer_type != 'internal'
             GROUP BY referrer_domain
             ORDER BY sessions DESC
             LIMIT %d",
            $date_range['start'],
            $date_range['end'],
            $limit
        ));

        // 获取直接访问的统计
        $direct_stats = $wpdb->get_row($wpdb->prepare(
            "SELECT
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(is_bounce) as bounces,
                AVG(duration) as avg_duration,
                AVG(pageviews) as avg_pages
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s
               AND (referrer_domain = '' OR referrer_domain IS NULL OR referrer_type = 'direct')",
            $date_range['start'],
            $date_range['end']
        ));

        $results = array();

        // 添加直接访问
        if ($direct_stats && $direct_stats->sessions > 0) {
            $results[] = (object) array(
                'referrer_domain' => '',
                'referrer_type' => 'direct',
                'referrer' => '',
                'sessions' => (int)$direct_stats->sessions,
                'visitors' => (int)$direct_stats->visitors,
                'bounces' => (int)$direct_stats->bounces,
                'avg_duration' => (float)$direct_stats->avg_duration,
                'avg_pages' => (float)$direct_stats->avg_pages,
            );
        }

        // 添加外部来源
        foreach ($external_referrers as $ref) {
            $results[] = $ref;
        }

        // 按会话数排序
        usort($results, function($a, $b) {
            return $b->sessions - $a->sessions;
        });

        return array_slice($results, 0, $limit);
    }

    /**
     * 获取流量来源类型汇总
     */
    public function get_traffic_sources($range) {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $sessions_table = JEA_Database::get_table('sessions');

        // 使用更可靠的方式统计流量来源
        // 优先使用 UTM 参数，其次使用 referrer_type
        $type_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT
                CASE
                    WHEN utm_source != '' AND utm_source IS NOT NULL THEN
                        CASE
                            WHEN utm_medium IN ('cpc', 'ppc', 'paid', 'paidsearch') THEN 'search'
                            WHEN utm_medium IN ('social', 'social-media', 'sm') THEN 'social'
                            WHEN utm_medium IN ('email', 'newsletter', 'mail') THEN 'email'
                            WHEN utm_source IN ('google', 'bing', 'baidu', 'yahoo', 'yandex', 'duckduckgo', 'sogou') THEN 'search'
                            WHEN utm_source IN ('facebook', 'twitter', 'instagram', 'linkedin', 'weibo', 'wechat', 'tiktok') THEN 'social'
                            ELSE 'referral'
                        END
                    WHEN referrer_domain = '' OR referrer_domain IS NULL THEN 'direct'
                    WHEN referrer_type = 'internal' THEN 'direct'
                    ELSE COALESCE(referrer_type, 'referral')
                END as source_type,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(is_bounce) as bounces,
                AVG(duration) as avg_duration
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s
             GROUP BY source_type
             ORDER BY sessions DESC",
            $date_range['start'],
            $date_range['end']
        ));

        $result = array(
            'direct' => array('sessions' => 0, 'visitors' => 0, 'bounces' => 0, 'avg_duration' => 0),
            'search' => array('sessions' => 0, 'visitors' => 0, 'bounces' => 0, 'avg_duration' => 0),
            'social' => array('sessions' => 0, 'visitors' => 0, 'bounces' => 0, 'avg_duration' => 0),
            'referral' => array('sessions' => 0, 'visitors' => 0, 'bounces' => 0, 'avg_duration' => 0),
            'email' => array('sessions' => 0, 'visitors' => 0, 'bounces' => 0, 'avg_duration' => 0),
        );

        foreach ($type_stats as $stat) {
            $type = $stat->source_type;
            if (isset($result[$type])) {
                $result[$type] = array(
                    'sessions' => (int)$stat->sessions,
                    'visitors' => (int)$stat->visitors,
                    'bounces' => (int)$stat->bounces,
                    'avg_duration' => (float)$stat->avg_duration,
                );
            }
        }

        return $result;
    }

    /**
     * 获取搜索引擎详细列表
     */
    public function get_search_engines($range, $limit = 20) {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $sessions_table = JEA_Database::get_table('sessions');

        // 搜索引擎域名关键词列表
        $search_engine_keywords = array(
            'google', 'baidu', 'bing', 'sogou', 'so.com', 'yahoo', 'yandex',
            'duckduckgo', 'shenma', 'sm.cn', 'haosou', 'ask.com', 'aol',
            'ecosia', 'qwant', 'startpage', 'searx', 'gibiru', 'boardreader',
            'search.', 'youdao', 'chinaso', 'toutiao'
        );

        // 构建搜索引擎域名匹配条件
        $like_conditions = array();
        foreach ($search_engine_keywords as $keyword) {
            $like_conditions[] = $wpdb->prepare("referrer_domain LIKE %s", '%' . $keyword . '%');
        }
        $like_sql = implode(' OR ', $like_conditions);

        // 查询：优先使用 referrer_type='search'，同时也通过域名匹配
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT
                referrer_domain,
                referrer,
                COUNT(*) as count,
                COUNT(DISTINCT visitor_id) as visitors
             FROM $sessions_table
             WHERE start_time BETWEEN %s AND %s
               AND referrer_domain != ''
               AND (referrer_type = 'search' OR ($like_sql))
             GROUP BY referrer_domain
             ORDER BY count DESC
             LIMIT %d",
            $date_range['start'],
            $date_range['end'],
            $limit
        ));

        // 添加搜索引擎名称、图标、URL
        $search_engine_info = array(
            'google' => array('name' => 'Google', 'icon' => '🔍', 'url' => 'https://www.google.com'),
            'baidu' => array('name' => '百度', 'icon' => '🔎', 'url' => 'https://www.baidu.com'),
            'bing' => array('name' => 'Bing', 'icon' => '🔍', 'url' => 'https://www.bing.com'),
            'sogou' => array('name' => '搜狗', 'icon' => '🔎', 'url' => 'https://www.sogou.com'),
            'so.com' => array('name' => '360搜索', 'icon' => '🔎', 'url' => 'https://www.so.com'),
            'haosou' => array('name' => '360搜索', 'icon' => '🔎', 'url' => 'https://www.haosou.com'),
            'yahoo' => array('name' => 'Yahoo', 'icon' => '🔍', 'url' => 'https://search.yahoo.com'),
            'yandex' => array('name' => 'Yandex', 'icon' => '🔍', 'url' => 'https://www.yandex.com'),
            'duckduckgo' => array('name' => 'DuckDuckGo', 'icon' => '🦆', 'url' => 'https://duckduckgo.com'),
            'sm.cn' => array('name' => '神马搜索', 'icon' => '🔎', 'url' => 'https://m.sm.cn'),
            'shenma' => array('name' => '神马搜索', 'icon' => '🔎', 'url' => 'https://m.sm.cn'),
            'youdao' => array('name' => '有道', 'icon' => '🔎', 'url' => 'https://www.youdao.com'),
            'chinaso' => array('name' => '中国搜索', 'icon' => '🔎', 'url' => 'https://www.chinaso.com'),
            'toutiao' => array('name' => '头条搜索', 'icon' => '🔎', 'url' => 'https://so.toutiao.com'),
            'ask.com' => array('name' => 'Ask.com', 'icon' => '🔍', 'url' => 'https://www.ask.com'),
            'aol' => array('name' => 'AOL Search', 'icon' => '🔍', 'url' => 'https://search.aol.com'),
            'ecosia' => array('name' => 'Ecosia', 'icon' => '🌳', 'url' => 'https://www.ecosia.org'),
            'qwant' => array('name' => 'Qwant', 'icon' => '🔍', 'url' => 'https://www.qwant.com'),
        );

        foreach ($results as &$result) {
            $domain = strtolower($result->referrer_domain);
            $result->engine_name = $result->referrer_domain;
            $result->engine_icon = '🔍';
            $result->engine_url = 'https://' . $result->referrer_domain;

            foreach ($search_engine_info as $key => $info) {
                if (strpos($domain, $key) !== false) {
                    $result->engine_name = $info['name'];
                    $result->engine_icon = $info['icon'];
                    $result->engine_url = $info['url'];
                    break;
                }
            }
        }

        return $results;
    }

    /**
     * 获取设备品牌型号统计
     */
    public function get_device_brands($range, $limit = 20) {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $visitors_table = JEA_Database::get_table('visitors');
        $sessions_table = JEA_Database::get_table('sessions');

        return $wpdb->get_results($wpdb->prepare(
            "SELECT
                v.device_type,
                v.device_brand,
                v.device_model,
                v.os,
                v.os_version,
                COUNT(*) as count
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s
               AND v.device_type IN ('mobile', 'tablet')
             GROUP BY v.device_brand, v.device_model
             ORDER BY count DESC
             LIMIT %d",
            $date_range['start'],
            $date_range['end'],
            $limit
        ));
    }

    /**
     * 获取详细地理位置统计（国家-省-城市）
     */
    public function get_geo_stats($range) {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $visitors_table = JEA_Database::get_table('visitors');
        $sessions_table = JEA_Database::get_table('sessions');

        // 国家统计
        $countries = $wpdb->get_results($wpdb->prepare(
            "SELECT
                v.country,
                v.country_code,
                COUNT(*) as count
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s AND v.country != ''
             GROUP BY v.country_code
             ORDER BY count DESC
             LIMIT 20",
            $date_range['start'],
            $date_range['end']
        ));

        // 省/地区统计
        $regions = $wpdb->get_results($wpdb->prepare(
            "SELECT
                v.country,
                v.country_code,
                v.region,
                COUNT(*) as count
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s AND v.region != ''
             GROUP BY v.country_code, v.region
             ORDER BY count DESC
             LIMIT 20",
            $date_range['start'],
            $date_range['end']
        ));

        // 城市统计
        $cities = $wpdb->get_results($wpdb->prepare(
            "SELECT
                v.country,
                v.country_code,
                v.region,
                v.city,
                COUNT(*) as count
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s AND v.city != ''
             GROUP BY v.country_code, v.city
             ORDER BY count DESC
             LIMIT 20",
            $date_range['start'],
            $date_range['end']
        ));

        return array(
            'countries' => $countries,
            'regions' => $regions,
            'cities' => $cities,
        );
    }

    /**
     * 获取最近访客列表（含IP）
     */
    public function get_recent_visitors($range, $limit = 50) {
        global $wpdb;

        $date_range = $this->get_date_range($range);
        $visitors_table = JEA_Database::get_table('visitors');
        $sessions_table = JEA_Database::get_table('sessions');

        return $wpdb->get_results($wpdb->prepare(
            "SELECT
                v.id,
                v.ip_address,
                v.country,
                v.country_code,
                v.region,
                v.city,
                v.browser,
                v.browser_version,
                v.os,
                v.os_version,
                v.device_type,
                v.device_brand,
                v.device_model,
                v.screen_width,
                v.screen_height,
                v.first_visit,
                v.last_visit,
                v.visit_count,
                s.referrer_domain,
                s.referrer_type,
                s.entry_page,
                s.pageviews as session_pageviews,
                s.duration as session_duration
             FROM $visitors_table v
             INNER JOIN $sessions_table s ON v.id = s.visitor_id
             WHERE s.start_time BETWEEN %s AND %s
             ORDER BY s.start_time DESC
             LIMIT %d",
            $date_range['start'],
            $date_range['end'],
            $limit
        ));
    }
}
