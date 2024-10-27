<?php
    
    if (!defined('ABSPATH')) exit;
    
    $campaign_slug = explode('/', $wp->request);
    
    $campaign_request = $wpdb->get_var('SELECT domain FROM ' . $wpdb->prefix . 'adpage' . ' WHERE slug = "' . $campaign_slug[0] . '" LIMIT 1');
    
    if ($campaign_request !== null) {
        
        if (!isset($campaign_slug[1])) {
            
            $campaign_domain = $campaign_request;
            
        }
        else {
            
            $campaign_domain = $campaign_request . '/' . $campaign_slug[1];
            
        }
        
        $campaign = wp_remote_get($campaign_domain, [
            'timeout' => 10,
            'httpversion' => '2.0',
            'user-agent' => 'WordPress/AdPage-Connect; ' . home_url(),
            'sslverify' => true
        ]);
        
        $headers = $campaign['headers'];
        $response = $campaign['body'];
        
        if (!empty($response)) {
            
            $wpdb->query('UPDATE ' . $wpdb->prefix . 'adpage' . ' SET hits = hits + 1 WHERE slug = "' . $wp->request . '"');
            
            header('Set-Cookie: ' . implode('; ', 
                $headers['set-cookie']
            ));
            
            header('X-AdPage-Connect: ' . 
                $adpgc_config['PLUGIN_VERSION']
            );
            
            header('X-AdPage-Cache: ' . 
                (isset($headers['cache-set']) ? $headers['cache-set'] : 0)
            );
            
            $dom = new DOMDocument();
            
            libxml_use_internal_errors(true);
            
            $dom->loadHTML($response);
            
            foreach ($dom->getElementsByTagName('a') as $item) {

                if ($item->getAttribute('data-wordpress') === 'REPLACE') {
                    
                    $item->setAttribute('href', get_site_url() . '/' . $campaign_slug[0] . $item->getAttribute('href'));
                    
                }
            }
            
            echo $dom->saveHTML();
            
            exit;
            
        }
        
    }
    
?>