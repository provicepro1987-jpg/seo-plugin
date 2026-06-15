<?php
/**
 * Keyword Ranking Tracker Class
 */

class ASEO_Keyword_Tracking {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu'));
        add_action('wp_ajax_aseo_track_keywords', array($this, 'ajax_track_keywords'));
    }
    
    /**
     * Add submenu page
     */
    public function add_submenu() {
        add_submenu_page(
            'aseo-settings',
            'Keyword Tracking',
            'Keyword Tracking',
            'manage_options',
            'aseo-keywords-track',
            array($this, 'render_page')
        );
    }
    
    /**
     * Render tracking page
     */
    public function render_page() {
        $tracked = get_option('aseo_tracked_keywords', array());
        ?>
        <div class="wrap">
            <h1>Keyword Ranking Tracker</h1>
            <p>Track your keyword rankings in Google Search Results.</p>
            
            <div class="aseo-info-box">
                <h2>Add Keyword to Track</h2>
                <p>
                    <input type="text" id="track-keyword" placeholder="Enter keyword..." style="width: 60%; padding: 8px;" />
                    <button class="button button-primary" onclick="aseoAddKeywordTracking()">Add Keyword</button>
                </p>
            </div>
            
            <?php if (!empty($tracked)) : ?>
                <div class="aseo-info-box" style="margin-top: 20px;">
                    <h2>Tracked Keywords</h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Current Rank</th>
                                <th>Last Checked</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tracked as $keyword) : ?>
                                <tr>
                                    <td><?php echo esc_html($keyword); ?></td>
                                    <td>#--</td>
                                    <td><?php echo date_i18n('M j, Y'); ?></td>
                                    <td>--</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p style="margin-top: 15px; font-size: 12px; color: #666;">
                        <em>For accurate ranking data, use Google Search Console or professional tools like Ahrefs or Semrush.</em>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        function aseoAddKeywordTracking() {
            const keyword = document.getElementById('track-keyword').value;
            if (!keyword) {
                alert('Please enter a keyword');
                return;
            }
            
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aseo_track_keywords',
                    nonce: '<?php echo wp_create_nonce('aseo_keywords'); ?>',
                    keyword: keyword
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
     * AJAX track keywords
     */
    public function ajax_track_keywords() {
        check_ajax_referer('aseo_keywords');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $keyword = sanitize_text_field($_POST['keyword']);
        $tracked = get_option('aseo_tracked_keywords', array());
        
        if (!in_array($keyword, $tracked)) {
            $tracked[] = $keyword;
            update_option('aseo_tracked_keywords', $tracked);
        }
        
        wp_die('Keyword added');
    }
}
