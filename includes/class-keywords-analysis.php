<?php
/**
 * Keywords Analysis Class
 */

class ASEO_Keywords_Analysis {
    
    public function __construct() {
        // Analysis methods available via helper functions
    }
    
    /**
     * Analyze keyword density in content
     */
    public static function analyze_keyword_density($content, $keyword) {
        if (empty($keyword) || empty($content)) {
            return 0;
        }
        
        $keyword_lower = strtolower($keyword);
        $content_lower = strtolower($content);
        
        // Remove HTML tags
        $content_clean = strip_tags($content_lower);
        
        // Count keyword occurrences
        $keyword_count = substr_count($content_clean, $keyword_lower);
        
        // Count total words
        $words = str_word_count($content_clean);
        
        if ($words == 0) {
            return 0;
        }
        
        $density = ($keyword_count / $words) * 100;
        
        return round($density, 2);
    }
    
    /**
     * Get keyword suggestions
     */
    public static function get_keyword_suggestions($content) {
        $content = strtolower($content);
        $content = strip_tags($content);
        
        // Remove common stop words
        $stop_words = array(
            'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 
            'of', 'with', 'by', 'from', 'is', 'are', 'was', 'were', 'be', 'been',
            'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would',
            'could', 'should', 'may', 'might', 'must', 'can', 'this', 'that'
        );
        
        // Split into words
        $words = preg_split('/[\s\.,;!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter stop words and short words
        $filtered = array_filter($words, function($word) use ($stop_words) {
            return strlen($word) > 3 && !in_array($word, $stop_words);
        });
        
        // Count word frequency
        $word_freq = array_count_values($filtered);
        
        // Sort by frequency
        arsort($word_freq);
        
        // Return top 10
        return array_slice($word_freq, 0, 10, true);
    }
    
    /**
     * Check keyword in headings
     */
    public static function check_keyword_in_headings($content, $keyword) {
        preg_match_all('/<h[1-6][^>]*>([^<]*)<\/h[1-6]>/i', $content, $headings);
        
        foreach ($headings[1] as $heading) {
            if (stripos($heading, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
