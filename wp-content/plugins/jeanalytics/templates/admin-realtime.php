<?php
/**
 * 管理后台 - 实时访客模板
 * 科技风格版本
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jea-wrap jea-tech jea-realtime-container">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="13,2 3,14 12,14 11,22 21,10 12,10 13,2"/>
                    </svg>
                </span>
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
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23,4 23,10 17,10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
                <?php _e('刷新', 'jeanalytics'); ?>
            </button>
        </div>
    </div>

    <div class="jea-grid jea-grid-3">
        <!-- 实时访客数 -->
        <div class="jea-card">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <?php _e('当前在线', 'jeanalytics'); ?>
                </h3>
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
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                        <line x1="12" y1="18" x2="12.01" y2="18"/>
                    </svg>
                    <?php _e('设备类型', 'jeanalytics'); ?>
                </h3>
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
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                    </svg>
                    <?php _e('热门页面', 'jeanalytics'); ?>
                </h3>
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
            <h3 class="jea-card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                <?php _e('实时访客列表', 'jeanalytics'); ?>
            </h3>
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

<style>
/* 实时访客页面特定样式 */
.jea-tech .jea-realtime-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(34, 197, 94, 0.1);
    border-radius: 6px;
    margin-right: 12px;
}

.jea-tech .jea-pulse {
    width: 10px;
    height: 10px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
    }
}

.jea-tech .jea-realtime-count {
    text-align: center;
    padding: 20px;
}

.jea-tech .jea-realtime-count span:first-child {
    display: block;
    font-size: 48px;
    font-weight: 700;
    color: var(--jea-primary);
    line-height: 1;
    margin-bottom: 8px;
}

.jea-tech .jea-realtime-count span:last-child {
    font-size: 14px;
    color: var(--jea-text-secondary);
}

.jea-tech .jea-visitor-list {
    min-height: 200px;
}

.jea-tech .jea-visitor-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: rgba(59, 130, 246, 0.05);
    border-radius: 8px;
    margin-bottom: 8px;
    gap: 12px;
}

.jea-tech .jea-visitor-item:last-child {
    margin-bottom: 0;
}

.jea-tech .jea-visitor-avatar {
    width: 40px;
    height: 40px;
    background: var(--jea-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    flex-shrink: 0;
}

.jea-tech .jea-visitor-info {
    flex: 1;
    min-width: 0;
}

.jea-tech .jea-visitor-page {
    font-weight: 500;
    color: var(--jea-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.jea-tech .jea-visitor-meta {
    display: flex;
    gap: 12px;
    font-size: 12px;
    color: var(--jea-text-muted);
    margin-top: 4px;
}

.jea-tech .jea-visitor-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.jea-tech .jea-visitor-time {
    font-size: 12px;
    color: var(--jea-text-muted);
    white-space: nowrap;
}

.jea-tech .jea-refresh-realtime svg {
    margin-right: 6px;
}
</style>
