<?php
/**
 * 管理后台 - 实时访客模板
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jea-wrap jea-realtime-container">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">⚡</span>
                <?php _e('实时访客', 'jeanalytics'); ?>
            </h1>
            <p class="jea-subtitle"><?php _e('查看当前正在浏览网站的访客', 'jeanalytics'); ?></p>
        </div>

        <div class="jea-controls">
            <div class="jea-realtime-header">
                <span class="jea-pulse"></span>
                <span style="color: #22c55e; font-weight: 500;"><?php _e('实时更新', 'jeanalytics'); ?></span>
            </div>
            <button class="jea-btn jea-btn-secondary jea-refresh-realtime">
                <span>🔄</span> <?php _e('刷新', 'jeanalytics'); ?>
            </button>
        </div>
    </div>

    <div class="jea-grid jea-grid-3">
        <!-- 实时访客数 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">👥 <?php _e('当前在线', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body">
                <div class="jea-realtime-count">
                    <span id="realtime-count">0</span>
                    <span><?php _e('位访客正在浏览', 'jeanalytics'); ?></span>
                </div>
            </div>
        </div>

        <!-- 设备分布 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">📱 <?php _e('设备类型', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="realtime-devices">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>

        <!-- 热门页面 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">🔥 <?php _e('热门页面', 'jeanalytics'); ?></h3>
            </div>
            <div class="jea-card-body" id="realtime-pages">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 访客列表 -->
    <div class="jea-card" style="margin-top: 20px;">
        <div class="jea-card-header">
            <h3 class="jea-card-title">🌐 <?php _e('实时访客列表', 'jeanalytics'); ?></h3>
            <span class="jea-badge jea-badge-success"><?php _e('最近5分钟', 'jeanalytics'); ?></span>
        </div>
        <div class="jea-card-body">
            <div class="jea-visitor-list" id="realtime-visitors">
                <div class="jea-loading">
                    <div class="jea-spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>
