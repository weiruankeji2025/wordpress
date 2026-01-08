<?php
/**
 * 管理后台 - 仪表板模板
 * 科技风格版本
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jea-wrap jea-tech">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg></span>
                <?php _e('威软访客', 'jeanalytics'); ?>
            </h1>
            <p class="jea-subtitle"><?php _e('网站流量分析系统', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-controls">
            <div class="jea-date-range">
                <button data-range="today"><?php _e('今天', 'jeanalytics'); ?></button>
                <button data-range="yesterday"><?php _e('昨天', 'jeanalytics'); ?></button>
                <button data-range="7days" class="active"><?php _e('7天', 'jeanalytics'); ?></button>
                <button data-range="30days"><?php _e('30天', 'jeanalytics'); ?></button>
                <button data-range="90days"><?php _e('90天', 'jeanalytics'); ?></button>
            </div>

            <div class="jea-export-dropdown">
                <button class="jea-btn jea-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <?php _e('导出', 'jeanalytics'); ?>
                </button>
                <div class="jea-export-menu">
                    <button class="jea-export-item" data-type="overview" data-format="csv"><?php _e('导出概览 (CSV)', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="pages" data-format="csv"><?php _e('导出页面数据 (CSV)', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="visitors" data-format="csv"><?php _e('导出访客数据 (CSV)', 'jeanalytics'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 核心指标卡片 -->
    <div class="jea-stats-grid">
        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('访客数', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            </div>
            <div class="jea-stat-value" id="stat-visitors">0</div>
            <span class="jea-stat-change neutral" id="stat-visitors-change">- 0%</span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('页面浏览量', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
            </div>
            <div class="jea-stat-value" id="stat-pageviews">0</div>
            <span class="jea-stat-change neutral" id="stat-pageviews-change">- 0%</span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('会话数', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg></span>
            </div>
            <div class="jea-stat-value" id="stat-sessions">0</div>
            <span class="jea-stat-change neutral" id="stat-sessions-change">- 0%</span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('跳出率', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,10 4,15 9,20"/><path d="M20 4v7a4 4 0 0 1-4 4H4"/></svg></span>
            </div>
            <div class="jea-stat-value" id="stat-bounce-rate">0%</div>
            <span class="jea-stat-change neutral" id="stat-bounce-change">- 0%</span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('平均访问时长', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></span>
            </div>
            <div class="jea-stat-value" id="stat-avg-duration">0秒</div>
            <span class="jea-stat-change neutral" id="stat-duration-change">- 0%</span>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('页面/会话', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
            </div>
            <div class="jea-stat-value" id="stat-pages-per-session">0</div>
            <span class="jea-stat-change neutral" id="stat-pages-change">- 0%</span>
        </div>
    </div>

    <!-- 图表区域 -->
    <div class="jea-charts-grid">
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <?php _e('访问趋势', 'jeanalytics'); ?>
                </h3>
                <div class="jea-chart-legend">
                    <span class="jea-legend-item"><span class="jea-legend-dot" style="background: #3b82f6;"></span><?php _e('访客', 'jeanalytics'); ?></span>
                    <span class="jea-legend-item"><span class="jea-legend-dot" style="background: #10b981;"></span><?php _e('浏览量', 'jeanalytics'); ?></span>
                </div>
            </div>
            <div class="jea-card-body">
                <div class="jea-chart-container"><canvas id="mainChart"></canvas></div>
            </div>
        </div>

        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    <?php _e('设备分布', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body" id="devices-chart">
                <div class="jea-chart-container" style="height: 200px;"><canvas id="devicesChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- 数据列表区域 -->
    <div class="jea-grid jea-grid-3">
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13,2 13,9 20,9"/></svg>
                    <?php _e('热门页面', 'jeanalytics'); ?>
                </h3>
                <a href="<?php echo admin_url('admin.php?page=jeanalytics-pages'); ?>" class="jea-link"><?php _e('全部', 'jeanalytics'); ?> &rarr;</a>
            </div>
            <div class="jea-card-body" id="top-pages-list">
                <div class="jea-loading"><div class="jea-spinner"></div></div>
            </div>
        </div>

        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <?php _e('搜索引擎', 'jeanalytics'); ?>
                </h3>
                <a href="<?php echo admin_url('admin.php?page=jeanalytics-referrers'); ?>" class="jea-link"><?php _e('全部', 'jeanalytics'); ?> &rarr;</a>
            </div>
            <div class="jea-card-body" id="search-engines-list">
                <div class="jea-loading"><div class="jea-spinner"></div></div>
            </div>
        </div>

        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <?php _e('地区分布', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body" id="geo-stats-list">
                <div class="jea-loading"><div class="jea-spinner"></div></div>
            </div>
        </div>
    </div>

    <!-- 设备和时段 -->
    <div class="jea-grid jea-grid-2" style="margin-top: 20px;">
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    <?php _e('移动设备', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body" id="device-brands-list">
                <div class="jea-loading"><div class="jea-spinner"></div></div>
            </div>
        </div>

        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                    <?php _e('访问时段', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body" id="hourly-chart">
                <div class="jea-loading"><div class="jea-spinner"></div></div>
            </div>
        </div>
    </div>

    <!-- 访客列表 -->
    <div class="jea-card" style="margin-top: 20px;">
        <div class="jea-card-header">
            <h3 class="jea-card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <?php _e('最近访客', 'jeanalytics'); ?>
            </h3>
            <a href="<?php echo admin_url('admin.php?page=jeanalytics-visitors'); ?>" class="jea-link"><?php _e('全部', 'jeanalytics'); ?> &rarr;</a>
        </div>
        <div class="jea-card-body" id="recent-visitors-list">
            <div class="jea-loading"><div class="jea-spinner"></div></div>
        </div>
    </div>
</div>
