<?php
/**
 * 数据导出类
 */

if (!defined('ABSPATH')) {
    exit;
}

class JEA_Export {

    /**
     * 导出数据
     */
    public function export($type, $format, $range) {
        $stats = new JEA_Stats();

        switch ($type) {
            case 'overview':
                $data = $stats->get_dashboard_stats($range);
                break;
            case 'pages':
                $data = $stats->get_top_pages($range, 1000);
                break;
            case 'referrers':
                $data = $stats->get_referrers($range, 1000);
                break;
            case 'visitors':
                $data = $this->get_visitors_data($range);
                break;
            default:
                return false;
        }

        if (empty($data)) {
            return false;
        }

        switch ($format) {
            case 'csv':
                return $this->to_csv($data, $type);
            case 'json':
                return $this->to_json($data);
            default:
                return false;
        }
    }

    /**
     * 获取访客数据
     */
    private function get_visitors_data($range) {
        global $wpdb;

        $stats = new JEA_Stats();
        $method = new ReflectionMethod($stats, 'get_date_range');
        $method->setAccessible(true);
        $date_range = $method->invoke($stats, $range);

        $visitors_table = JEA_Database::get_table('visitors');
        $sessions_table = JEA_Database::get_table('sessions');

        return $wpdb->get_results($wpdb->prepare(
            "SELECT
                v.id,
                v.first_visit,
                v.last_visit,
                v.visit_count,
                v.country,
                v.city,
                v.browser,
                v.os,
                v.device_type,
                COUNT(s.id) as sessions
             FROM $visitors_table v
             LEFT JOIN $sessions_table s ON v.id = s.visitor_id
                AND s.start_time BETWEEN %s AND %s
             WHERE v.last_visit BETWEEN %s AND %s
             GROUP BY v.id
             ORDER BY v.last_visit DESC
             LIMIT 10000",
            $date_range['start'],
            $date_range['end'],
            $date_range['start'],
            $date_range['end']
        ), ARRAY_A);
    }

    /**
     * 转换为CSV
     */
    private function to_csv($data, $type) {
        if (empty($data)) {
            return false;
        }

        // 处理不同类型的数据
        if ($type === 'overview') {
            $rows = array();
            $rows[] = array('指标', '当前值', '变化百分比', '上期值');

            $labels = array(
                'visitors' => '访客数',
                'pageviews' => '页面浏览量',
                'sessions' => '会话数',
                'bounce_rate' => '跳出率(%)',
                'avg_duration' => '平均会话时长(秒)',
                'pages_per_session' => '每会话页面数',
                'new_visitors' => '新访客',
                'returning_visitors' => '回访访客',
            );

            foreach ($data['overview'] as $key => $value) {
                $rows[] = array(
                    $labels[$key] ?? $key,
                    $value['value'],
                    $value['change'] . '%',
                    $value['previous'],
                );
            }

            $data = $rows;
        } else {
            // 转换对象数组为数组
            if (isset($data[0]) && is_object($data[0])) {
                $data = array_map(function($item) {
                    return (array)$item;
                }, $data);
            }

            // 添加表头
            if (!empty($data)) {
                array_unshift($data, array_keys($data[0]));
            }
        }

        $output = fopen('php://temp', 'r+');

        // 写入BOM以支持Excel中文
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        // 生成文件名
        $filename = 'jeanalytics_' . $type . '_' . date('Y-m-d_His') . '.csv';

        // 保存到临时目录
        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/jeanalytics-exports';

        if (!file_exists($export_dir)) {
            wp_mkdir_p($export_dir);
        }

        $file_path = $export_dir . '/' . $filename;
        file_put_contents($file_path, $csv);

        return array(
            'filename' => $filename,
            'url' => $upload_dir['baseurl'] . '/jeanalytics-exports/' . $filename,
            'path' => $file_path,
        );
    }

    /**
     * 转换为JSON
     */
    private function to_json($data) {
        $filename = 'jeanalytics_export_' . date('Y-m-d_His') . '.json';

        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/jeanalytics-exports';

        if (!file_exists($export_dir)) {
            wp_mkdir_p($export_dir);
        }

        $file_path = $export_dir . '/' . $filename;
        file_put_contents($file_path, wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return array(
            'filename' => $filename,
            'url' => $upload_dir['baseurl'] . '/jeanalytics-exports/' . $filename,
            'path' => $file_path,
        );
    }

    /**
     * 清理导出文件
     */
    public static function cleanup_exports($days = 7) {
        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/jeanalytics-exports';

        if (!file_exists($export_dir)) {
            return;
        }

        $files = glob($export_dir . '/*');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $days * 86400) {
                    unlink($file);
                }
            }
        }
    }
}
