<?php
/**
 * 管理后台 - 访客分析模板
 */

if (!defined('ABSPATH')) {
    exit;
}

$stats = new JEA_Stats();
$range = isset($_GET['range']) ? sanitize_text_field($_GET['range']) : '7days';
$dashboard_data = $stats->get_dashboard_stats($range);
?>

<div class="jea-wrap">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">👥</span>
                <?php _e('访客分析', 'jeanalytics'); ?>
            </h1>
            <p class="jea-subtitle"><?php _e('深入了解您的访客特征和行为', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-controls">
            <div class="jea-date-range">
                <button data-range="today" <?php echo $range === 'today' ? 'class="active"' : ''; ?>><?php _e('今天', 'jeanalytics'); ?></button>
                <button data-range="7days" <?php echo $range === '7days' ? 'class="active"' : ''; ?>><?php _e('7天', 'jeanalytics'); ?></button>
                <button data-range="30days" <?php echo $range === '30days' ? 'class="active"' : ''; ?>><?php _e('30天', 'jeanalytics'); ?></button>
                <button data-range="90days" <?php echo $range === '90days' ? 'class="active"' : ''; ?>><?php _e('90天', 'jeanalytics'); ?></button>
            </div>

            <div class="jea-export-dropdown">
                <button class="jea-btn jea-btn-secondary">
                    <span>📥</span> <?php _e('导出', 'jeanalytics'); ?>
                </button>
                <div class="jea-export-menu">
                    <button class="jea-export-item" data-type="visitors" data-format="csv"><?php _e('导出为 CSV', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="visitors" data-format="json"><?php _e('导出为 JSON', 'jeanalytics'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 访客概览卡片 -->
    <div class="jea-stats-grid" style="margin-bottom: 24px;">
        <div class="jea-stat-card visitors">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('总访客', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon visitors">👥</span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($dashboard_data['overview']['visitors']['value']); ?></div>
        </div>

        <div class="jea-stat-card sessions">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('新访客', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon sessions">✨</span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($dashboard_data['overview']['new_visitors']['value']); ?></div>
        </div>

        <div class="jea-stat-card pageviews">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('回访访客', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon pageviews">🔄</span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($dashboard_data['overview']['returning_visitors']['value']); ?></div>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('新访客比例', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">📊</span>
            </div>
            <?php
            $total_visitors = $dashboard_data['overview']['visitors']['value'];
            $new_visitors = $dashboard_data['overview']['new_visitors']['value'];
            $new_ratio = $total_visitors > 0 ? round(($new_visitors / $total_visitors) * 100, 1) : 0;
            ?>
            <div class="jea-stat-value"><?php echo $new_ratio; ?>%</div>
        </div>
    </div>

    <div class="jea-grid jea-grid-2">
        <!-- 设备分布 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">📱 <?php _e('设备类型', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body">
                <?php if (empty($dashboard_data['devices'])): ?>
                    <div class="jea-empty">
                        <div class="jea-empty-icon">📱</div>
                        <div class="jea-empty-text"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    </div>
                <?php else:
                    $total_devices = array_sum(array_column($dashboard_data['devices'], 'count'));
                    foreach ($dashboard_data['devices'] as $device):
                        $percent = $total_devices > 0 ? round(($device->count / $total_devices) * 100, 1) : 0;
                        $icon = array('desktop' => '🖥️', 'mobile' => '📱', 'tablet' => '📱')[$device->device_type] ?? '📱';
                ?>
                    <div class="jea-progress-item">
                        <div class="jea-progress-header">
                            <span class="jea-progress-label"><?php echo $icon; ?> <?php echo esc_html($device->label); ?></span>
                            <span class="jea-progress-value"><?php echo number_format($device->count); ?> (<?php echo $percent; ?>%)</span>
                        </div>
                        <div class="jea-progress-bar">
                            <div class="jea-progress-fill primary" style="width: <?php echo $percent; ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- 浏览器分布 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🌐 <?php _e('浏览器', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body">
                <?php if (empty($dashboard_data['browsers'])): ?>
                    <div class="jea-empty">
                        <div class="jea-empty-icon">🌐</div>
                        <div class="jea-empty-text"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    </div>
                <?php else:
                    $total_browsers = array_sum(array_column($dashboard_data['browsers'], 'count'));
                    foreach (array_slice($dashboard_data['browsers'], 0, 6) as $browser):
                        $percent = $total_browsers > 0 ? round(($browser->count / $total_browsers) * 100, 1) : 0;
                        $icon = JEA_Dashboard::get_browser_icon($browser->browser);
                ?>
                    <div class="jea-progress-item">
                        <div class="jea-progress-header">
                            <span class="jea-progress-label"><?php echo $icon; ?> <?php echo esc_html($browser->browser); ?></span>
                            <span class="jea-progress-value"><?php echo number_format($browser->count); ?> (<?php echo $percent; ?>%)</span>
                        </div>
                        <div class="jea-progress-bar">
                            <div class="jea-progress-fill secondary" style="width: <?php echo $percent; ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <div class="jea-grid jea-grid-2" style="margin-top: 20px;">
        <!-- 操作系统 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">💻 <?php _e('操作系统', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body">
                <?php if (empty($dashboard_data['operating_systems'])): ?>
                    <div class="jea-empty">
                        <div class="jea-empty-icon">💻</div>
                        <div class="jea-empty-text"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    </div>
                <?php else:
                    $total_os = array_sum(array_column($dashboard_data['operating_systems'], 'count'));
                    foreach (array_slice($dashboard_data['operating_systems'], 0, 6) as $os):
                        $percent = $total_os > 0 ? round(($os->count / $total_os) * 100, 1) : 0;
                        $icon = '🖥️';
                        if (stripos($os->os, 'windows') !== false) $icon = '🪟';
                        elseif (stripos($os->os, 'mac') !== false) $icon = '🍎';
                        elseif (stripos($os->os, 'linux') !== false) $icon = '🐧';
                        elseif (stripos($os->os, 'android') !== false) $icon = '🤖';
                        elseif (stripos($os->os, 'ios') !== false) $icon = '📱';
                ?>
                    <div class="jea-progress-item">
                        <div class="jea-progress-header">
                            <span class="jea-progress-label"><?php echo $icon; ?> <?php echo esc_html($os->os); ?></span>
                            <span class="jea-progress-value"><?php echo number_format($os->count); ?> (<?php echo $percent; ?>%)</span>
                        </div>
                        <div class="jea-progress-bar">
                            <div class="jea-progress-fill success" style="width: <?php echo $percent; ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- 国家/地区 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🌍 <?php _e('国家/地区', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body">
                <?php if (empty($dashboard_data['countries'])): ?>
                    <div class="jea-empty">
                        <div class="jea-empty-icon">🌍</div>
                        <div class="jea-empty-text"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    </div>
                <?php else: ?>
                    <div class="jea-country-list">
                        <?php foreach ($dashboard_data['countries'] as $country):
                            $flag = JEA_Dashboard::get_country_flag($country->country_code);
                        ?>
                        <div class="jea-country-item">
                            <span class="jea-country-flag"><?php echo $flag; ?></span>
                            <span class="jea-country-name"><?php echo esc_html($country->country ?: 'Unknown'); ?></span>
                            <span class="jea-country-count"><?php echo number_format($country->count); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
