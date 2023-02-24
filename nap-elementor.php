<?php
/*
Plugin Name: Nap - Elementor Integration
Description: Connects Elementor Pro forms to Nap Biotec Apis
Version: 1.0.0
Author: Tip Siri
Text Domain: nap-elementor
*/

define('NAP_ELEMENTOR_DIR_PATH', plugin_dir_path(__FILE__));
define('NAP_ELEMENTOR_VERSION', '1.0.0');


require_once NAP_ELEMENTOR_DIR_PATH . 'config.php';
require_once NAP_ELEMENTOR_DIR_PATH . 'includes/elementor.php';

function nap_admin_page_setup() {
   require_once NAP_ELEMENTOR_DIR_PATH . 'includes/admin.php';
}
function nap_setup_admin_menu() {
   add_menu_page( 'Settings', 'NAP Biotec', 'manage_options', 'admin-ui', 'nap_admin_page_setup' );
}
add_action( 'admin_menu', 'nap_setup_admin_menu' );


function process_form_data() {
    
    // check_admin_referer
	if ( ! current_user_can( 'administrator' ) ) {
		wp_safe_redirect( add_query_arg( array( 'updated' => 'false' ), wp_get_referer() ));
	    die();
	}

    if ( ! wp_verify_nonce( $_POST['nap_admin_settings_nonce'], 'nap_admin_settings' ) ) {
        wp_safe_redirect( add_query_arg( array( 'updated' => 'false' ), wp_get_referer() ));
	    die();
    } 

    if( $_POST['tab'] === 'manage' ) {
        update_option( 'active_environment', $_POST['active_environment'] );
        update_option( 'org', trim($_POST['org']) );
    } else if( $_POST['tab'] === 'dev' ) {
        update_option( 'api_endpoint_dev' , trim($_POST['api_endpoint_dev']) );
        update_option( 'basic_auth_username_dev' , trim($_POST['basic_auth_username_dev']) );
        update_option( 'basic_auth_password_dev', trim($_POST['basic_auth_password_dev']) );
        update_option( 'secret_key_dev', trim($_POST['secret_key_dev']) );
    } else if( $_POST['tab'] === 'prod' ) {
        update_option( 'api_endpoint_prod' , trim($_POST['api_endpoint_prod']) );
        update_option( 'basic_auth_username_prod' , trim($_POST['basic_auth_username_prod']) );
        update_option( 'basic_auth_password_prod', trim($_POST['basic_auth_password_prod']) );
        update_option( 'secret_key_prod', trim($_POST['secret_key_prod']) );
    }

    wp_safe_redirect( add_query_arg( array('updated' => 'true' ), wp_get_referer() ));
	die();
}
add_action( 'admin_post_nopriv_process_form', 'process_form_data' );
add_action( 'admin_post_process_form', 'process_form_data' );


function nap_enqueue_scripts() {
    $slug = get_post_field( 'post_name', get_post() );

    $path = NAP_ELEMENTOR_DIR_PATH;
    $path = substr($path, strpos($path, '/', 1));
    $path = substr($path, strpos($path, '/', 1));

    if ( $slug == 'register-product' ||  $slug == 'register-customer' ) {
        wp_enqueue_script( 'nap', $path . 'assets/js/nap.js', array( 'jquery' ) );

        $slug = str_replace("-", "_", $slug);
        wp_localize_script( 'nap', 'napVar', array( 'nonce' => wp_create_nonce('napAjaxNonce'), 'form' => "#nap_$slug" ) );
    }

    if( $slug == 'default-kit' ) {
        wp_enqueue_style( 'style', $path . '/assets/css/nap.css' );
    }
}
add_action( 'wp_enqueue_scripts', 'nap_enqueue_scripts' );


if( ! function_exists('product_registration_status_shortcode') ) {
    function product_registration_status_shortcode( $atts, $content = null ) {

        $status = isset($_GET['status']) ? decrypt($_GET['status']) : false;
        echo '<pre style="display:none">';
        echo 'Status:';
        var_dump($status);
        echo '</pre>';

        $statusCode = isset($_GET['statusCode']) ? decrypt($_GET['statusCode']) : false;
        echo '<pre style="display:none">';
        echo 'statusCode:';
        var_dump($statusCode);
        echo '</pre>';

        $serial = isset($_GET['serial']) ? decrypt($_GET['serial']) : false;
        echo '<pre style="display:none">';
        echo 'Serial:';
        var_dump($serial);
        echo '</pre>';


        $pin = isset($_GET['pin']) ? decrypt($_GET['pin']) : false;
        echo '<pre style="display:none">';
        echo 'Pin:';
        var_dump($pin);
        echo '</pre>';
        
        // The decrypted text's value is null or empty as the passing string is invalid
        if( is_null($status) || empty($status) || is_null($statusCode) || empty($statusCode) || 
            is_null($serial) || empty($serial) || is_null($pin) || empty($pin) ) {
            $statusCode = 'ERROR';
        }

        $statusCode = $statusCode == 'ERROR' ? 'danger' : 'success';

        // Output: Always use return (never echo or print)
        return "<div class=\"elementor-message elementor-message-{$statusCode}\"></div>
                <table class=\"styled-table\">
                  <tbody>
                    <tr>
                      <td style=\"text-align:left; font-size: large;\">Serial</td>
                      <td style=\"text-align:right; font-size: medium;\" id=\"serial-txt\">{$serial}</td>
                    </tr>
                    <tr>
                      <td style=\"text-align:left; font-size: large;\">Pin</td>
                      <td style=\"text-align:right; font-size: medium;\" id=\"pin-txt\">{$pin}</td>
                    </tr>
                    <tr>
                      <td style=\"text-align:left; font-size: large;\">Status</td>
                      <td style=\"text-align:right; font-size: medium;\" id=\"status-txt\">{$status}</td>
                    </tr>
                  </tbody>
                </table>";
    }
    add_shortcode('product_registration_status', 'product_registration_status_shortcode' );
}

function decrypt($ctext) {
    $ctext = str_replace(' ', '+', $ctext);
    
    $password = get_option( 'secret_key_'.ACTIVE_ENV );
    $key = substr(hash('sha256', $password, true), 0, 32);

    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    $text = openssl_decrypt(base64_decode($ctext), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $text ;
}