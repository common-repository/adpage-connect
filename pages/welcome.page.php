<?php if (!defined('ABSPATH')) exit; ?>

<div class="adpgc">
    
    <?php include __DIR__ . '/inc/header.inc.php'; ?>
    
    <div class="adpgc-wrapper">
        
        <div class="adpgc-container">
            
            <div class="adpgc-welcome">
                
                <img src="<?= $adpgc_config['PLUGIN_ENDPOINT'] . '/assets/images/welcome.svg'; ?>" />
                
                <h1>Welcome to AdPage Connect!</h1>
                
                <p>
                    You're almost ready to publish your awesome campaigns to your WordPress-website. 
                    There's only one thing left to do. We need your AdPage API-key in order to get started. 
                    Please go to your <a href="https://app.adpage.io/main/settings/api" target="_blank">dashboard</a> to generate one.
                </p>
                
                <a href="<?= get_admin_url(null, 'options-general.php'); ?>" class="button">Set API-key</a>
                
            </div>
            
        </div>
        
    </div>

    <?php include __DIR__ . '/inc/footer.inc.php'; ?>
    
</div>