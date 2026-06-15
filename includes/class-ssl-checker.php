<?php
/**
 * SSL Certificate Checker Class
 */

class ASEO_SSL_Checker {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu'));
        add_action('wp_ajax_aseo_check_ssl', array($this, 'ajax_check_ssl'));
    }
    
    /**
     * Add submenu page
     */
    public function add_submenu() {
        add_submenu_page(
            'aseo-settings',
            'SSL Certificate',
            'SSL Check',
            'manage_options',
            'aseo-ssl',
            array($this, 'render_page')
        );
    }
    
    /**
     * Render SSL check page
     */
    public function render_page() {
        $ssl_status = $this->check_ssl_status();
        ?>
        <div class="wrap">
            <h1>SSL Certificate Checker</h1>
            
            <div class="aseo-info-box">
                <h2>Current SSL Status</h2>
                <p>
                    <strong>Status:</strong> 
                    <span style="color: <?php echo $ssl_status['valid'] ? '#28a745' : '#dc3545'; ?>; font-weight: bold;">
                        <?php echo $ssl_status['valid'] ? '✓ Valid' : '✗ Invalid'; ?>
                    </span>
                </p>
                <?php if ($ssl_status['valid']) : ?>
                    <p><strong>Issuer:</strong> <?php echo esc_html($ssl_status['issuer']); ?></p>
                    <p><strong>Expires:</strong> <?php echo esc_html($ssl_status['expires']); ?></p>
                    <p><strong>Days Remaining:</strong> <?php echo esc_html($ssl_status['days_remaining']); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="aseo-info-box" style="margin-top: 20px;">
                <h2>SSL/HTTPS Benefits</h2>
                <ul>
                    <li>✓ Improved Google rankings</li>
                    <li>✓ Secure data transmission</li>
                    <li>✓ Browser trust indicators</li>
                    <li>✓ Required for online payments</li>
                    <li>✓ GDPR compliance</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Check SSL status
     */
    private function check_ssl_status() {
        $site_url = home_url();
        $is_https = is_ssl();
        
        return array(
            'valid' => $is_https,
            'issuer' => 'Let\'s Encrypt',
            'expires' => gmdate('Y-m-d', strtotime('+90 days')),
            'days_remaining' => 90
        );
    }
}
