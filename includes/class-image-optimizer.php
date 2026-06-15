<?php
/**
 * Image Optimization Class
 */

class ASEO_Image_Optimizer {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
    }
    
    /**
     * Add meta box to post editor
     */
    public function add_meta_box() {
        add_meta_box(
            'aseo_image_optimization',
            'Image Optimization',
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
        $analysis = $this->analyze_images($post->post_content);
        
        ?>
        <div style="padding: 10px;">
            <h4 style="margin-top: 0;">Image Analysis</h4>
            <p><strong>Total Images:</strong> <?php echo $analysis['total']; ?></p>
            <p><strong>Images with Alt Text:</strong> <?php echo $analysis['with_alt']; ?>/<?php echo $analysis['total']; ?></p>
            <p><strong>Missing Alt Text:</strong> <span style="color: <?php echo $analysis['missing_alt'] > 0 ? '#d32f2f' : '#28a745'; ?>;"><?php echo $analysis['missing_alt']; ?></span></p>
            
            <?php if ($analysis['missing_alt'] > 0) : ?>
                <p style="color: #ff9800; font-size: 12px; margin: 10px 0; padding: 8px; background: #fff3e0; border-radius: 3px;">
                    ⚠️ Add alt text to <?php echo $analysis['missing_alt']; ?> image(s) for better SEO.
                </p>
            <?php endif; ?>
            
            <hr style="margin: 10px 0; border: none; border-top: 1px solid #eee;">
            
            <h4>Image Optimization Tips</h4>
            <ul style="font-size: 12px; padding-left: 15px;">
                <li>Use descriptive alt text</li>
                <li>Optimize file sizes</li>
                <li>Use modern formats (WebP)</li>
                <li>Use descriptive filenames</li>
                <li>Add captions when relevant</li>
            </ul>
        </div>
        <?php
    }
    
    /**
     * Analyze images in content
     */
    private function analyze_images($content) {
        $images = array('total' => 0, 'with_alt' => 0, 'missing_alt' => 0);
        
        preg_match_all('/<img[^>]+>/i', $content, $img_tags);
        $images['total'] = count($img_tags[0]);
        
        foreach ($img_tags[0] as $img) {
            if (preg_match('/alt=["\']([^"\']*)["\']/i', $img)) {
                $images['with_alt']++;
            } else {
                $images['missing_alt']++;
            }
        }
        
        return $images;
    }
}
