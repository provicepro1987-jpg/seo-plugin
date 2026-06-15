<?php
/**
 * Admin Settings Page Class
 */

class ASEO_Admin_Settings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'SEO Settings',
            'SEO',
            'manage_options',
            'aseo-settings',
            array($this, 'render_settings_page'),
            'dashicons-search',
            25
        );
        
        add_submenu_page(
            'aseo-settings',
            'General Settings',
            'General',
            'manage_options',
            'aseo-settings',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'aseo-settings',
            'SEO Analysis',
            'Analysis',
            'manage_options',
            'aseo-analysis',
            array($this, 'render_analysis_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('aseo_settings_group', 'aseo_company_name');
        register_setting('aseo_settings_group', 'aseo_company_logo');
        register_setting('aseo_settings_group', 'aseo_enable_meta_tags');
        register_setting('aseo_settings_group', 'aseo_enable_sitemap');
        register_setting('aseo_settings_group', 'aseo_enable_structured_data');
    }
    
    /**
     * Enqueue scripts
     */
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'aseo') === false) {
            return;
        }
        
        wp_enqueue_style('aseo-admin-style', ASEO_PLUGIN_URL . 'assets/css/admin.css', array(), ASEO_PLUGIN_VERSION);
        wp_enqueue_script('aseo-admin-script', ASEO_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), ASEO_PLUGIN_VERSION, true);
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>SEO Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('aseo_settings_group'); ?>
                <?php do_settings_sections('aseo_settings_group'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="aseo_company_name">Company Name</label></th>
                        <td>
                            <input type="text" name="aseo_company_name" value="<?php echo esc_attr(get_option('aseo_company_name')); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aseo_company_logo">Company Logo URL</label></th>
                        <td>
                            <input type="url" name="aseo_company_logo" value="<?php echo esc_url(get_option('aseo_company_logo')); ?>" class="regular-text" />
                            <p class="description">Use a square image (600x600px recommended)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Enable Features</th>
                        <td>
                            <label>
                                <input type="checkbox" name="aseo_enable_meta_tags" value="1" <?php checked(get_option('aseo_enable_meta_tags'), 1); ?> />
                                Enable Meta Tags
                            </label><br />
                            <label>
                                <input type="checkbox" name="aseo_enable_sitemap" value="1" <?php checked(get_option('aseo_enable_sitemap'), 1); ?> />
                                Enable XML Sitemap
                            </label><br />
                            <label>
                                <input type="checkbox" name="aseo_enable_structured_data" value="1" <?php checked(get_option('aseo_enable_structured_data'), 1); ?> />
                                Enable Structured Data
                            </label>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <div class="aseo-info-box">
                <h2>SEO Information</h2>
                <p><strong>Sitemap URL:</strong> <a href="<?php echo home_url('sitemap.xml'); ?>" target="_blank"><?php echo home_url('sitemap.xml'); ?></a></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render analysis page
     */
    public function render_analysis_page() {
        $posts = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'numberposts' => 50
        ));
        
        ?>
        <div class="wrap">
            <h1>SEO Analysis</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Meta Title</th>
                        <th>Meta Description</th>
                        <th>Focus Keyword</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post) {
                        $meta_title = get_post_meta($post->ID, '_aseo_meta_title', true);
                        $meta_desc = get_post_meta($post->ID, '_aseo_meta_description', true);
                        $keyword = get_post_meta($post->ID, '_aseo_focus_keyword', true);
                        $status = $meta_title && $meta_desc && $keyword ? '✓ Complete' : '✗ Incomplete';
                        ?>
                        <tr>
                            <td><a href="<?php echo get_edit_post_link($post); ?>"><?php echo esc_html($post->post_title); ?></a></td>
                            <td><?php echo esc_html($meta_title ?: 'Not set'); ?></td>
                            <td><?php echo esc_html(substr($meta_desc, 0, 50) . '...'); ?></td>
                            <td><?php echo esc_html($keyword ?: 'Not set'); ?></td>
                            <td><?php echo $status; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
