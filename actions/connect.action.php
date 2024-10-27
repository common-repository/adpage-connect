<?php
  
    if (!defined('ABSPATH')) exit;
    
    if (is_user_logged_in()) {
        
        if (current_user_can('administrator')) {
            
            $forbidden = [
                'wp-admin',
                'wp-content',
                'wp-includes',
                'admin',
                'adpgc'
            ];
            
            $hash = $_POST['hash'];
            $slug = $_POST['slug'];
            $title = $_POST['title'];
            $domain = $_POST['domain'];
            
            if (!preg_match('/^[A-Za-z0-9-]+$/', $slug)) {
                
                adpgc_json_response('failed', [
                    'error' => 'Slug contains invalid characters'
                ]);
                
            }
            
            if ((strlen($slug) < 4) || (strlen($slug) > 20)) {
                
                adpgc_json_response('failed', [
                    'error' => 'Slug must be within the size of 4-20'
                ]);
                
            }
            
            if (in_array($slug, $forbidden)) {
                
                adpgc_json_response('failed', [
                    'error' => 'Slug is forbidden'
                ]);
                
            }
            
            $campaign = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'adpage' . ' WHERE slug = "' . $slug . '"');
            
            if (sizeof($campaign) !== 0) {
            
                adpgc_json_response('failed', [
                    'error' => 'Slug is already taken'
                ]);
            
            }
            
            $wpdb->query('INSERT INTO ' . $wpdb->prefix . 'adpage (title, slug, hash, domain, hits, timestamp) VALUES ("' . $title . '", "' . $slug . '", "' . $hash . '", "' . $domain . '", "0", "' . time() . '")');
            
            adpgc_json_response('success', [
                'info' => 'Campaign connect successfully'
            ]);
            
        }
        else {
            
            adpgc_json_response('failed', [
                'error' => 'You must be a admin to do that'
            ]);
            
        }
        
    }
    else {
        
        adpgc_json_response('failed', [
            'error' => 'You must be logged in to do that'
        ]);
        
    }
   
?>