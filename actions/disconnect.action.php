<?php
  
    if (!defined('ABSPATH')) exit;
    
    if (is_user_logged_in()) {
        
        if (current_user_can('administrator')) {
            
            $hash = $_POST['hash'];
    
            $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'adpage WHERE hash = "' . $hash . '"');
            
            adpgc_json_response('success', [
                'info' => 'Campaign disconnect successfully'
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