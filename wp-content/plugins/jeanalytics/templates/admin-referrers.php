<?php
/**
 * 管理后台 - 流量来源模板
 * 科技风格版本
 */

if (!defined('ABSPATH')) {
    exit;
}

$stats = new JEA_Stats();
$range = isset($_GET['range']) ? sanitize_text_field($_GET['range']) : '7days';
$referrers = $stats->get_referrers($range, 100);

// 计算各类型总数
$type_totals = array(
    'direct' => 0,
    'search' => 0,
    'social' => 0,
    'referral' => 0,
    'email' => 0,
);

foreach ($referrers as $ref) {
    $type = $ref->referrer_type;
    if (isset($type_totals[$type])) {
        $type_totals[$type] += intval($ref->sessions);
    }
}
?>

<div class="jea-wrap jea-tech">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                </span>
                <?php _e('流量来源', 'jeanalytics'); ?>
            </h1>
            <p class="jea-subtitle"><?php _e('分析访客从哪里来到您的网站', 'jeanalytics'); ?></p>
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
                    <button class="jea-export-item" data-type="referrers" data-format="csv"><?php _e('导出为 CSV', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="referrers" data-format="json"><?php _e('导出为 JSON', 'jeanalytics'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 来源类型卡片 -->
    <div class="jea-stats-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 24px;">
        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('直接访问', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                </span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($type_totals['direct']); ?></div>
            <span class="jea-stat-change neutral"><?php _e('会话数', 'jeanalytics'); ?></span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('搜索引擎', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($type_totals['search']); ?></div>
            <span class="jea-stat-change neutral"><?php _e('会话数', 'jeanalytics'); ?></span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('社交媒体', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                    </svg>
                </span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($type_totals['social']); ?></div>
            <span class="jea-stat-change neutral"><?php _e('会话数', 'jeanalytics'); ?></span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('外部链接', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                </span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($type_totals['referral']); ?></div>
            <span class="jea-stat-change neutral"><?php _e('会话数', 'jeanalytics'); ?></span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('邮件营销', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($type_totals['email']); ?></div>
            <span class="jea-stat-change neutral"><?php _e('会话数', 'jeanalytics'); ?></span>
        </div>
    </div>

    <!-- 来源列表 -->
    <div class="jea-card">
        <div class="jea-card-header">
            <h3 class="jea-card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                <?php _e('所有来源', 'jeanalytics'); ?>
            </h3>
            <span class="jea-badge jea-badge-primary"><?php echo count($referrers); ?> <?php _e('个来源', 'jeanalytics'); ?></span>
        </div>
        <div class="jea-card-body">
            <?php if (empty($referrers)): ?>
                <div class="jea-empty">
                    <div class="jea-empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                    </div>
                    <div class="jea-empty-title"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                </div>
            <?php else: ?>
                <div class="jea-table-wrapper">
                    <table class="jea-table">
                        <thead>
                            <tr>
                                <th><?php _e('来源', 'jeanalytics'); ?></th>
                                <th><?php _e('类型', 'jeanalytics'); ?></th>
                                <th><?php _e('会话', 'jeanalytics'); ?></th>
                                <th><?php _e('访客', 'jeanalytics'); ?></th>
                                <th><?php _e('跳出率', 'jeanalytics'); ?></th>
                                <th><?php _e('平均时长', 'jeanalytics'); ?></th>
                                <th><?php _e('页面/会话', 'jeanalytics'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($referrers as $ref):
                                $type_labels = array(
                                    'direct' => '直接访问',
                                    'search' => '搜索引擎',
                                    'social' => '社交媒体',
                                    'referral' => '外部链接',
                                    'email' => '邮件',
                                );
                                $bounce_rate = $ref->sessions > 0 ? round(($ref->bounces / $ref->sessions) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($ref->referrer_domain ?: '直接访问'); ?></strong>
                                </td>
                                <td>
                                    <span class="jea-type-badge">
                                        <?php echo esc_html($type_labels[$ref->referrer_type] ?? $ref->referrer_type); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo number_format($ref->sessions); ?></strong></td>
                                <td><?php echo number_format($ref->visitors); ?></td>
                                <td>
                                    <span class="jea-rate-badge <?php echo $bounce_rate > 70 ? 'high' : ($bounce_rate > 50 ? 'medium' : 'low'); ?>">
                                        <?php echo $bounce_rate; ?>%
                                    </span>
                                </td>
                                <td><?php echo JEA_Dashboard::format_duration(intval($ref->avg_duration)); ?></td>
                                <td><?php echo number_format($ref->avg_pages, 1); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* 流量来源页面特定样式 */
.jea-tech .jea-type-badge {
    display: inline-block;
    padding: 4px 10px;
    background: rgba(59, 130, 246, 0.15);
    color: var(--jea-primary);
    font-size: 12px;
    border-radius: 4px;
}

.jea-tech .jea-rate-badge {
    display: inline-block;
    padding: 3px 8px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 4px;
}

.jea-tech .jea-rate-badge.low {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.jea-tech .jea-rate-badge.medium {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
}

.jea-tech .jea-rate-badge.high {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

@media (max-width: 1200px) {
    .jea-tech .jea-stats-grid[style*="grid-template-columns: repeat(5"] {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .jea-tech .jea-stats-grid[style*="grid-template-columns: repeat(5"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 500px) {
    .jea-tech .jea-stats-grid[style*="grid-template-columns: repeat(5"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // 日期范围切换
    $('.jea-date-range button').on('click', function() {
        const range = $(this).data('range');
        window.location.href = '<?php echo admin_url('admin.php?page=jeanalytics-referrers&range='); ?>' + range;
    });
});
</script>
