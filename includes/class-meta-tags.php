<?php
/**
 * Meta Tags Handler Class
 */

class ASEO_Meta_Tags {
    
    public function __construct() {
        add_action('wp_head', array($this, 'add_meta_tags'));
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_meta_box'));
    }
    
    /**
     * Add meta tags to frontend
     */
    public function add_meta_tags() {
        if (is_singular()) {
            $post_id = get_the_ID();
            
            // Get custom meta tags
            $meta_title = get_post_meta($post_id, '_aseo_meta_title', true);
            $meta_description = get_post_meta($post_id, '_aseo_meta_description', true);
            $focus_keyword = get_post_meta($post_id, '_aseo_focus_keyword', true);
            
            // Fallback to default if not set
            if (empty($meta_title)) {
                $meta_title = get_the_title($post_id);
            }
            if (empty($meta_description)) {
                $meta_description = wp_trim_words(get_the_excerpt($post_id), 20);
            }
            
            // Output meta tags
            echo '<meta name="description" content="' . esc_attr($meta_description) . '" />' . "\n";
            echo '<meta name="keywords" content="' . esc_attr($focus_keyword) . '" />' . "\n";
            
            // Open Graph Tags
            echo '<meta property="og:title" content="' . esc_attr($meta_title) . '" />' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($meta_description) . '" />' . "\n";
            echo '<meta property="og:type" content="article" />' . "\n";
            echo '<meta property="og:url" content="' . esc_url(get_permalink($post_id)) . '" />' . "\n";
            
            // Featured image for OG
            if (has_post_thumbnail($post_id)) {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
                echo '<meta property="og:image" content="' . esc_url($image[0]) . '" />' . "\n";
            }
            
            // Twitter Card
            echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
            echo '<meta name="twitter:title" content="' . esc_attr($meta_title) . '" />' . "\n";
            echo '<meta name="twitter:description" content="' . esc_attr($meta_description) . '" />' . "\n";
            
            // Robots meta tags
            $noindex = get_post_meta($post_id, '_aseo_noindex', true);
            $nofollow = get_post_meta($post_id, '_aseo_nofollow', true);
            
            $robots = [];
            if (!$noindex) $robots[] = 'index';
            else $robots[] = 'noindex';
            if (!$nofollow) $robots[] = 'follow';
            else $robots[] = 'nofollow';
            
            echo '<meta name="robots" content="' . esc_attr(implode(', ', $robots)) . '" />' . "\n";
        }
    }
    
    /**
     * Add meta box to post editor
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_meta_box',
            'SEO Settings',
            array($this, 'render_meta_box'),
            array('post', 'page'),
            'normal',
            'high'
        );
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        $meta_title = get_post_meta($post->ID, '_aseo_meta_title', true);
        $meta_description = get_post_meta($post->ID, '_aseo_meta_description', true);
        $focus_keyword = get_post_meta($post->ID, '_aseo_focus_keyword', true);
        $noindex = get_post_meta($post->ID, '_aseo_noindex', true);
        $nofollow = get_post_meta($post->ID, '_aseo_nofollow', true);
        
        wp_nonce_field('aseo_meta_box_nonce', 'aseo_meta_box_nonce');
        
        ?>
        <div style="padding: 10px;">
            <p>
                <label><strong>Meta Title (60 characters recommended):</strong></label>
                <input type="text" name="_aseo_meta_title" value="<?php echo esc_attr($meta_title); ?>" style="width: 100%; padding: 8px;" />
                <small>Current length: <span id="title-length">0</span>/60</small>
            </p>
            
            <p>
                <label><strong>Meta Description (160 characters recommended):</strong></label>
                <textarea name="_aseo_meta_description" style="width: 100%; height: 80px; padding: 8px;"><?php echo esc_attr($meta_description); ?></textarea>
                <small>Current length: <span id="desc-length">0</span>/160</small>
            </p>
            
            <p>
                <label><strong>Focus Keyword:</strong></label>
                <input type="text" name="_aseo_focus_keyword" value="<?php echo esc_attr($focus_keyword); ?>" style="width: 100%; padding: 8px;" />
            </p>
            
            <p>
                <label><input type="checkbox" name="_aseo_noindex" <?php checked($noindex, 1); ?> value="1" /> Block search engines from indexing</label>
            </p>
            
            <p>
                <label><input type="checkbox" name="_aseo_nofollow" <?php checked($nofollow, 1); ?> value="1" /> Block search engines from following links</label>
            </p>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var titleInput = document.querySelector('[name="_aseo_meta_title"]');
            var descInput = document.querySelector('[name="_aseo_meta_description"]');
            
            if (titleInput) {
                titleInput.addEventListener('keyup', function() {
                    document.getElementById('title-length').textContent = this.value.length;
                });
                titleInput.dispatchEvent(new Event('keyup'));
            }
            
            if (descInput) {
                descInput.addEventListener('keyup', function() {
                    document.getElementById('desc-length').textContent = this.value.length;
                });
                descInput.dispatchEvent(new Event('keyup'));
            }
        });
        </script>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_box($post_id) {
        if (!isset($_POST['aseo_meta_box_nonce']) || !wp_verify_nonce($_POST['aseo_meta_box_nonce'], 'aseo_meta_box_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save meta fields
        if (isset($_POST['_aseo_meta_title'])) {
            update_post_meta($post_id, '_aseo_meta_title', sanitize_text_field($_POST['_aseo_meta_title']));
        }
        
        if (isset($_POST['_aseo_meta_description'])) {
            update_post_meta($post_id, '_aseo_meta_description', sanitize_text_field($_POST['_aseo_meta_description']));
        }
        
        if (isset($_POST['_aseo_focus_keyword'])) {
            update_post_meta($post_id, '_aseo_focus_keyword', sanitize_text_field($_POST['_aseo_focus_keyword']));
        }
        
        update_post_meta($post_id, '_aseo_noindex', isset($_POST['_aseo_noindex']) ? 1 : 0);
        update_post_meta($post_id, '_aseo_nofollow', isset($_POST['_aseo_nofollow']) ? 1 : 0);
    }
}
