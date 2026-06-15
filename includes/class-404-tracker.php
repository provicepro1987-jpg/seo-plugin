<?php
/**
 * 404 Error Tracker Class
 */

class ASEO_404_Tracker {
    
    public function __construct() {
        add_action('template_redirect', array($this, 'track_404'));
        add_action('admin_menu', array($this, 'add_submenu'));
    }
    
    /**
     * Track 404 errors
     */
    public function track_404() {
        if (is_404()) {
            $url = $_SERVER['REQUEST_URI'];
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct';
            
            $errors = get_option('aseo_404_errors', array());
            
            $key = md5($url);
            if (isset($errors[$key])) {
                $errors[$key]['count']++;
            } else {
                $errors[$key] = array(
                    'url' => $url,
                    'referer' => $referer,
                    'count' => 1,
                    'first_seen' => current_time('mysql')
                );
            }
            
            update_option('aseo_404_errors', $errors);
        }
    }
    
    /**
     * Add submenu page
     */
    public function add_submenu() {
        add_submenu_page(
            'aseo-settings',
            '404 Errors',
            '404 Tracker',
            'manage_options',
            'aseo-404',
            array($this, 'render_page')
        );
    }
    
    /**
     * Render tracker page
     */
    public function render_page() {
        $errors = get_option('aseo_404_errors', array());
        
        // Sort by count
        usort($errors, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        ?>
        <div class="wrap">
            <h1>404 Error Tracker</h1>
            <p>Monitor broken links and 404 errors on your website.</p>
            
            <?php if (!empty($errors)) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th>Count</th>
                            <th>First Seen</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($errors, 0, 50) as $key => $error) : ?>
                            <tr>
                                <td><?php echo esc_html($error['url']); ?></td>
                                <td><?php echo $error['count']; ?></td>
                                <td><?php echo esc_html($error['first_seen']); ?></td>
                                <td><a href="<?php echo admin_url('admin.php?page=aseo-redirects'); ?>" class="button button-small">Create Redirect</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p style="color: #999; font-style: italic;">No 404 errors tracked yet.</p>
            <?php endif; ?>
        </div>
        <?php
    }
}
