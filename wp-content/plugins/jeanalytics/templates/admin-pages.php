<?php
/**
 * 管理后台 - 页面统计模板
 */

if (!defined('ABSPATH')) {
    exit;
}

$stats = new JEA_Stats();
$range = isset($_GET['range']) ? sanitize_text_field($_GET['range']) : '7days';
$pages = $stats->get_top_pages($range, 100);
?>

<div class="jea-wrap">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">📄</span>
                <?php _e('页面统计', 'jeanalytics'); ?>
            </h1>
            <p class="jea-subtitle"><?php _e('分析每个页面的访问情况和用户行为', 'jeanalytics'); ?></p>
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
                    <button class="jea-export-item" data-type="pages" data-format="csv"><?php _e('导出为 CSV', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="pages" data-format="json"><?php _e('导出为 JSON', 'jeanalytics'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 页面列表 -->
    <div class="jea-card">
        <div class="jea-card-header">
            <h3 class="jea-card-title">📊 <?php _e('所有页面', 'jeanalytics'); ?></h3>
            <span class="jea-badge jea-badge-primary"><?php echo count($pages); ?> <?php _e('个页面', 'jeanalytics'); ?></span>
        </div>
        <div class="jea-card-body">
            <?php if (empty($pages)): ?>
                <div class="jea-empty">
                    <div class="jea-empty-icon">📄</div>
                    <div class="jea-empty-title"><?php _e('暂无数据', 'jeanalytics'); ?></div>
                    <div class="jea-empty-text"><?php _e('还没有收集到页面访问数据', 'jeanalytics'); ?></div>
                </div>
            <?php else: ?>
                <div class="jea-table-wrapper">
                    <table class="jea-table">
                        <thead>
                            <tr>
                                <th><?php _e('页面', 'jeanalytics'); ?></th>
                                <th><?php _e('浏览量', 'jeanalytics'); ?></th>
                                <th><?php _e('访客数', 'jeanalytics'); ?></th>
                                <th><?php _e('入口', 'jeanalytics'); ?></th>
                                <th><?php _e('出口', 'jeanalytics'); ?></th>
                                <th><?php _e('平均时长', 'jeanalytics'); ?></th>
                                <th><?php _e('平均滚动', 'jeanalytics'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pages as $page):
                                $path = str_replace(home_url(), '', $page->page_url) ?: '/';
                                $title = $page->page_title ?: $path;
                            ?>
                            <tr>
                                <td>
                                    <div class="jea-page-url">
                                        <a href="<?php echo esc_url($page->page_url); ?>" target="_blank" title="<?php echo esc_attr($title); ?>">
                                            <?php echo esc_html(mb_strlen($title) > 50 ? mb_substr($title, 0, 50) . '...' : $title); ?>
                                        </a>
                                    </div>
                                </td>
                                <td><strong><?php echo number_format($page->pageviews); ?></strong></td>
                                <td><?php echo number_format($page->visitors); ?></td>
                                <td><?php echo number_format($page->entries); ?></td>
                                <td><?php echo number_format($page->exits); ?></td>
                                <td><?php echo JEA_Dashboard::format_duration(intval($page->avg_time)); ?></td>
                                <td>
                                    <div class="jea-progress-bar" style="width: 80px; display: inline-block;">
                                        <div class="jea-progress-fill primary" style="width: <?php echo intval($page->avg_scroll); ?>%;"></div>
                                    </div>
                                    <?php echo intval($page->avg_scroll); ?>%
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
