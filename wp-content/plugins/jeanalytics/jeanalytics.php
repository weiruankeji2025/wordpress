<?php
/**
 * Plugin Name: JE Analytics - 智能流量分析
 * Plugin URI: https://jeanalytics.dev
 * Description: 一款专业、现代化的WordPress流量和访客分析工具，提供详尽的数据分析和精美的可视化界面
 * Version: 1.0.0
 * Author: JE Analytics Team
 * Author URI: https://jeanalytics.dev
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: jeanalytics
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 插件常量定义
define('JEA_VERSION', '1.0.0');
define('JEA_PLUGIN_FILE', __FILE__);
define('JEA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JEA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('JEA_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * 主插件类
 */
final class JEAnalytics {

    /**
     * 单例实例
     */
    private static $instance = null;

    /**
     * 获取单例实例
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 构造函数
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * 包含必要文件
     */
    private function includes() {
        // 核心类
        require_once JEA_PLUGIN_DIR . 'includes/class-jea-database.php';
        require_once JEA_PLUGIN_DIR . 'includes/class-jea-tracker.php';
        require_once JEA_PLUGIN_DIR . 'includes/class-jea-ajax.php';
        require_once JEA_PLUGIN_DIR . 'includes/class-jea-stats.php';
        require_once JEA_PLUGIN_DIR . 'includes/class-jea-geoip.php';
        require_once JEA_PLUGIN_DIR . 'includes/class-jea-export.php';

        // 管理后台
        if (is_admin()) {
            require_once JEA_PLUGIN_DIR . 'includes/admin/class-jea-admin.php';
            require_once JEA_PLUGIN_DIR . 'includes/admin/class-jea-dashboard.php';
            require_once JEA_PLUGIN_DIR . 'includes/admin/class-jea-settings.php';
        }
    }

    /**
     * 初始化钩子
     */
    private function init_hooks() {
        register_activation_hook(JEA_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(JEA_PLUGIN_FILE, array($this, 'deactivate'));

        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    /**
     * 插件激活
     */
    public function activate() {
        JEA_Database::create_tables();

        // 添加默认选项
        $default_options = array(
            'track_logged_users' => 'yes',
            'track_admin' => 'no',
            'exclude_ips' => '',
            'data_retention' => 365,
            'realtime_refresh' => 30,
            'dashboard_widget' => 'yes',
        );

        add_option('jea_settings', $default_options);
        add_option('jea_version', JEA_VERSION);

        // 刷新重写规则
        flush_rewrite_rules();
    }

    /**
     * 插件停用
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * 初始化
     */
    public function init() {
        // 初始化追踪器
        JEA_Tracker::instance();

        // 初始化AJAX处理
        JEA_Ajax::instance();

        // 初始化管理后台
        if (is_admin()) {
            JEA_Admin::instance();
        }
    }

    /**
     * 加载语言文件
     */
    public function load_textdomain() {
        load_plugin_textdomain('jeanalytics', false, dirname(JEA_PLUGIN_BASENAME) . '/languages/');
    }
}

/**
 * 返回主插件实例
 */
function JEA() {
    return JEAnalytics::instance();
}

// 启动插件
JEA();
