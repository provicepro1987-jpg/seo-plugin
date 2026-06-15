<?php
/**
 * Social Media Preview Class
 */

class ASEO_Social_Preview {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_social_data'));
    }
    
    /**
     * Add meta box
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_social_preview',
            'Social Media Preview',
            array($this, 'render_meta_box'),
            array('post', 'page'),
            'normal',
            'low'
        );
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        $og_title = get_post_meta($post->ID, '_aseo_og_title', true);
        $og_desc = get_post_meta($post->ID, '_aseo_og_description', true);
        $twitter_card = get_post_meta($post->ID, '_aseo_twitter_card', true);
        
        if (empty($og_title)) $og_title = get_the_title($post);
        if (empty($og_desc)) $og_desc = wp_trim_words(get_the_excerpt($post), 20);
        if (empty($twitter_card)) $twitter_card = 'summary';
        
        wp_nonce_field('aseo_social_nonce', 'aseo_social_nonce');
        
        ?>
        <div style="padding: 10px;">
            <p>
                <label><strong>Facebook/LinkedIn Title:</strong></label>
                <input type="text" name="_aseo_og_title" value="<?php echo esc_attr($og_title); ?>" style="width: 100%; padding: 8px;" />
            </p>
            <p>
                <label><strong>Facebook/LinkedIn Description:</strong></label>
                <textarea name="_aseo_og_description" style="width: 100%; height: 60px; padding: 8px;"><?php echo esc_attr($og_desc); ?></textarea>
            </p>
            <p>
                <label><strong>Twitter Card Type:</strong></label>
                <select name="_aseo_twitter_card" style="width: 100%; padding: 8px;">
                    <option value="summary" <?php selected($twitter_card, 'summary'); ?>>Summary</option>
                    <option value="summary_large_image" <?php selected($twitter_card, 'summary_large_image'); ?>>Summary with Large Image</option>
                </select>
            </p>
            
            <hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">
            
            <h4>Facebook Preview</h4>
            <div style="padding: 10px; border: 1px solid #ddd; border-radius: 3px; background: #f5f5f5;">
                <strong><?php echo esc_html(substr($og_title, 0, 30)); ?></strong>
                <p style="margin: 5px 0; color: #666; font-size: 12px;"><?php echo esc_html(substr($og_desc, 0, 80)); ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save social data
     */
    public function save_social_data($post_id) {
        if (!isset($_POST['aseo_social_nonce']) || !wp_verify_nonce($_POST['aseo_social_nonce'], 'aseo_social_nonce')) {
            return;
        }
        
        if (isset($_POST['_aseo_og_title'])) {
            update_post_meta($post_id, '_aseo_og_title', sanitize_text_field($_POST['_aseo_og_title']));
        }
        
        if (isset($_POST['_aseo_og_description'])) {
            update_post_meta($post_id, '_aseo_og_description', sanitize_text_field($_POST['_aseo_og_description']));
        }
        
        if (isset($_POST['_aseo_twitter_card'])) {
            update_post_meta($post_id, '_aseo_twitter_card', sanitize_text_field($_POST['_aseo_twitter_card']));
        }
    }
}
