<?php
/**
 * 管理后台 - 设置模板
 * 科技风格版本
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = JEA_Settings::get();
?>

<div class="jea-wrap jea-tech">
    <!-- 头部 -->
    <div class="jea-header">
        <div>
            <h1 class="jea-title">
                <span class="jea-title-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                </span>
                <?php _e('设置', 'jeanalytics'); ?>
            </h1>
            <p class="jea-subtitle"><?php _e('配置威软访客的行为和功能', 'jeanalytics'); ?></p>
        </div>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields('jea_settings_group'); ?>

        <!-- 追踪设置 -->
        <div class="jea-card" style="margin-bottom: 20px;">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22,4 12,14.01 9,11.01"/>
                    </svg>
                    <?php _e('追踪设置', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body">
                <div class="jea-form-group">
                    <div class="jea-checkbox-group">
                        <input type="checkbox" name="jea_settings[track_logged_users]" id="track_logged_users" value="yes" class="jea-checkbox" <?php checked($settings['track_logged_users'], 'yes'); ?>>
                        <label for="track_logged_users" class="jea-form-label" style="margin-bottom: 0;">
                            <?php _e('追踪已登录用户', 'jeanalytics'); ?>
                        </label>
                    </div>
                    <p class="jea-form-hint"><?php _e('启用后将追踪已登录用户的访问行为', 'jeanalytics'); ?></p>
                </div>

                <div class="jea-form-group">
                    <div class="jea-checkbox-group">
                        <input type="checkbox" name="jea_settings[track_admin]" id="track_admin" value="yes" class="jea-checkbox" <?php checked($settings['track_admin'], 'yes'); ?>>
                        <label for="track_admin" class="jea-form-label" style="margin-bottom: 0;">
                            <?php _e('追踪管理员', 'jeanalytics'); ?>
                        </label>
                    </div>
                    <p class="jea-form-hint"><?php _e('启用后将追踪管理员用户的访问（通常建议关闭）', 'jeanalytics'); ?></p>
                </div>

                <div class="jea-form-group">
                    <label for="exclude_ips" class="jea-form-label">
                        <?php _e('排除IP地址', 'jeanalytics'); ?>
                    </label>
                    <textarea name="jea_settings[exclude_ips]" id="exclude_ips" class="jea-textarea" placeholder="<?php _e('每行一个IP地址', 'jeanalytics'); ?>"><?php echo esc_textarea($settings['exclude_ips']); ?></textarea>
                    <p class="jea-form-hint"><?php _e('这些IP地址的访问将不会被追踪，每行填写一个IP', 'jeanalytics'); ?></p>
                </div>
            </div>
        </div>

        <!-- 数据设置 -->
        <div class="jea-card" style="margin-bottom: 20px;">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <ellipse cx="12" cy="5" rx="9" ry="3"/>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                    </svg>
                    <?php _e('数据设置', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body">
                <div class="jea-form-group">
                    <label for="data_retention" class="jea-form-label">
                        <?php _e('数据保留天数', 'jeanalytics'); ?>
                    </label>
                    <select name="jea_settings[data_retention]" id="data_retention" class="jea-select">
                        <option value="30" <?php selected($settings['data_retention'], 30); ?>><?php _e('30 天', 'jeanalytics'); ?></option>
                        <option value="90" <?php selected($settings['data_retention'], 90); ?>><?php _e('90 天', 'jeanalytics'); ?></option>
                        <option value="180" <?php selected($settings['data_retention'], 180); ?>><?php _e('180 天', 'jeanalytics'); ?></option>
                        <option value="365" <?php selected($settings['data_retention'], 365); ?>><?php _e('365 天（1年）', 'jeanalytics'); ?></option>
                        <option value="730" <?php selected($settings['data_retention'], 730); ?>><?php _e('730 天（2年）', 'jeanalytics'); ?></option>
                        <option value="0" <?php selected($settings['data_retention'], 0); ?>><?php _e('永久保留', 'jeanalytics'); ?></option>
                    </select>
                    <p class="jea-form-hint"><?php _e('超过此天数的详细数据将被自动清理，汇总数据会保留', 'jeanalytics'); ?></p>
                </div>
            </div>
        </div>

        <!-- 显示设置 -->
        <div class="jea-card" style="margin-bottom: 20px;">
            <div class="jea-card-header">
                <h3 class="jea-card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                    <?php _e('显示设置', 'jeanalytics'); ?>
                </h3>
            </div>
            <div class="jea-card-body">
                <div class="jea-form-group">
                    <label for="realtime_refresh" class="jea-form-label">
                        <?php _e('实时数据刷新间隔', 'jeanalytics'); ?>
                    </label>
                    <select name="jea_settings[realtime_refresh]" id="realtime_refresh" class="jea-select">
                        <option value="15" <?php selected($settings['realtime_refresh'], 15); ?>><?php _e('15 秒', 'jeanalytics'); ?></option>
                        <option value="30" <?php selected($settings['realtime_refresh'], 30); ?>><?php _e('30 秒', 'jeanalytics'); ?></option>
                        <option value="60" <?php selected($settings['realtime_refresh'], 60); ?>><?php _e('60 秒', 'jeanalytics'); ?></option>
                    </select>
                    <p class="jea-form-hint"><?php _e('实时访客页面的自动刷新间隔', 'jeanalytics'); ?></p>
                </div>

                <div class="jea-form-group">
                    <div class="jea-checkbox-group">
                        <input type="checkbox" name="jea_settings[dashboard_widget]" id="dashboard_widget" value="yes" class="jea-checkbox" <?php checked($settings['dashboard_widget'], 'yes'); ?>>
                        <label for="dashboard_widget" class="jea-form-label" style="margin-bottom: 0;">
                            <?php _e('显示WordPress仪表板小部件', 'jeanalytics'); ?>
                        </label>
                    </div>
                    <p class="jea-form-hint"><?php _e('在WordPress后台首页显示流量概览小部件', 'jeanalytics'); ?></p>
                </div>
            </div>
        </div>

        <!-- 保存按钮 -->
        <div style="margin-top: 24px;">
            <?php submit_button(__('保存设置', 'jeanalytics'), 'jea-btn jea-btn-primary', 'submit', false); ?>
        </div>
    </form>

    <!-- 插件信息 -->
    <div class="jea-card" style="margin-top: 32px;">
        <div class="jea-card-header">
            <h3 class="jea-card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <?php _e('关于 威软访客', 'jeanalytics'); ?>
            </h3>
        </div>
        <div class="jea-card-body">
            <div class="jea-grid jea-grid-3">
                <div class="jea-info-item">
                    <strong><?php _e('版本', 'jeanalytics'); ?></strong>
                    <p><?php echo JEA_VERSION; ?></p>
                </div>
                <div class="jea-info-item">
                    <strong><?php _e('数据库版本', 'jeanalytics'); ?></strong>
                    <p><?php echo get_option('jea_db_version', '1.0.0'); ?></p>
                </div>
                <div class="jea-info-item">
                    <strong><?php _e('PHP版本', 'jeanalytics'); ?></strong>
                    <p><?php echo PHP_VERSION; ?></p>
                </div>
            </div>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--jea-border);">
                <p style="color: var(--jea-text-secondary); font-size: 14px;">
                    <?php _e('感谢使用 威软访客！这是一款完全开源免费、轻量级、隐私友好的WordPress流量分析工具。', 'jeanalytics'); ?>
                </p>
                <p style="color: var(--jea-text-muted); font-size: 13px; margin-top: 8px;">
                    <?php _e('开源地址：', 'jeanalytics'); ?><a href="https://github.com/weiruankeji2025/wordpress" target="_blank" style="color: var(--jea-primary);">https://github.com/weiruankeji2025/wordpress</a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
/* 设置页面特定样式 */
.jea-tech .jea-form-group {
    margin-bottom: 20px;
}

.jea-tech .jea-form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 8px;
    color: var(--jea-text);
}

.jea-tech .jea-form-hint {
    margin-top: 6px;
    font-size: 13px;
    color: var(--jea-text-muted);
}

.jea-tech .jea-checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.jea-tech .jea-checkbox {
    width: 18px;
    height: 18px;
    accent-color: var(--jea-primary);
}

.jea-tech .jea-select,
.jea-tech .jea-textarea {
    width: 100%;
    max-width: 400px;
    padding: 10px 12px;
    background: var(--jea-bg);
    border: 1px solid var(--jea-border);
    border-radius: 6px;
    color: var(--jea-text);
    font-size: 14px;
}

.jea-tech .jea-textarea {
    min-height: 100px;
    resize: vertical;
}

.jea-tech .jea-select:focus,
.jea-tech .jea-textarea:focus {
    outline: none;
    border-color: var(--jea-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.jea-tech .jea-info-item {
    padding: 12px;
    background: rgba(59, 130, 246, 0.05);
    border-radius: 6px;
}

.jea-tech .jea-info-item strong {
    display: block;
    font-size: 12px;
    color: var(--jea-text-secondary);
    margin-bottom: 4px;
}

.jea-tech .jea-info-item p {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--jea-text);
}

.jea-tech .submit {
    margin: 0;
    padding: 0;
}

.jea-tech input[type="submit"].jea-btn {
    margin: 0;
}
</style>
