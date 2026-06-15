<?php
/**
 * SEO Report Generator Class
 */

class ASEO_Report_Generator {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu'));
        add_action('wp_ajax_aseo_generate_report', array($this, 'ajax_generate_report'));
    }
    
    /**
     * Add submenu page
     */
    public function add_submenu() {
        add_submenu_page(
            'aseo-settings',
            'SEO Reports',
            'Reports',
            'manage_options',
            'aseo-reports',
            array($this, 'render_page')
        );
    }
    
    /**
     * Render reports page
     */
    public function render_page() {
        ?>
        <div class="wrap">
            <h1>SEO Reports</h1>
            <p>Generate comprehensive SEO analysis reports for your site.</p>
            
            <div class="aseo-info-box">
                <h2>Generate New Report</h2>
                <button class="button button-primary" onclick="aseoGenerateReport()">Generate Full SEO Report</button>
                <div id="report-output" style="margin-top: 20px; display: none;"></div>
            </div>
        </div>
        
        <script>
        function aseoGenerateReport() {
            const output = document.getElementById('report-output');
            output.innerHTML = '<p>Generating report...</p>';
            output.style.display = 'block';
            
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aseo_generate_report',
                    nonce: '<?php echo wp_create_nonce('aseo_report'); ?>'
                },
                success: function(response) {
                    output.innerHTML = response;
                },
                error: function() {
                    output.innerHTML = '<p style="color: red;">Error generating report.</p>';
                }
            });
        }
        </script>
        <?php
    }
    
    /**
     * AJAX generate report
     */
    public function ajax_generate_report() {
        check_ajax_referer('aseo_report');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $report = $this->generate_full_report();
        echo $report;
        wp_die();
    }
    
    /**
     * Generate full report
     */
    private function generate_full_report() {
        $posts = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'numberposts' => -1
        ));
        
        $html = '<div style="padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 5px;">';
        $html .= '<h2>SEO Analysis Report</h2>';
        $html .= '<p><strong>Generated:</strong> ' . current_time('mysql') . '</p>';
        $html .= '<p><strong>Site:</strong> ' . esc_html(get_bloginfo('name')) . '</p>';
        
        $html .= '<h3>Content Overview</h3>';
        $html .= '<ul>';
        $html .= '<li>Total Posts/Pages: ' . count($posts) . '</li>';
        $html .= '</ul>';
        
        $html .= '<h3>Top Pages by Content Length</h3>';
        $html .= '<table class="wp-list-table widefat fixed striped" style="margin-top: 10px;">';
        $html .= '<thead><tr><th>Title</th><th>Words</th><th>SEO Score</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach (array_slice($posts, 0, 10) as $post) {
            $word_count = str_word_count(strip_tags($post->post_content));
            $score = $this->get_seo_score($post->ID);
            $html .= '<tr>';
            $html .= '<td>' . esc_html($post->post_title) . '</td>';
            $html .= '<td>' . $word_count . '</td>';
            $html .= '<td>' . $score . '/100</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<p style="margin-top: 15px;"><button class="button" onclick="window.print()">Print Report</button></p>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Get SEO score for post
     */
    private function get_seo_score($post_id) {
        $post = get_post($post_id);
        $score = 0;
        
        if (!empty(get_post_meta($post_id, '_aseo_meta_title', true))) $score += 20;
        if (!empty(get_post_meta($post_id, '_aseo_meta_description', true))) $score += 20;
        if (!empty(get_post_meta($post_id, '_aseo_focus_keyword', true))) $score += 15;
        if (str_word_count(strip_tags($post->post_content)) >= 300) $score += 15;
        if (preg_match('/<h[1-6][^>]*>/i', $post->post_content)) $score += 15;
        if (preg_match('/<img[^>]+alt=/i', $post->post_content)) $score += 15;
        
        return min(100, $score);
    }
}
