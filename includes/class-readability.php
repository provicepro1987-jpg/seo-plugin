<?php
/**
 * Readability Analysis Class
 */

class ASEO_Readability {
    
    public function __construct() {
        // Readability methods available via helper functions
    }
    
    /**
     * Calculate Flesch Reading Ease Score
     */
    public static function calculate_reading_ease($content) {
        $content = strip_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Count sentences
        $sentences = count(preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY));
        
        // Count words
        $words = count(preg_split('/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY));
        
        // Count syllables
        $syllables = self::count_syllables($content);
        
        if ($words == 0 || $sentences == 0) {
            return 0;
        }
        
        // Flesch Reading Ease formula
        $score = 206.835 - 1.015 * ($words / $sentences) - 84.6 * ($syllables / $words);
        
        return max(0, min(100, round($score, 2)));
    }
    
    /**
     * Count syllables in text
     */
    private static function count_syllables($text) {
        $text = strtolower($text);
        $syllable_count = 0;
        $vowels = 'aeiouy';
        $previous_was_vowel = false;
        
        for ($i = 0; $i < strlen($text); $i++) {
            $is_vowel = strpos($vowels, $text[$i]) !== false;
            
            if ($is_vowel && !$previous_was_vowel) {
                $syllable_count++;
            }
            
            $previous_was_vowel = $is_vowel;
        }
        
        // Adjust for common patterns
        $syllable_count -= max(0, substr_count($text, 'e') / 2);
        
        return max(1, $syllable_count);
    }
    
    /**
     * Get readability score interpretation
     */
    public static function get_readability_interpretation($score) {
        if ($score >= 90) {
            return array('grade' => '5th', 'interpretation' => 'Very Easy');
        } elseif ($score >= 80) {
            return array('grade' => '6th', 'interpretation' => 'Easy');
        } elseif ($score >= 70) {
            return array('grade' => '7th', 'interpretation' => 'Fairly Easy');
        } elseif ($score >= 60) {
            return array('grade' => '8th-9th', 'interpretation' => 'Standard');
        } elseif ($score >= 50) {
            return array('grade' => '10th-12th', 'interpretation' => 'Fairly Difficult');
        } elseif ($score >= 30) {
            return array('grade' => 'College', 'interpretation' => 'Difficult');
        } else {
            return array('grade' => 'College +', 'interpretation' => 'Very Difficult');
        }
    }
    
    /**
     * Analyze content readability
     */
    public static function analyze_readability($content) {
        $analysis = array(
            'word_count' => self::count_words($content),
            'sentence_count' => self::count_sentences($content),
            'avg_words_per_sentence' => 0,
            'reading_ease_score' => self::calculate_reading_ease($content),
            'avg_word_length' => self::calculate_avg_word_length($content)
        );
        
        if ($analysis['sentence_count'] > 0) {
            $analysis['avg_words_per_sentence'] = round($analysis['word_count'] / $analysis['sentence_count'], 2);
        }
        
        return $analysis;
    }
    
    /**
     * Count words
     */
    private static function count_words($text) {
        $text = strip_tags($text);
        return count(preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY));
    }
    
    /**
     * Count sentences
     */
    private static function count_sentences($text) {
        $text = strip_tags($text);
        return count(preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY));
    }
    
    /**
     * Calculate average word length
     */
    private static function calculate_avg_word_length($text) {
        $text = strip_tags($text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        if (count($words) == 0) {
            return 0;
        }
        
        $total_length = 0;
        foreach ($words as $word) {
            $total_length += strlen(preg_replace('/[^a-z]/i', '', $word));
        }
        
        return round($total_length / count($words), 2);
    }
}
