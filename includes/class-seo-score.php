<?php
/**
 * SEO Score Calculator Class
 */

class ASEO_SEO_Score {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
    }
    
    /**
     * Add meta box to post editor
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_score_box',
            'SEO Score',
            array($this, 'render_meta_box'),
            array('post', 'page'),
            'side',
            'high'
        );
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        $score = $this->calculate_seo_score($post);
        $grade = $this->get_score_grade($score);
        $color = $this->get_score_color($score);
        
        ?>
        <div style="padding: 10px; text-align: center;">
            <div style="font-size: 48px; font-weight: bold; color: <?php echo $color; ?>; margin: 15px 0;">
                <?php echo $score; ?>
            </div>
            <p style="font-size: 18px; font-weight: bold; color: <?php echo $color; ?>; margin: 0;">
                <?php echo $grade; ?>
            </p>
            
            <div style="margin-top: 15px; padding: 10px; background: #f5f5f5; border-radius: 3px; text-align: left;">
                <p style="margin: 5px 0;"><strong>Factors:</strong></p>
                <ul style="font-size: 12px; margin: 5px 0; padding-left: 15px;">
                    <li>Meta Title: <?php echo $this->check_meta_title($post) ? '✓' : '✗'; ?></li>
                    <li>Meta Description: <?php echo $this->check_meta_description($post) ? '✓' : '✗'; ?></li>
                    <li>Focus Keyword: <?php echo $this->check_focus_keyword($post) ? '✓' : '✗'; ?></li>
                    <li>Content Length: <?php echo $this->check_content_length($post) ? '✓' : '✗'; ?></li>
                    <li>Headings: <?php echo $this->check_headings($post) ? '✓' : '✗'; ?></li>
                    <li>Images Alt Text: <?php echo $this->check_image_alt($post) ? '✓' : '✗'; ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Calculate overall SEO score
     */
    private function calculate_seo_score($post) {
        $score = 0;
        
        // Meta title (20 points)
        if ($this->check_meta_title($post)) $score += 20;
        
        // Meta description (20 points)
        if ($this->check_meta_description($post)) $score += 20;
        
        // Focus keyword (15 points)
        if ($this->check_focus_keyword($post)) $score += 15;
        
        // Content length (15 points)
        if ($this->check_content_length($post)) $score += 15;
        
        // Headings (15 points)
        if ($this->check_headings($post)) $score += 15;
        
        // Image alt text (15 points)
        if ($this->check_image_alt($post)) $score += 15;
        
        return min(100, $score);
    }
    
    /**
     * Check meta title
     */
    private function check_meta_title($post) {
        $title = get_post_meta($post->ID, '_aseo_meta_title', true);
        return !empty($title) && strlen($title) >= 30 && strlen($title) <= 60;
    }
    
    /**
     * Check meta description
     */
    private function check_meta_description($post) {
        $desc = get_post_meta($post->ID, '_aseo_meta_description', true);
        return !empty($desc) && strlen($desc) >= 120 && strlen($desc) <= 160;
    }
    
    /**
     * Check focus keyword
     */
    private function check_focus_keyword($post) {
        return !empty(get_post_meta($post->ID, '_aseo_focus_keyword', true));
    }
    
    /**
     * Check content length
     */
    private function check_content_length($post) {
        $word_count = str_word_count(strip_tags($post->post_content));
        return $word_count >= 300;
    }
    
    /**
     * Check headings
     */
    private function check_headings($post) {
        return preg_match('/<h[1-6][^>]*>[^<]+<\/h[1-6]>/i', $post->post_content);
    }
    
    /**
     * Check image alt text
     */
    private function check_image_alt($post) {
        preg_match_all('/<img[^>]+>/i', $post->post_content, $imgs);
        
        if (empty($imgs[0])) return true;
        
        $with_alt = 0;
        foreach ($imgs[0] as $img) {
            if (preg_match('/alt=["\']([^"\']*)["\']/i', $img)) {
                $with_alt++;
            }
        }
        
        return $with_alt / count($imgs[0]) >= 0.8;
    }
    
    /**
     * Get score grade
     */
    private function get_score_grade($score) {
        if ($score >= 90) return 'Excellent';
        if ($score >= 75) return 'Good';
        if ($score >= 60) return 'Fair';
        if ($score >= 45) return 'Poor';
        return 'Needs Improvement';
    }
    
    /**
     * Get score color
     */
    private function get_score_color($score) {
        if ($score >= 90) return '#28a745';
        if ($score >= 75) return '#17a2b8';
        if ($score >= 60) return '#ffc107';
        if ($score >= 45) return '#fd7e14';
        return '#dc3545';
    }
}
