<?php
/**
 * Breadcrumb Navigation Class
 */

class ASEO_Breadcrumbs {
    
    public function __construct() {
        add_action('wp_head', array($this, 'output_breadcrumb_schema'));
    }
    
    /**
     * Output breadcrumb schema
     */
    public function output_breadcrumb_schema() {
        if (is_singular() || is_category() || is_tag()) {
            echo $this->get_breadcrumb_schema();
        }
    }
    
    /**
     * Get breadcrumb schema
     */
    private function get_breadcrumb_schema() {
        $breadcrumbs = array(
            array(
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => home_url()
            )
        );
        
        $position = 2;
        
        if (is_singular()) {
            $post = get_post();
            $categories = get_the_category($post->ID);
            
            if (!empty($categories)) {
                $cat = $categories[0];
                $breadcrumbs[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $cat->name,
                    'item' => get_category_link($cat->term_id)
                );
            }
            
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => get_the_title($post->ID),
                'item' => get_permalink($post->ID)
            );
        } elseif (is_category()) {
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => single_cat_title('', false),
                'item' => get_category_link(get_queried_object()->term_id)
            );
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        );
        
        return '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
    }
}
