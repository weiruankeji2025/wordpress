<?php
/**
 * 管理后台 - 流量来源模板
 */

if (!defined('ABSPATH')) {
    exit;
}

$stats = new JEA_Stats();
$range = isset($_GET['range']) ? sanitize_text_field($_GET['range']) : '7days';
$referrers = $stats->get_referrers($range, 100);
?>

<div class="jea-wrap">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">🔗</span>
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
                    <span>📥</span> <?php _e('导出', 'jeanalytics'); ?>
                </button>
                <div class="jea-export-menu">
                    <button class="jea-export-item" data-type="referrers" data-format="csv"><?php _e('导出为 CSV', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="referrers" data-format="json"><?php _e('导出为 JSON', 'jeanalytics'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 来源类型卡片 -->
    <div class="jea-stats-grid" style="margin-bottom: 24px;">
        <?php
        $type_totals = array(
            'direct' => array('label' => '直接访问', 'icon' => '🔗', 'count' => 0),
            'search' => array('label' => '搜索引擎', 'icon' => '🔍', 'count' => 0),
            'social' => array('label' => '社交媒体', 'icon' => '📱', 'count' => 0),
            'referral' => array('label' => '外部链接', 'icon' => '🌐', 'count' => 0),
            'email' => array('label' => '邮件营销', 'icon' => '📧', 'count' => 0),
        );

        foreach ($referrers as $ref) {
            $type = $ref->referrer_type;
            if (isset($type_totals[$type])) {
                $type_totals[$type]['count'] += intval($ref->sessions);
            }
        }

        foreach ($type_totals as $type => $data):
        ?>
        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php echo esc_html($data['label']); ?></span>
                <span class="jea-stat-icon" style="background: rgba(99, 102, 241, 0.1); font-size: 20px;"><?php echo $data['icon']; ?></span>
            </div>
            <div class="jea-stat-value"><?php echo number_format($data['count']); ?></div>
            <p class="jea-stat-compare"><?php _e('会话数', 'jeanalytics'); ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 来源列表 -->
    <div class="jea-card">
        <div class="jea-card-header">
            <h3 class="jea-card-title">📊 <?php _e('所有来源', 'jeanalytics'); ?></h3>
            <span class="jea-badge jea-badge-primary"><?php echo count($referrers); ?> <?php _e('个来源', 'jeanalytics'); ?></span>
        </div>
        <div class="jea-card-body">
            <?php if (empty($referrers)): ?>
                <div class="jea-empty">
                    <div class="jea-empty-icon">🔗</div>
                    <div class="jea-empty-title"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    <div class="jea-empty-text"><?php _e('还没有收集到流量来源数据', 'jeanalytics'); ?></div>
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
                                $type_icons = array(
                                    'direct' => '🔗',
                                    'search' => '🔍',
                                    'social' => '📱',
                                    'referral' => '🌐',
                                    'email' => '📧',
                                );
                                $bounce_rate = $ref->sessions > 0 ? round(($ref->bounces / $ref->sessions) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($ref->referrer_domain ?: '直接访问'); ?></strong>
                                </td>
                                <td>
                                    <span class="jea-badge jea-badge-primary">
                                        <?php echo $type_icons[$ref->referrer_type] ?? '🔗'; ?>
                                        <?php echo esc_html($type_labels[$ref->referrer_type] ?? $ref->referrer_type); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo number_format($ref->sessions); ?></strong></td>
                                <td><?php echo number_format($ref->visitors); ?></td>
                                <td>
                                    <span class="jea-badge <?php echo $bounce_rate > 70 ? 'jea-badge-danger' : ($bounce_rate > 50 ? 'jea-badge-warning' : 'jea-badge-success'); ?>">
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
