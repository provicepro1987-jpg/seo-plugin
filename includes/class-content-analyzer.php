<?php
/**
 * Advanced Content Analyzer Class
 */

class ASEO_Content_Analyzer {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
    }
    
    /**
     * Add meta box
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_content_analysis',
            'Content Analysis',
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
        $analysis = $this->analyze_content($post->post_content);
        
        ?>
        <div style="padding: 10px;">
            <table style="width: 100%;">
                <tr>
                    <td><strong>Word Count:</strong></td>
                    <td style="text-align: right;"><?php echo $analysis['word_count']; ?> words</td>
                </tr>
                <tr>
                    <td><strong>Paragraph Count:</strong></td>
                    <td style="text-align: right;"><?php echo $analysis['paragraphs']; ?></td>
                </tr>
                <tr>
                    <td><strong>Heading Count:</strong></td>
                    <td style="text-align: right;"><?php echo $analysis['headings']; ?></td>
                </tr>
                <tr>
                    <td><strong>Image Count:</strong></td>
                    <td style="text-align: right;"><?php echo $analysis['images']; ?></td>
                </tr>
                <tr>
                    <td><strong>Links (Internal):</strong></td>
                    <td style="text-align: right;"><?php echo $analysis['internal_links']; ?></td>
                </tr>
                <tr>
                    <td><strong>Links (External):</strong></td>
                    <td style="text-align: right;"><?php echo $analysis['external_links']; ?></td>
                </tr>
                <tr>
                    <td><strong>Readability Score:</strong></td>
                    <td style="text-align: right;"><?php echo $analysis['readability_score']; ?>/100</td>
                </tr>
                <tr>
                    <td><strong>Average Sentence Length:</strong></td>
                    <td style="text-align: right;"><?php echo $analysis['avg_sentence_length']; ?> words</td>
                </tr>
            </table>
            
            <?php if ($analysis['word_count'] < 300) : ?>
                <p style="margin-top: 10px; padding: 8px; background: #fff3e0; border-left: 4px solid #ff9800; color: #333;">
                    ⚠️ Content is less than 300 words. Consider adding more content for better SEO.
                </p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Analyze content
     */
    private function analyze_content($content) {
        $clean_content = strip_tags($content);
        
        $word_count = str_word_count($clean_content);
        $sentences = count(preg_split('/[.!?]+/', $clean_content, -1, PREG_SPLIT_NO_EMPTY));
        $paragraphs = substr_count($content, '</p>');
        
        preg_match_all('/<h[1-6][^>]*>/i', $content, $headings);
        $heading_count = count($headings[0]);
        
        preg_match_all('/<img[^>]+>/i', $content, $images);
        $image_count = count($images[0]);
        
        preg_match_all('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $content, $links);
        $internal_links = 0;
        $external_links = 0;
        
        $site_url = home_url();
        foreach ($links[1] as $link) {
            if (strpos($link, $site_url) !== false || strpos($link, '/') === 0) {
                $internal_links++;
            } else if (strpos($link, 'http') === 0) {
                $external_links++;
            }
        }
        
        $readability = ASEO_Readability::calculate_reading_ease($content);
        $avg_sentence = $sentences > 0 ? round($word_count / $sentences, 1) : 0;
        
        return array(
            'word_count' => $word_count,
            'sentences' => $sentences,
            'paragraphs' => $paragraphs,
            'headings' => $heading_count,
            'images' => $image_count,
            'internal_links' => $internal_links,
            'external_links' => $external_links,
            'readability_score' => round($readability, 0),
            'avg_sentence_length' => $avg_sentence
        );
    }
}
