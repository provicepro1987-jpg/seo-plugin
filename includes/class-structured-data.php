<?php
/**
 * Structured Data (Schema.org) Handler Class
 */

class ASEO_Structured_Data {
    
    public function __construct() {
        add_action('wp_head', array($this, 'add_structured_data'));
    }
    
    /**
     * Add structured data to frontend
     */
    public function add_structured_data() {
        if (get_option('aseo_enable_structured_data')) {
            if (is_singular('post')) {
                echo $this->get_article_schema();
            } elseif (is_home() || is_archive()) {
                echo $this->get_organization_schema();
            }
        }
    }
    
    /**
     * Get Article Schema
     */
    private function get_article_schema() {
        $post_id = get_the_ID();
        $post = get_post($post_id);
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => get_the_title($post_id),
            'description' => wp_trim_words(get_the_excerpt($post_id), 20),
            'image' => array(
                '@type' => 'ImageObject',
                'url' => has_post_thumbnail($post_id) ? wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full')[0] : '',
                'width' => 1200,
                'height' => 630
            ),
            'datePublished' => gmdate('Y-m-d\TH:i:sZ', strtotime($post->post_date)),
            'dateModified' => gmdate('Y-m-d\TH:i:sZ', strtotime($post->post_modified)),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $post->post_author)
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_option('aseo_company_name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_option('aseo_company_logo'),
                    'width' => 600,
                    'height' => 60
                )
            )
        );
        
        return '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
    }
    
    /**
     * Get Organization Schema
     */
    private function get_organization_schema() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_option('aseo_company_name'),
            'url' => home_url(),
            'logo' => get_option('aseo_company_logo'),
            'sameAs' => array(
                'https://www.facebook.com/yourpage',
                'https://www.twitter.com/yourpage',
                'https://www.instagram.com/yourpage'
            )
        );
        
        return '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
    }
}
