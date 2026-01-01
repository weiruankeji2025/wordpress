<?php
/**
 * 管理后台 - 仪表板模板
 * 3D立体视觉效果版本
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
                <?php _e('威软访客 - 流量分析仪表板', 'jeanalytics'); ?>
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
                </div>
            </div>
        </div>
    </div>

    <!-- 3D核心指标卡片 -->
    <div class="jea-stats-grid jea-3d-cards">
        <div class="jea-stat-card jea-3d-card visitors">
            <div class="jea-3d-bg"></div>
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('访客数', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon visitors">👥</span>
            </div>
            <div class="jea-stat-value" id="stat-visitors">0</div>
            <span class="jea-stat-change neutral" id="stat-visitors-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card jea-3d-card pageviews">
            <div class="jea-3d-bg"></div>
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('页面浏览量', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon pageviews">📄</span>
            </div>
            <div class="jea-stat-value" id="stat-pageviews">0</div>
            <span class="jea-stat-change neutral" id="stat-pageviews-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card jea-3d-card sessions">
            <div class="jea-3d-bg"></div>
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('会话数', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon sessions">🔄</span>
            </div>
            <div class="jea-stat-value" id="stat-sessions">0</div>
            <span class="jea-stat-change neutral" id="stat-sessions-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card jea-3d-card bounce">
            <div class="jea-3d-bg"></div>
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('跳出率', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon bounce">↩️</span>
            </div>
            <div class="jea-stat-value" id="stat-bounce-rate">0%</div>
            <span class="jea-stat-change neutral" id="stat-bounce-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card jea-3d-card duration">
            <div class="jea-3d-bg"></div>
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('平均访问时长', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">⏱️</span>
            </div>
            <div class="jea-stat-value" id="stat-avg-duration">0秒</div>
            <span class="jea-stat-change neutral" id="stat-duration-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-stat-card jea-3d-card pages">
            <div class="jea-3d-bg"></div>
            <div class="jea-stat-header">
                <span class="jea-stat-label"><?php _e('每次会话页面数', 'jeanalytics'); ?></span>
                <span class="jea-stat-icon" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">📑</span>
            </div>
            <div class="jea-stat-value" id="stat-pages-per-session">0</div>
            <span class="jea-stat-change neutral" id="stat-pages-change">— 0%</span>
            <p class="jea-stat-compare"><?php _e('对比上一周期', 'jeanalytics'); ?></p>
        </div>
    </div>

    <!-- 3D图表区域 -->
    <div class="jea-charts-grid">
        <!-- 主图表 - 3D效果 -->
        <div class="jea-card jea-3d-chart-card">
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
                <div class="jea-chart-container jea-3d-chart">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 3D环形图 - 设备分布 -->
        <div class="jea-card jea-3d-chart-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">📱 <?php _e('设备分布', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="devices-chart">
                <div class="jea-chart-container jea-3d-pie" style="height: 200px;">
                    <canvas id="devicesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 详细数据区域 - 3D卡片 -->
    <div class="jea-grid jea-grid-3">
        <!-- 热门页面 -->
        <div class="jea-card jea-3d-card-light">
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

        <!-- 搜索引擎来源 -->
        <div class="jea-card jea-3d-card-light">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🔍 <?php _e('搜索引擎', 'jeanalytics'); ?></h3>
                <a href="<?php echo admin_url('admin.php?page=jeanalytics-referrers'); ?>" class="jea-btn jea-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                    <?php _e('查看全部', 'jeanalytics'); ?>
                </a>
            </div>
            <div class="jea-card-body" id="search-engines-list">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>

        <!-- 国家/城市榜单 -->
        <div class="jea-card jea-3d-card-light">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🌍 <?php _e('地区榜单', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="geo-stats-list">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 设备品牌分析和小时分布 -->
    <div class="jea-grid jea-grid-2" style="margin-top: 20px;">
        <!-- 移动设备品牌分析 -->
        <div class="jea-card jea-3d-card-light">
            <div class="jea-card-header">
                <h3 class="jea-card-title">📱 <?php _e('移动设备品牌', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="device-brands-list">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>

        <!-- 访问时段分布 - 3D柱状图 -->
        <div class="jea-card jea-3d-card-light">
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

    <!-- 最近访客列表（含IP） -->
    <div class="jea-card jea-3d-card-light" style="margin-top: 20px;">
        <div class="jea-card-header">
            <h3 class="jea-card-title">👤 <?php _e('最近访客', 'jeanalytics'); ?></h3>
            <a href="<?php echo admin_url('admin.php?page=jeanalytics-visitors'); ?>" class="jea-btn jea-btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                <?php _e('查看全部', 'jeanalytics'); ?>
            </a>
        </div>
        <div class="jea-card-body" id="recent-visitors-list">
            <div class="jea-loading">
                <div class="jea-spinner"></div>
            </div>
        </div>
    </div>

</div>

<!-- 3D效果样式 -->
<style>
/* 3D卡片效果 */
.jea-3d-cards {
    perspective: 1000px;
}

.jea-3d-card {
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.jea-3d-card:hover {
    transform: translateY(-8px) rotateX(5deg);
    box-shadow:
        0 20px 40px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(99, 102, 241, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

.jea-3d-card .jea-3d-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
    border-radius: inherit;
    pointer-events: none;
}

.jea-3d-card.visitors {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
}

.jea-3d-card.visitors .jea-stat-label,
.jea-3d-card.visitors .jea-stat-compare { color: rgba(255,255,255,0.8); }
.jea-3d-card.visitors .jea-stat-value { color: white; }
.jea-3d-card.visitors .jea-stat-icon { background: rgba(255,255,255,0.2); color: white; }
.jea-3d-card.visitors::before { background: linear-gradient(90deg, rgba(255,255,255,0.3), transparent); }

.jea-3d-card.pageviews {
    background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
    color: white;
}
.jea-3d-card.pageviews .jea-stat-label,
.jea-3d-card.pageviews .jea-stat-compare { color: rgba(255,255,255,0.8); }
.jea-3d-card.pageviews .jea-stat-value { color: white; }
.jea-3d-card.pageviews .jea-stat-icon { background: rgba(255,255,255,0.2); color: white; }
.jea-3d-card.pageviews::before { background: linear-gradient(90deg, rgba(255,255,255,0.3), transparent); }

.jea-3d-card.sessions {
    background: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
    color: white;
}
.jea-3d-card.sessions .jea-stat-label,
.jea-3d-card.sessions .jea-stat-compare { color: rgba(255,255,255,0.8); }
.jea-3d-card.sessions .jea-stat-value { color: white; }
.jea-3d-card.sessions .jea-stat-icon { background: rgba(255,255,255,0.2); color: white; }
.jea-3d-card.sessions::before { background: linear-gradient(90deg, rgba(255,255,255,0.3), transparent); }

.jea-3d-card.bounce {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
}
.jea-3d-card.bounce .jea-stat-label,
.jea-3d-card.bounce .jea-stat-compare { color: rgba(255,255,255,0.8); }
.jea-3d-card.bounce .jea-stat-value { color: white; }
.jea-3d-card.bounce .jea-stat-icon { background: rgba(255,255,255,0.2); color: white; }
.jea-3d-card.bounce::before { background: linear-gradient(90deg, rgba(255,255,255,0.3), transparent); }

.jea-3d-card.duration {
    background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
    color: white;
}
.jea-3d-card.duration .jea-stat-label,
.jea-3d-card.duration .jea-stat-compare { color: rgba(255,255,255,0.8); }
.jea-3d-card.duration .jea-stat-value { color: white; }
.jea-3d-card.duration .jea-stat-icon { background: rgba(255,255,255,0.2) !important; color: white !important; }
.jea-3d-card.duration::before { background: linear-gradient(90deg, rgba(255,255,255,0.3), transparent); }

.jea-3d-card.pages {
    background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
    color: white;
}
.jea-3d-card.pages .jea-stat-label,
.jea-3d-card.pages .jea-stat-compare { color: rgba(255,255,255,0.8); }
.jea-3d-card.pages .jea-stat-value { color: white; }
.jea-3d-card.pages .jea-stat-icon { background: rgba(255,255,255,0.2) !important; color: white !important; }
.jea-3d-card.pages::before { background: linear-gradient(90deg, rgba(255,255,255,0.3), transparent); }

.jea-3d-card .jea-stat-change.up,
.jea-3d-card .jea-stat-change.down,
.jea-3d-card .jea-stat-change.neutral {
    background: rgba(255,255,255,0.2);
    color: white;
}

/* 3D图表卡片 */
.jea-3d-chart-card {
    box-shadow:
        0 10px 30px rgba(0, 0, 0, 0.1),
        0 1px 0 rgba(255, 255, 255, 0.5) inset;
    border: none;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
}

.jea-3d-chart {
    position: relative;
}

.jea-3d-chart::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 40px;
    background: linear-gradient(to top, rgba(99, 102, 241, 0.05), transparent);
    pointer-events: none;
}

/* 3D轻量卡片 */
.jea-3d-card-light {
    box-shadow:
        0 4px 20px rgba(0, 0, 0, 0.08),
        0 1px 0 rgba(255, 255, 255, 0.8) inset;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.jea-3d-card-light:hover {
    transform: translateY(-4px);
    box-shadow:
        0 12px 30px rgba(0, 0, 0, 0.12),
        0 1px 0 rgba(255, 255, 255, 0.8) inset;
}

/* 3D柱状图效果 */
.jea-3d-bar {
    position: relative;
    background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%);
    border-radius: 4px 4px 0 0;
    box-shadow:
        2px 0 0 rgba(79, 70, 229, 0.8),
        2px 2px 0 rgba(79, 70, 229, 0.6);
    transform: perspective(100px) rotateY(-2deg);
}

/* 访客列表项 */
.jea-visitor-row {
    display: grid;
    grid-template-columns: 120px 1fr 120px 100px 80px 100px;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--jea-border-light);
    align-items: center;
    font-size: 13px;
}

.jea-visitor-row:hover {
    background: var(--jea-bg-hover);
    margin: 0 -16px;
    padding: 12px 16px;
}

.jea-visitor-ip {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 12px;
    color: var(--jea-primary);
    background: rgba(99, 102, 241, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
}

.jea-visitor-location {
    display: flex;
    align-items: center;
    gap: 6px;
}

.jea-visitor-device {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.jea-visitor-device-brand {
    font-weight: 500;
    color: var(--jea-text);
}

.jea-visitor-device-model {
    font-size: 11px;
    color: var(--jea-text-muted);
}

/* 搜索引擎列表项 */
.jea-search-engine-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--jea-border-light);
}

.jea-search-engine-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.jea-search-engine-icon {
    font-size: 24px;
}

.jea-search-engine-name {
    font-weight: 500;
    color: var(--jea-text);
}

.jea-search-engine-url {
    font-size: 12px;
    color: var(--jea-text-muted);
}

.jea-search-engine-stats {
    text-align: right;
}

.jea-search-engine-count {
    font-size: 18px;
    font-weight: 600;
    color: var(--jea-text);
}

/* 地区榜单标签页 */
.jea-geo-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
}

.jea-geo-tab {
    padding: 6px 12px;
    border: none;
    background: var(--jea-bg);
    border-radius: 6px;
    font-size: 12px;
    color: var(--jea-text-secondary);
    cursor: pointer;
    transition: all 0.2s ease;
}

.jea-geo-tab.active {
    background: var(--jea-primary);
    color: white;
}

/* 设备品牌项 */
.jea-device-brand-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--jea-border-light);
}

.jea-device-brand-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.jea-device-brand-icon {
    width: 32px;
    height: 32px;
    background: var(--jea-bg);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.jea-device-brand-name {
    font-weight: 500;
}

.jea-device-brand-model {
    font-size: 12px;
    color: var(--jea-text-muted);
}
</style>
