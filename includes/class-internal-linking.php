<?php
/**
 * Internal Linking Suggestions Class
 */

class ASEO_Internal_Linking {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
    }
    
    /**
     * Add meta box to post editor
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_internal_links',
            'Internal Linking Suggestions',
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
        $content = $post->post_content;
        $keywords = $this->extract_keywords($content);
        $suggestions = $this->get_internal_link_suggestions($keywords, $post->ID);
        
        ?>
        <div style="padding: 10px;">
            <p><em>Suggested pages to link to:</em></p>
            <?php if (!empty($suggestions)) : ?>
                <ul style="list-style: none; padding: 0;">
                    <?php foreach ($suggestions as $post_id => $title) : ?>
                        <li style="padding: 5px 0; border-bottom: 1px solid #eee;">
                            <a href="<?php echo get_permalink($post_id); ?>" target="_blank"><?php echo esc_html($title); ?></a>
                            <small style="display: block; color: #666;">ID: <?php echo $post_id; ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p style="color: #999; font-style: italic;">No suggestions at this time.</p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Extract keywords from content
     */
    private function extract_keywords($content) {
        $keywords = array();
        
        // Remove HTML tags
        $content = strip_tags($content);
        
        // Split into words
        $words = preg_split('/[\s\.,;!?]+/', strtolower($content), -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter common words
        $stop_words = array('the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for');
        $filtered = array_filter($words, function($word) use ($stop_words) {
            return strlen($word) > 4 && !in_array($word, $stop_words);
        });
        
        $word_freq = array_count_values($filtered);
        arsort($word_freq);
        
        return array_slice(array_keys($word_freq), 0, 5);
    }
    
    /**
     * Get internal link suggestions
     */
    private function get_internal_link_suggestions($keywords, $current_post_id) {
        $suggestions = array();
        
        foreach ($keywords as $keyword) {
            $posts = get_posts(array(
                's' => $keyword,
                'post_type' => array('post', 'page'),
                'post_status' => 'publish',
                'posts_per_page' => 3,
                'exclude' => $current_post_id
            ));
            
            foreach ($posts as $post) {
                if (count($suggestions) >= 5) break;
                $suggestions[$post->ID] = $post->post_title;
            }
        }
        
        return $suggestions;
    }
}
