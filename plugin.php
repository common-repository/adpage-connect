<?php
    
    /*
                      _  _____
            /\       | ||  __ \
           /  \    __| || |__) |__ _   __ _   ___
          / /\ \  / _` ||  ___// _` | / _` | / _ \
         / ____ \| (_| || |   | (_| || (_| ||  __/
        /_/    \_\\__,_||_|    \__,_| \__, | \___|
                                       __/ |
        Create great landingpages!    |___/
        
        Plugin Name: AdPage Connect
        Plugin URI: https://adpage.io/
        Description: Show your awesome AdPage campaigns in your WordPress-website
        Version: 3.0.0
        Author: Team AdPage
        Author URI: https://github.com/AdPageGroup
        
    */
    
    if (!defined('ABSPATH')) exit;
    
    $adpgc_config = [
        'PLUGIN_VERSION' => '3.0.0',
        'API_ENDPOINT' => 'https://api.adpage.io',
        'CAMPAIGN_ENDPOINT' => 'https://{HASH}.livepage.online',
        'KEY_PARAM' => 'adpgc_apikey',
        'PLUGIN_ENDPOINT' => plugins_url('adpage-connect'),
        'DATE_TIME_FORMAT' => get_option('date_format') . ' ' . get_option('time_format')
    ];
    
    add_action('parse_request', 'adpgc_endpoint', 0);
    add_action('admin_menu', 'adpgc_admin_pages');
    add_action('admin_enqueue_scripts', 'adpgc_admin_sources');
    add_action('admin_init', 'adpgc_admin_settings');
    add_action('admin_bar_menu', 'adpgc_admin_bar', 999);
    
    register_activation_hook(__FILE__, 'adpgc_enable');
    register_deactivation_hook(__FILE__, 'adpgc_disable');
    
    include __DIR__ . '/adpage-connect.class.php';
    
    function adpgc_admin_pages() {
        
        add_menu_page('AdPage Settings', 'AdPage', 'manage_options', 'adpage', 'adpgc_admin', plugins_url(basename(__DIR__) . '/assets/images/icon.svg'), 4);
        
    }
    
    function adpgc_admin_sources() {
        
        if ((isset($_GET['page']) ? $_GET['page'] : null) == 'adpage') {
        
            wp_enqueue_style('custom_wp_admin_css', plugins_url('assets/style.css', __FILE__));
            wp_enqueue_script('custom_wp_admin_js', plugins_url('assets/scripts.js', __FILE__));
            
        }
        
    }
    
    function adpgc_admin_settings() {
    
        global $adpgc_config;

        add_settings_section(  
            'adpgc_settings',
            'AdPage Connect',
            'adpgc_settings_callback',
            'general'
        );

        add_settings_field(
            $adpgc_config['KEY_PARAM'],
            'API-key',
            'adpgc_settings_fields',
            'general',
            'adpgc_settings',
            [
                $adpgc_config['KEY_PARAM']
            ]
        ); 

        register_setting('general', $adpgc_config['KEY_PARAM'], 'adpgc_settings_validate');

    }
    
    function adpgc_enable() {
        
        global $wpdb;
        
        $database = str_replace(
            [
                '[TABLE_NAME]', '[TABLE_CHARSET]'
            ],
            [
                $wpdb->prefix . 'adpage',
                $wpdb->get_charset_collate()
            ],
            file_get_contents(__DIR__ . '/database.sql')
        );
        
        $wpdb->query($database);
    
    }
    
    function adpgc_disable() {
        
        global $wpdb;
        global $adpgc_config;
        
        delete_option(ADPGC_CONFIG['KEY_PARAM']);
        
        $wpdb->query('DROP TABLE ' . $wpdb->prefix . 'adpage');
        
    }
    
    function adpgc_settings_callback() {
        
        echo '<p>Please paste your API-key below to use AdPage Connect.</p>';
         
    }
    
    function adpgc_settings_fields($args) {
        
        $value = $args[0];
        $option = get_option($value);
        
        echo '<input type="text" id="'. $value .'" name="'. $value .'" value="' . $option . '" class="regular-text" />';
        echo '<p class="description">Go to your <a href="https://app.adpage.io/main/settings/api" target="_blank">dashboard</a> to generate a API-key.</p>';
        
    }
    
    function adpgc_settings_validate($key) {
        
        global $adpgc_config;
        
        $validate = AdPage::validate_key($key);
        
        if ($validate !== true) {
        
            add_settings_error(
                'adpgc_error_key_invalid',
                esc_attr('settings_updated'),
                'This AdPage API-key looks invalid! Did you copy and paste the exact same value?',
                'error'
            );
            
            return get_option($adpgc_config['KEY_PARAM']);
            
        }
        
        return $key;
        
    }
    
    function adpgc_json_response($status, $data) {
        
        header('Content-Type: application/json');
        
        if ($status == 'success') {
            
            status_header(202);
            
        }
        else {
            
            status_header(203);
            
        }
        
        $response = json_encode([
            'ok' => ($status == 'success' ? true : false),
            'data' => $data
        ]);
        
        echo $response;
        
        exit;
        
    }
    
    function adpgc_admin() {
        
        global $wp;
        global $wpdb;
        global $adpgc_config;
        
        $key = get_option($adpgc_config['KEY_PARAM']);
        
        if (strlen($key) === 0) {
            
            include __DIR__ . '/pages/welcome.page.php';
            
        }
        else {
            
            include __DIR__ . '/pages/campaigns.page.php';
            
        }
        
    }
    
    function adpgc_endpoint() {
        
        global $wp;
        global $wpdb;
        global $adpgc_config;
        
        switch ($wp->request) {
            
            case 'adpgc/connect':
            
                include __DIR__ . '/actions/connect.action.php';
                
                exit;
                
                break;
                
            case 'adpgc/disconnect':
            
                include __DIR__ . '/actions/disconnect.action.php';
                
                exit;
                
                break;
                
            default:
            
                include __DIR__ . '/campaign.php';
            
        }
        
    }
    
    function adpgc_admin_bar($toolbar) {
        
        global $wpdb;
        
        $campaigns = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'adpage');
        
        $toolbar_icon = '<img src="' . plugins_url(basename(__DIR__) . '/assets/images/icon.svg') . '" style="opacity: .6; vertical-align: middle; height: 16px; margin: -5px 5px 0 0;" />';
        
        $toolbar->add_node([
            'id' => 'adpage_menu',
            'title' => $toolbar_icon .' AdPage',
            'href' => get_admin_url(null, 
                'admin.php?page=adpage'
            )
        ]);
        
        foreach ($campaigns as $campaign) {
        
            $toolbar->add_node([
                'id' => 'adpage_menu_sub_' . $campaign->id,
                'title' => $campaign->title . ' (' . $campaign->hits . ')',
                'parent' => 'adpage_menu',
                'href' => site_url() . '/' . $campaign->slug,
                'meta'   => [
                    'target'   => '_blank'
                ]
            ]);
            
        }
        
    }
    
?>