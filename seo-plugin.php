<?php
/**
 * Plugin Name: Advanced SEO Plugin
 * Plugin URI: https://github.com/provicepro1987-jpg/seo-plugin
 * Description: A comprehensive SEO plugin with meta tags, sitemap, structured data, and more
 * Version: 1.0.0
 * Author: SEO Team
 * Author URI: https://github.com/provicepro1987-jpg
 * License: GPL2
 * Text Domain: advanced-seo-plugin
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ASEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASEO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ASEO_PLUGIN_VERSION', '1.0.0');

// Include required files
require_once ASEO_PLUGIN_DIR . 'includes/class-seo-plugin.php';
require_once ASEO_PLUGIN_DIR . 'includes/class-meta-tags.php';
require_once ASEO_PLUGIN_DIR . 'includes/class-sitemap.php';
require_once ASEO_PLUGIN_DIR . 'includes/class-structured-data.php';
require_once ASEO_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once ASEO_PLUGIN_DIR . 'includes/class-keywords-analysis.php';
require_once ASEO_PLUGIN_DIR . 'includes/class-readability.php';

// Initialize the plugin
function aseo_init() {
    $plugin = new ASEO_Plugin();
    $plugin->init();
}
add_action('plugins_loaded', 'aseo_init');

// Activation hook
register_activation_hook(__FILE__, array('ASEO_Plugin', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('ASEO_Plugin', 'deactivate'));
