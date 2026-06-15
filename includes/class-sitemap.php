<?php
/**
 * XML Sitemap Generator Class
 */

class ASEO_Sitemap {
    
    public function __construct() {
        add_action('init', array($this, 'register_sitemap_rewrite'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'generate_sitemap'));
    }
    
    /**
     * Register rewrite rule for sitemap
     */
    public function register_sitemap_rewrite() {
        add_rewrite_rule(
            'sitemap\.xml$',
            'index.php?aseo_sitemap=1',
            'top'
        );
    }
    
    /**
     * Add query variables
     */
    public function add_query_vars($vars) {
        $vars[] = 'aseo_sitemap';
        return $vars;
    }
    
    /**
     * Generate and output sitemap
     */
    public function generate_sitemap() {
        if (get_query_var('aseo_sitemap')) {
            header('Content-Type: application/xml; charset=UTF-8');
            echo $this->build_sitemap();
            exit;
        }
    }
    
    /**
     * Build XML sitemap
     */
    private function build_sitemap() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Add homepage
        $xml .= $this->add_url(home_url(), gmdate('Y-m-d'), '1.0', 'weekly');
        
        // Add posts
        $posts = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'numberposts' => -1,
            'suppress_filters' => false
        ));
        
        foreach ($posts as $post) {
            $priority = $post->post_type === 'post' ? '0.8' : '0.9';
            $xml .= $this->add_url(
                get_permalink($post),
                gmdate('Y-m-d', strtotime($post->post_modified)),
                $priority,
                'weekly'
            );
        }
        
        // Add taxonomies
        $categories = get_categories(array('hide_empty' => true));
        foreach ($categories as $cat) {
            $xml .= $this->add_url(get_category_link($cat), gmdate('Y-m-d'), '0.7', 'weekly');
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Add URL to sitemap
     */
    private function add_url($location, $lastmod, $priority, $changefreq) {
        return sprintf(
            "\t<url>\n\t\t<loc>%s</loc>\n\t\t<lastmod>%s</lastmod>\n\t\t<changefreq>%s</changefreq>\n\t\t<priority>%s</priority>\n\t</url>\n",
            esc_url($location),
            esc_xml($lastmod),
            esc_xml($changefreq),
            esc_xml($priority)
        );
    }
}

/**
 * Escape XML special characters
 */
if (!function_exists('esc_xml')) {
    function esc_xml($text) {
        $text = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $text);
        return $text;
    }
}
