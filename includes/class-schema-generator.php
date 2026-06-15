<?php
/**
 * Advanced Schema Generator Class
 */

class ASEO_Schema_Generator {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_schema'));
        add_action('wp_head', array($this, 'output_schema'));
    }
    
    /**
     * Add meta box
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_schema',
            'Schema Markup',
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
        $schema_type = get_post_meta($post->ID, '_aseo_schema_type', true);
        $schema_data = get_post_meta($post->ID, '_aseo_schema_data', true);
        
        wp_nonce_field('aseo_schema_nonce', 'aseo_schema_nonce');
        
        ?>
        <p>
            <label><strong>Schema Type:</strong></label>
            <select name="_aseo_schema_type" style="width: 100%; padding: 8px;">
                <option value="">None</option>
                <option value="article" <?php selected($schema_type, 'article'); ?>>Article</option>
                <option value="product" <?php selected($schema_type, 'product'); ?>>Product</option>
                <option value="faq" <?php selected($schema_type, 'faq'); ?>>FAQ</option>
                <option value="event" <?php selected($schema_type, 'event'); ?>>Event</option>
                <option value="recipe" <?php selected($schema_type, 'recipe'); ?>>Recipe</option>
                <option value="local_business" <?php selected($schema_type, 'local_business'); ?>>Local Business</option>
            </select>
        </p>
        
        <?php if ($schema_type === 'product') : ?>
            <p>
                <label><strong>Product Name:</strong></label>
                <input type="text" name="_aseo_schema_name" value="" style="width: 100%; padding: 8px;" />
            </p>
            <p>
                <label><strong>Product Price:</strong></label>
                <input type="number" name="_aseo_schema_price" value="" step="0.01" style="width: 100%; padding: 8px;" />
            </p>
            <p>
                <label><strong>Currency:</strong></label>
                <input type="text" name="_aseo_schema_currency" value="USD" style="width: 100%; padding: 8px;" />
            </p>
        <?php elseif ($schema_type === 'recipe') : ?>
            <p>
                <label><strong>Recipe Name:</strong></label>
                <input type="text" name="_aseo_schema_name" value="" style="width: 100%; padding: 8px;" />
            </p>
            <p>
                <label><strong>Prep Time (minutes):</strong></label>
                <input type="number" name="_aseo_schema_prep_time" value="" style="width: 100%; padding: 8px;" />
            </p>
            <p>
                <label><strong>Cook Time (minutes):</strong></label>
                <input type="number" name="_aseo_schema_cook_time" value="" style="width: 100%; padding: 8px;" />
            </p>
        <?php endif; ?>
        <?php
    }
    
    /**
     * Save schema
     */
    public function save_schema($post_id) {
        if (!isset($_POST['aseo_schema_nonce']) || !wp_verify_nonce($_POST['aseo_schema_nonce'], 'aseo_schema_nonce')) {
            return;
        }
        
        if (isset($_POST['_aseo_schema_type'])) {
            update_post_meta($post_id, '_aseo_schema_type', sanitize_text_field($_POST['_aseo_schema_type']));
        }
    }
    
    /**
     * Output schema
     */
    public function output_schema() {
        if (is_singular()) {
            $post_id = get_the_ID();
            $schema_type = get_post_meta($post_id, '_aseo_schema_type', true);
            
            if (!empty($schema_type)) {
                echo $this->generate_schema($post_id, $schema_type);
            }
        }
    }
    
    /**
     * Generate schema based on type
     */
    private function generate_schema($post_id, $type) {
        $schema = array();
        
        switch ($type) {
            case 'product':
                $schema = $this->get_product_schema($post_id);
                break;
            case 'faq':
                $schema = $this->get_faq_schema($post_id);
                break;
            case 'event':
                $schema = $this->get_event_schema($post_id);
                break;
            case 'recipe':
                $schema = $this->get_recipe_schema($post_id);
                break;
            case 'local_business':
                $schema = $this->get_local_business_schema();
                break;
        }
        
        return !empty($schema) ? '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n" : '';
    }
    
    /**
     * Get product schema
     */
    private function get_product_schema($post_id) {
        return array(
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => get_the_title($post_id),
            'description' => wp_trim_words(get_the_excerpt($post_id), 50),
            'image' => has_post_thumbnail($post_id) ? wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full')[0] : '',
            'offers' => array(
                '@type' => 'Offer',
                'url' => get_permalink($post_id),
                'priceCurrency' => 'USD',
                'price' => '0.00',
                'availability' => 'https://schema.org/InStock'
            )
        );
    }
    
    /**
     * Get FAQ schema
     */
    private function get_faq_schema($post_id) {
        return array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array(
                '@type' => 'Question',
                'name' => get_the_title($post_id),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => wp_trim_words(get_the_excerpt($post_id), 50)
                )
            )
        );
    }
    
    /**
     * Get event schema
     */
    private function get_event_schema($post_id) {
        return array(
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => get_the_title($post_id),
            'description' => wp_trim_words(get_the_excerpt($post_id), 50),
            'startDate' => gmdate('Y-m-d'),
            'endDate' => gmdate('Y-m-d'),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OnlineEventAttendanceMode',
            'organizer' => array(
                '@type' => 'Organization',
                'name' => get_option('aseo_company_name')
            )
        );
    }
    
    /**
     * Get recipe schema
     */
    private function get_recipe_schema($post_id) {
        return array(
            '@context' => 'https://schema.org/',
            '@type' => 'Recipe',
            'name' => get_the_title($post_id),
            'description' => wp_trim_words(get_the_excerpt($post_id), 50),
            'prepTime' => 'PT0M',
            'cookTime' => 'PT0M',
            'totalTime' => 'PT0M',
            'recipeYield' => '4',
            'recipeCategory' => 'Lunch'
        );
    }
    
    /**
     * Get local business schema
     */
    private function get_local_business_schema() {
        return array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => get_option('aseo_company_name'),
            'image' => get_option('aseo_company_logo'),
            'url' => home_url(),
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => 'Your Street Address',
                'addressLocality' => 'Your City',
                'addressRegion' => 'Your State',
                'postalCode' => '00000'
            )
        );
    }
}
