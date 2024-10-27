<?php if (!defined('ABSPATH')) exit; ?>

<?php
    
    $campaigns = AdPage::campaigns();
    
    $connected_campaigns = [];
    
    foreach ($wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'adpage') as $campaign) {
        
        $connected_campaigns[] = $campaign->hash;
        
    }
    
?>

<div class="adpgc">
    
    <?php include __DIR__ . '/inc/header.inc.php'; ?>
    
    <div class="adpgc-wrapper">
        
        <div class="adpgc-container">
            
            <div class="adpgc-campaigns">
                
                <?php foreach ($campaigns as $campaign) { ?>
                        
                    <div class="adpgc-campaign" data-campaign="<?= $campaign->hash; ?>">
                        
                        <div class="adpgc-meta">
                        
                            <strong>
                                <?= $campaign->name; ?>
                            </strong>
                            
                            <?php if (in_array($campaign->hash, $connected_campaigns)) { ?>
                            
                                <?php
                                    
                                    $campaign_connected_slug = $wpdb->get_var('SELECT slug FROM ' . $wpdb->prefix . 'adpage WHERE hash = "' . $campaign->hash . '"');
                                    $campaign_connected_slug = site_url() . '/' . $campaign_connected_slug;
                                    
                                ?>
                                <a href="<?= $campaign_connected_slug; ?>" target="_blank" class="adpgc-meta-link"><?= $campaign_connected_slug; ?></a>
                            
                            <?php } ?>
                            
                            <?php if ($campaign->isPublished == true) { ?>
                                <span class="meta-green">
                                    <b class="dashicons dashicons-yes"></b> Published
                                </span>
                            <?php } else { ?>
                                <span>
                                    <b class="dashicons dashicons-no-alt"></b> Unpublished
                                </span>
                            <?php } ?>
                            
                            <?php if (in_array($campaign->hash, $connected_campaigns)) { ?>
                                <span class="meta-green">
                                    <b class="dashicons dashicons-yes"></b> Connected
                                </span>
                            <?php } else { ?>
                                <span>
                                    <b class="dashicons dashicons-no-alt"></b> Not connected
                                </span>
                            <?php } ?>
                                
                        </div>
                        
                        <div class="adpgc-actions">
                            
                            <ul>
                                
                                <li>
                                    <a href="<?= $campaign->domains[0]->domain; ?>" target="_blank">
                                        <img src="<?= $adpgc_config['PLUGIN_ENDPOINT'] . '/assets/images/campaign-view.svg'; ?>" />
                                        <span>View</span>
                                    </a>
                                </li>
                                <?php if (in_array($campaign->hash, $connected_campaigns)) { ?>
                                    <li>
                                        <a href="<?= $campaign->hash; ?>" class="disconnect-campaign">
                                            <img src="<?= $adpgc_config['PLUGIN_ENDPOINT'] . '/assets/images/campaign-disconnect.svg'; ?>" />
                                            <span>Disconnect</span>
                                        </a>
                                    </li>
                                <?php } elseif ($campaign->isPublished == true) { ?>
                                    <li>
                                        <a href="<?= $campaign->hash; ?>" class="connect-campaign">
                                            <img src="<?= $adpgc_config['PLUGIN_ENDPOINT'] . '/assets/images/campaign-connect.svg'; ?>" />
                                            <span>Connect</span>
                                        </a>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <a href="https://app.adpage.io/main/campaign/<?= $campaign->hash; ?>/publish?ref=adpage-connect" target="_blank">
                                            <img src="<?= $adpgc_config['PLUGIN_ENDPOINT'] . '/assets/images/campaign-publish.svg'; ?>" />
                                            <span>Publish</span>
                                        </a>
                                    </li>
                                <?php } ?>
                                
                            </ul>
                            
                        </div>
                        
                    </div>
                        
                <?php } ?>
            
            </div>
            
            <div class="adpgc-modals" style="display: none;">
                
                <?php foreach ($campaigns as $campaign) { ?>
                
                    <?php if ($campaign->isPublished == true) { ?>
                
                        <div class="adpgc-modal" data-campaign="<?= $campaign->hash; ?>" style="display: none;">
                            
                            <div class="adpgc-modal-header">
                                <?= $campaign->name; ?>
                                <span class="adpgc-modal-close">
                                    <b class="dashicons dashicons-no-alt"></b>
                                </span>
                            </div>
                            
                            <div class="adpgc-modal-content">
                                
                                <form method="post" action="/adpgc/connect">
                                    
                                    <p class="slug-preview">
                                        <?= site_url(); ?>/<span>test-slug</span>
                                    </p>
                                    
                                    <input type="hidden" name="hash" value="<?= $campaign->hash; ?>" />
                                    <input type="hidden" name="title" value="<?= $campaign->name; ?>" />
                                    <input type="hidden" name="domain" value="<?= $campaign->domains[0]->domain; ?>" />
                                    
                                    <input type="text" name="slug" value="" placeholder="test-slug" />
                                    
                                    <input type="submit" value="Connect" />
                                    
                                </form>
                                
                            </div>
                            
                        </div>
                        
                    <?php } ?>
                    
                <?php } ?>
                
            </div>
            
        </div>
        
    </div>

    <?php include __DIR__ . '/inc/footer.inc.php'; ?>
    
</div>