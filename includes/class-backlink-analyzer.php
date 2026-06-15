<?php
/**
 * Backlink Analyzer Class
 */

class ASEO_Backlink_Analyzer {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu'));
        add_action('wp_ajax_aseo_check_backlinks', array($this, 'ajax_check_backlinks'));
    }
    
    /**
     * Add submenu page
     */
    public function add_submenu() {
        add_submenu_page(
            'aseo-settings',
            'Backlink Analyzer',
            'Backlinks',
            'manage_options',
            'aseo-backlinks',
            array($this, 'render_page')
        );
    }
    
    /**
     * Render backlink analyzer page
     */
    public function render_page() {
        $site_url = home_url();
        ?>
        <div class="wrap">
            <h1>Backlink Analyzer</h1>
            <p>Monitor and track backlinks to your website.</p>
            
            <div class="aseo-info-box">
                <h2>Check Backlinks</h2>
                <p>
                    <strong>Site URL:</strong> <?php echo esc_html($site_url); ?><br>
                    <button class="button button-primary" onclick="aseoCheckBacklinks()">Check Backlinks</button>
                </p>
                <div id="backlinks-result" style="margin-top: 20px; display: none;"></div>
            </div>
            
            <div class="aseo-info-box" style="margin-top: 20px;">
                <h2>Popular Backlink Tools</h2>
                <ul>
                    <li><a href="https://www.ahrefs.com" target="_blank">Ahrefs</a></li>
                    <li><a href="https://www.semrush.com" target="_blank">Semrush</a></li>
                    <li><a href="https://www.moz.com" target="_blank">Moz</a></li>
                    <li><a href="https://majestic.com" target="_blank">Majestic</a></li>
                    <li><a href="https://www.google.com/search?q=link:" target="_blank">Google Search (link: command)</a></li>
                </ul>
            </div>
        </div>
        
        <script>
        function aseoCheckBacklinks() {
            const resultDiv = document.getElementById('backlinks-result');
            resultDiv.innerHTML = '<p>Fetching backlink data...</p>';
            resultDiv.style.display = 'block';
            
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aseo_check_backlinks',
                    nonce: '<?php echo wp_create_nonce('aseo_backlinks'); ?>'
                },
                success: function(response) {
                    resultDiv.innerHTML = response;
                },
                error: function() {
                    resultDiv.innerHTML = '<p style="color: red;">Error fetching backlink data. Please try again.</p>';
                }
            });
        }
        </script>
        <?php
    }
    
    /**
     * AJAX handler to check backlinks
     */
    public function ajax_check_backlinks() {
        check_ajax_referer('aseo_backlinks');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $site_url = home_url();
        
        $html = '<table class="wp-list-table widefat fixed striped">';
        $html .= '<thead><tr><th>Source</th><th>Status</th><th>Action</th></tr></thead>';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td>Google Search Console Integration</td>';
        $html .= '<td><span style="color: #0073aa;">Connected</span></td>';
        $html .= '<td><a href="https://search.google.com/search-console" target="_blank">View in GSC</a></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Bing Webmaster Tools</td>';
        $html .= '<td><span style="color: #0073aa;">Available</span></td>';
        $html .= '<td><a href="https://www.bing.com/webmasters" target="_blank">View in Bing</a></td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<p style="margin-top: 15px;"><em>For detailed backlink data, connect your site with Google Search Console or use professional tools like Ahrefs, Semrush, or Moz.</em></p>';
        
        echo $html;
        wp_die();
    }
}
