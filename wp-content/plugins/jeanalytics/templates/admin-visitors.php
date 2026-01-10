<?php
/**
 * 管理后台 - 访客分析模板
 * 科技风格版本
 */

if (!defined('ABSPATH')) {
    exit;
}

$stats = new JEA_Stats();
$range = isset($_GET['range']) ? sanitize_text_field($_GET['range']) : '7days';
$dashboard_data = $stats->get_dashboard_stats($range);
?>

<div class="jea-wrap jea-tech">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <?php _e('导出', 'jeanalytics'); ?>
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
        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('总访客', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($dashboard_data['overview']['visitors']['value']); ?></div>
            <span class="jea-stat-change neutral"><?php _e('独立访客', 'jeanalytics'); ?></span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('新访客', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($dashboard_data['overview']['new_visitors']['value']); ?></div>
            <span class="jea-stat-change neutral"><?php _e('首次访问', 'jeanalytics'); ?></span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('回访访客', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23,4 23,10 17,10"/>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                    </svg>
                </span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($dashboard_data['overview']['returning_visitors']['value']); ?></div>
            <span class="jea-stat-change neutral"><?php _e('再次访问', 'jeanalytics'); ?></span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('新访客比例', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"/>
                    </svg>
                </span>
            </div>
            <?php
            $total_visitors = $dashboard_data['overview']['visitors']['value'];
            $new_visitors = $dashboard_data['overview']['new_visitors']['value'];
            $new_ratio = $total_visitors > 0 ? round(($new_visitors / $total_visitors) * 100, 1) : 0;
            ?>
            <div class="jea-stat-value"><?php echo $new_ratio; ?>%</div>
            <span class="jea-stat-change neutral"><?php _e('占比', 'jeanalytics'); ?></span>
        </div>
    </div>

    <div class="jea-grid jea-grid-2">
        <!-- 设备分布 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                        <line x1="12" y1="18" x2="12.01" y2="18"/>
                    </svg>
                    <?php _e('设备类型', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body">
                <?php if (empty($dashboard_data['devices'])): ?>
                    <div class="jea-empty">
                        <div class="jea-empty-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                                <line x1="12" y1="18" x2="12.01" y2="18"/>
                            </svg>
                        </div>
                        <div class="jea-empty-text"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    </div>
                <?php else:
                    $total_devices = array_sum(array_column($dashboard_data['devices'], 'count'));
                    foreach ($dashboard_data['devices'] as $device):
                        $percent = $total_devices > 0 ? round(($device->count / $total_devices) * 100, 1) : 0;
                ?>
                    <div class="jea-progress-item">
                        <div class="jea-progress-header">
                            <span class="jea-progress-label">
                                <?php if ($device->device_type === 'desktop'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                        <line x1="8" y1="21" x2="16" y2="21"/>
                                        <line x1="12" y1="17" x2="12" y2="21"/>
                                    </svg>
                                <?php elseif ($device->device_type === 'mobile'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                                        <line x1="12" y1="18" x2="12.01" y2="18"/>
                                    </svg>
                                <?php else: ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="4" y="4" width="16" height="16" rx="2" ry="2"/>
                                        <rect x="9" y="9" width="6" height="6"/>
                                        <line x1="9" y1="2" x2="9" y2="4"/>
                                        <line x1="15" y1="2" x2="15" y2="4"/>
                                        <line x1="9" y1="20" x2="9" y2="22"/>
                                        <line x1="15" y1="20" x2="15" y2="22"/>
                                    </svg>
                                <?php endif; ?>
                                <?php echo esc_html($device->label); ?>
                            </span>
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
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="4"/>
                        <line x1="21.17" y1="8" x2="12" y2="8"/>
                        <line x1="3.95" y1="6.06" x2="8.54" y2="14"/>
                        <line x1="10.88" y1="21.94" x2="15.46" y2="14"/>
                    </svg>
                    <?php _e('浏览器', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body">
                <?php if (empty($dashboard_data['browsers'])): ?>
                    <div class="jea-empty">
                        <div class="jea-empty-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <circle cx="12" cy="12" r="10"/>
                                <circle cx="12" cy="12" r="4"/>
                            </svg>
                        </div>
                        <div class="jea-empty-text"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    </div>
                <?php else:
                    $total_browsers = array_sum(array_column($dashboard_data['browsers'], 'count'));
                    foreach (array_slice($dashboard_data['browsers'], 0, 6) as $browser):
                        $percent = $total_browsers > 0 ? round(($browser->count / $total_browsers) * 100, 1) : 0;
                ?>
                    <div class="jea-progress-item">
                        <div class="jea-progress-header">
                            <span class="jea-progress-label">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                </svg>
                                <?php echo esc_html($browser->browser); ?>
                            </span>
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
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                    <?php _e('操作系统', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body">
                <?php if (empty($dashboard_data['operating_systems'])): ?>
                    <div class="jea-empty">
                        <div class="jea-empty-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                <line x1="8" y1="21" x2="16" y2="21"/>
                                <line x1="12" y1="17" x2="12" y2="21"/>
                            </svg>
                        </div>
                        <div class="jea-empty-text"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    </div>
                <?php else:
                    $total_os = array_sum(array_column($dashboard_data['operating_systems'], 'count'));
                    foreach (array_slice($dashboard_data['operating_systems'], 0, 6) as $os):
                        $percent = $total_os > 0 ? round(($os->count / $total_os) * 100, 1) : 0;
                ?>
                    <div class="jea-progress-item">
                        <div class="jea-progress-header">
                            <span class="jea-progress-label">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                </svg>
                                <?php echo esc_html($os->os); ?>
                            </span>
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
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    <?php _e('国家/地区', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body">
                <?php if (empty($dashboard_data['countries'])): ?>
                    <div class="jea-empty">
                        <div class="jea-empty-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="2" y1="12" x2="22" y2="12"/>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                        </div>
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

<style>
/* 访客分析页面特定样式 */
.jea-tech .jea-progress-label svg {
    vertical-align: middle;
    margin-right: 6px;
}

.jea-tech .jea-country-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.jea-tech .jea-country-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    background: rgba(59, 130, 246, 0.05);
    border-radius: 6px;
}

.jea-tech .jea-country-flag {
    font-size: 18px;
    margin-right: 10px;
}

.jea-tech .jea-country-name {
    flex: 1;
    color: var(--jea-text);
}

.jea-tech .jea-country-count {
    font-weight: 600;
    color: var(--jea-primary);
}
</style>

<script>
jQuery(document).ready(function($) {
    // 日期范围切换
    $('.jea-date-range button').on('click', function() {
        const range = $(this).data('range');
        window.location.href = '<?php echo admin_url('admin.php?page=jeanalytics-visitors&range='); ?>' + range;
    });
});
</script>
