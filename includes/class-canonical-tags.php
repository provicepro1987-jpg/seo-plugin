<?php
/**
 * Canonical Tags Handler Class
 */

class ASEO_Canonical_Tags {
    
    public function __construct() {
        add_action('wp_head', array($this, 'add_canonical_tag'));
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_canonical'));
    }
    
    /**
     * Add canonical tag to frontend
     */
    public function add_canonical_tag() {
        if (is_singular()) {
            $post_id = get_the_ID();
            $canonical = get_post_meta($post_id, '_aseo_canonical', true);
            
            if (empty($canonical)) {
                $canonical = get_permalink($post_id);
            }
            
            echo '<link rel="canonical" href="' . esc_url($canonical) . '" />' . "\n";
        }
    }
    
    /**
     * Add meta box
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_canonical',
            'Canonical URL',
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
        $canonical = get_post_meta($post->ID, '_aseo_canonical', true);
        $default = get_permalink($post->ID);
        
        wp_nonce_field('aseo_canonical_nonce', 'aseo_canonical_nonce');
        
        ?>
        <p>
            <label><strong>Custom Canonical URL:</strong></label>
            <input type="url" name="_aseo_canonical" value="<?php echo esc_url($canonical); ?>" style="width: 100%; padding: 8px;" />
            <small>Leave empty to use default: <?php echo esc_url($default); ?></small>
        </p>
        <?php
    }
    
    /**
     * Save canonical
     */
    public function save_canonical($post_id) {
        if (!isset($_POST['aseo_canonical_nonce']) || !wp_verify_nonce($_POST['aseo_canonical_nonce'], 'aseo_canonical_nonce')) {
            return;
        }
        
        if (isset($_POST['_aseo_canonical'])) {
            update_post_meta($post_id, '_aseo_canonical', esc_url_raw($_POST['_aseo_canonical']));
        }
    }
}
