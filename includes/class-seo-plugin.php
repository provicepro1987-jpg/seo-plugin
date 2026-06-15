<?php
/**
 * Main SEO Plugin Class
 */

class ASEO_Plugin {
    
    public function init() {
        // Initialize meta tags
        new ASEO_Meta_Tags();
        
        // Initialize sitemap
        new ASEO_Sitemap();
        
        // Initialize structured data
        new ASEO_Structured_Data();
        
        // Initialize admin settings
        new ASEO_Admin_Settings();
        
        // Initialize keywords analysis
        new ASEO_Keywords_Analysis();
        
        // Initialize readability checker
        new ASEO_Readability();
        
        // Load text domain for translations
        load_plugin_textdomain('advanced-seo-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Activation hook
     */
    public static function activate() {
        // Create custom tables if needed
        global $wpdb;
        
        // Table for storing SEO data
        $table_name = $wpdb->prefix . 'seo_meta';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id mediumint(9) NOT NULL,
            meta_title text NOT NULL,
            meta_description text NOT NULL,
            focus_keyword text,
            readability_score int,
            keyword_density float,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Set default options
        add_option('aseo_enable_meta_tags', 1);
        add_option('aseo_enable_sitemap', 1);
        add_option('aseo_enable_structured_data', 1);
        add_option('aseo_company_name', get_bloginfo('name'));
        add_option('aseo_company_logo', '');
    }
    
    /**
     * Deactivation hook
     */
    public static function deactivate() {
        // Clean up if needed
    }
}
