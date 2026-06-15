<?php
/**
 * Redirect Manager Class
 */

class ASEO_Redirect_Manager {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu'));
        add_action('wp_ajax_aseo_save_redirect', array($this, 'ajax_save_redirect'));
        add_action('wp_ajax_aseo_delete_redirect', array($this, 'ajax_delete_redirect'));
        add_action('template_redirect', array($this, 'check_redirects'));
    }
    
    /**
     * Add submenu page
     */
    public function add_submenu() {
        add_submenu_page(
            'aseo-settings',
            'Redirect Manager',
            'Redirects',
            'manage_options',
            'aseo-redirects',
            array($this, 'render_page')
        );
    }
    
    /**
     * Render redirect manager page
     */
    public function render_page() {
        $redirects = get_option('aseo_redirects', array());
        
        ?>
        <div class="wrap">
            <h1>Redirect Manager</h1>
            
            <div class="aseo-info-box">
                <h2>Add New Redirect</h2>
                <form id="aseo-redirect-form" style="margin: 15px 0;">
                    <p>
                        <label><strong>From URL:</strong></label>
                        <input type="text" id="from_url" placeholder="/old-page" style="width: 100%; padding: 8px; margin: 5px 0;" required>
                    </p>
                    <p>
                        <label><strong>To URL:</strong></label>
                        <input type="text" id="to_url" placeholder="/new-page" style="width: 100%; padding: 8px; margin: 5px 0;" required>
                    </p>
                    <p>
                        <label><strong>Type:</strong></label>
                        <select id="redirect_type" style="width: 100%; padding: 8px; margin: 5px 0;">
                            <option value="301">301 Moved Permanently</option>
                            <option value="302">302 Found</option>
                            <option value="307">307 Temporary Redirect</option>
                        </select>
                    </p>
                    <button type="button" class="button button-primary" onclick="aseoSaveRedirect()">Add Redirect</button>
                </form>
            </div>
            
            <div class="aseo-info-box" style="margin-top: 20px;">
                <h2>Active Redirects</h2>
                <?php if (!empty($redirects)) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($redirects as $index => $redirect) : ?>
                                <tr>
                                    <td><?php echo esc_html($redirect['from']); ?></td>
                                    <td><?php echo esc_html($redirect['to']); ?></td>
                                    <td><?php echo esc_html($redirect['type']); ?></td>
                                    <td><button class="button button-small button-link-delete" onclick="aseoDeleteRedirect(<?php echo $index; ?>)">Delete</button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="color: #999; font-style: italic;">No redirects configured yet.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <script>
        function aseoSaveRedirect() {
            const fromUrl = document.getElementById('from_url').value;
            const toUrl = document.getElementById('to_url').value;
            const redirectType = document.getElementById('redirect_type').value;
            
            if (!fromUrl || !toUrl) {
                alert('Please fill in all fields');
                return;
            }
            
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aseo_save_redirect',
                    nonce: '<?php echo wp_create_nonce('aseo_redirect'); ?>',
                    from: fromUrl,
                    to: toUrl,
                    type: redirectType
                },
                success: function() {
                    location.reload();
                }
            });
        }
        
        function aseoDeleteRedirect(index) {
            if (!confirm('Delete this redirect?')) return;
            
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aseo_delete_redirect',
                    nonce: '<?php echo wp_create_nonce('aseo_redirect'); ?>',
                    index: index
                },
                success: function() {
                    location.reload();
                }
            });
        }
        </script>
        <?php
    }
    
    /**
     * AJAX save redirect
     */
    public function ajax_save_redirect() {
        check_ajax_referer('aseo_redirect');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $redirects = get_option('aseo_redirects', array());
        $redirects[] = array(
            'from' => sanitize_text_field($_POST['from']),
            'to' => sanitize_text_field($_POST['to']),
            'type' => sanitize_text_field($_POST['type'])
        );
        
        update_option('aseo_redirects', $redirects);
        wp_die('Redirect saved');
    }
    
    /**
     * AJAX delete redirect
     */
    public function ajax_delete_redirect() {
        check_ajax_referer('aseo_redirect');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $index = intval($_POST['index']);
        $redirects = get_option('aseo_redirects', array());
        
        unset($redirects[$index]);
        update_option('aseo_redirects', array_values($redirects));
        
        wp_die('Redirect deleted');
    }
    
    /**
     * Check and handle redirects
     */
    public function check_redirects() {
        $redirects = get_option('aseo_redirects', array());
        $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($redirects as $redirect) {
            if ($current_path === $redirect['from']) {
                wp_redirect($redirect['to'], intval($redirect['type']));
                exit;
            }
        }
    }
}
