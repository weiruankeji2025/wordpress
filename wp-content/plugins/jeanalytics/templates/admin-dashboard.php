<?php
/**
 * 管理后台 - 仪表板模板
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jea-wrap">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">📊</span>
                <?php _e('流量分析仪表板', 'jeanalytics'); ?>
            </h1>
            <p class="jea-subtitle"><?php _e('实时监控您网站的访问数据和用户行为', 'jeanalytics'); ?></p>
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
                    <span>📥</span> <?php _e('导出', 'jeanalytics'); ?>
                </button>
                <div class="jea-export-menu">
                    <button class="jea-export-item" data-type="overview" data-format="csv"><?php _e('导出概览 (CSV)', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="pages" data-format="csv"><?php _e('导出页面数据 (CSV)', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="visitors" data-format="csv"><?php _e('导出访客数据 (CSV)', 'jeanalytics'); ?></button>
                    <button class="jea-export-item" data-type="overview" data-format="json"><?php _e('导出全部 (JSON)', 'jeanalytics'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 核心指标卡片 -->
    <div class="jea-stats-grid">
        <div class="jea-stat-card visitors">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('访客数', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon visitors">👥</span>
            </div>
            <div class="jea-stat-value" id="stat-visitors">0</div>
            <span class="jea-stat-change neutral" id="stat-visitors-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card pageviews">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('页面浏览量', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon pageviews">📄</span>
            </div>
            <div class="jea-stat-value" id="stat-pageviews">0</div>
            <span class="jea-stat-change neutral" id="stat-pageviews-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card sessions">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('会话数', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon sessions">🔄</span>
            </div>
            <div class="jea-stat-value" id="stat-sessions">0</div>
            <span class="jea-stat-change neutral" id="stat-sessions-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card bounce">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('跳出率', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon bounce">↩️</span>
            </div>
            <div class="jea-stat-value" id="stat-bounce-rate">0%</div>
            <span class="jea-stat-change neutral" id="stat-bounce-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('平均访问时长', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">⏱️</span>
            </div>
            <div class="jea-stat-value" id="stat-avg-duration">0秒</div>
            <span class="jea-stat-change neutral" id="stat-duration-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card">
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('每次会话页面数', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">📑</span>
            </div>
            <div class="jea-stat-value" id="stat-pages-per-session">0</div>
            <span class="jea-stat-change neutral" id="stat-pages-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>
    </div>

    <!-- 图表区域 -->
    <div class="jea-charts-grid">
        <!-- 主图表 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">📈 <?php _e('访问趋势', 'jeanalytics'); ?></h3>
                <div class="jea-chart-legend">
                    <span class="jea-legend-item">
                        <span class="jea-legend-dot" style="background: #6366f1;"></span>
                        <?php _e('访客', 'jeanalytics'); ?>
                    </span>
                    <span class="jea-legend-item">
                        <span class="jea-legend-dot" style="background: #0ea5e9;"></span>
                        <?php _e('浏览量', 'jeanalytics'); ?>
                    </span>
                </div>
            </div>
            <div class="jea-card-body">
                <div class="jea-chart-container">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 设备分布 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">📱 <?php _e('设备分布', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="devices-chart">
                <div class="jea-chart-container" style="height: 200px;">
                    <canvas id="devicesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 详细数据区域 -->
    <div class="jea-grid jea-grid-3">
        <!-- 热门页面 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🔥 <?php _e('热门页面', 'jeanalytics'); ?></h3>
                <a href="<?php echo admin_url('admin.php?page=jeanalytics-pages'); ?>" class="jea-btn jea-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                    <?php _e('查看全部', 'jeanalytics'); ?>
                </a>
            </div>
            <div class="jea-card-body" id="top-pages-list">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>

        <!-- 流量来源 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🔗 <?php _e('流量来源', 'jeanalytics'); ?></h3>
                <a href="<?php echo admin_url('admin.php?page=jeanalytics-referrers'); ?>" class="jea-btn jea-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                    <?php _e('查看全部', 'jeanalytics'); ?>
                </a>
            </div>
            <div class="jea-card-body" id="referrers-list">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>

        <!-- 国家/地区 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🌍 <?php _e('访客来源地区', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="countries-list">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 浏览器和小时分布 -->
    <div class="jea-grid jea-grid-2" style="margin-top: 20px;">
        <!-- 浏览器分布 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🌐 <?php _e('浏览器分布', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="browsers-list">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>

        <!-- 访问时段分布 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🕐 <?php _e('访问时段分布', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="hourly-chart">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>
    </div>

</div>
