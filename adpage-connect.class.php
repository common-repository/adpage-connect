<?php
    
    if (!defined('ABSPATH')) exit;
    
    class AdPage {
        
        public static function campaigns() {
            
            $response = self::request('campaigns');
            
            if ($response->code === 200) {
                
                return $response->data;
                
            }
            
            return false;
            
        }
        
        public static function validate_key($key) {
            
            $response = self::request('campaigns', 'GET', [], $key);
            
            return $response->ok;
            
        }
        
        private static function request($endpoint, $method = 'GET', $data = [], $key = false) {
            
            global $adpgc_config;
            
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $adpgc_config['API_ENDPOINT'] . '/' . $endpoint);
            
            if ($key !== false) {
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'auth-token: ' . $key
                ]);
                
            }
            else {
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'auth-token: ' . get_option($adpgc_config['KEY_PARAM'])
                ]);
                
            }
            
            if ($method == 'POST') {
                
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, 
                    $data
                );
                
            }
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $response = json_decode(curl_exec($ch));
            
            curl_close($ch);
            
            return $response;
                      
        }
        
    }
    
?>