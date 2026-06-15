<?php
/**
 * Content Freshness Tracker Class
 */

class ASEO_Content_Freshness {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
    }
    
    /**
     * Add meta box
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_freshness',
            'Content Freshness',
            array($this, 'render_meta_box'),
            array('post', 'page'),
            'side',
            'default'
        );
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        $published = strtotime($post->post_date);
        $modified = strtotime($post->post_modified);
        $today = time();
        
        $days_since_modified = round(($today - $modified) / 86400);
        $status = $this->get_freshness_status($days_since_modified);
        $color = $this->get_freshness_color($days_since_modified);
        
        ?>
        <div style="padding: 10px;">
            <p>
                <strong>Status:</strong> 
                <span style="color: <?php echo $color; ?>; font-weight: bold;"><?php echo $status; ?></span>
            </p>
            <p>
                <small>Published: <?php echo date_i18n('M j, Y', $published); ?></small><br>
                <small>Last Updated: <?php echo date_i18n('M j, Y', $modified); ?></small><br>
                <small>Days Since Update: <?php echo $days_since_modified; ?></small>
            </p>
            
            <?php if ($days_since_modified > 180) : ?>
                <p style="margin-top: 10px; padding: 8px; background: #fff3e0; border-left: 4px solid #ff9800;">
                    <small>Consider updating this content to keep it fresh.</small>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Get freshness status
     */
    private function get_freshness_status($days) {
        if ($days <= 30) return 'Fresh';
        if ($days <= 90) return 'Recent';
        if ($days <= 180) return 'Aging';
        return 'Outdated';
    }
    
    /**
     * Get freshness color
     */
    private function get_freshness_color($days) {
        if ($days <= 30) return '#28a745';
        if ($days <= 90) return '#17a2b8';
        if ($days <= 180) return '#ffc107';
        return '#dc3545';
    }
}
