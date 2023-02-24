<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param Form_Record $record
 * @param Ajax_Handler $handler
 */
function nap_elementor_forms_submission_listener( $record, $handler ) {

    $form_name = $record->get_form_settings( 'form_name' );

    if( 'nap-customer-register-form' == $form_name ) {
        return;
    }

    if( 'nap-product-register-form' != $form_name ) {
        $handler->add_error_message( "Form Name is invalid" );
        return;
    }

    if( !isset( $_POST['nap_ajax_nonce'] ) || !wp_verify_nonce( $_POST['nap_ajax_nonce'], 'napAjaxNonce' ) ) {
        $handler->add_error_message( "Nonce is invalid" );
        return;
    }

    $raw_fields = $record->get( 'fields' );
    $fields = [];
    foreach ( $raw_fields as $id => $field ) {
        $fields[ $id ] = $field['value'];
    }


    $username = get_option( 'basic_auth_username_'.ACTIVE_ENV);
    $password = get_option( 'basic_auth_password_'.ACTIVE_ENV );
    $endpoint = get_option( 'api_endpoint_'.ACTIVE_ENV );
    $org = get_option( 'org' );

    $response = wp_remote_post( "https://{$endpoint}/api/assets/org/{$org}/action/RegisterAsset/{$fields["serial"]}/{$fields["pin"]}", array(
      'method' => 'GET',
      'headers' => array( 
        'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password ),
        'content-type' => 'application/json'
       )
    ));

    if( $response['response']['code'] == 400 ) {
        $handler->add_error_message( $response['body'] );
    } else if (  $response['response']['code'] == 401 ) {
        $handler->add_error_message( $response['response']['message'] ); 
    } else if (  $response['response']['code'] == 200 ) {
        $handler->add_response_data( true, $response['body'] );
    }

}
add_action( 'elementor_pro/forms/new_record', 'nap_elementor_forms_submission_listener', 10, 2 );


/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param Form_Record $record
 * @param Ajax_Handler $handler
 */
function nap_customer_register_form_submission_listener( $record, $handler ) {

    $form_name = $record->get_form_settings( 'form_name' );

    if( 'nap-product-register-form' == $form_name ) {
        return;
    }

    if( 'nap-customer-register-form' != $form_name ) {
        $handler->add_error_message( "Form Name is invalid" );
        return;
    }

    $raw_fields = $record->get( 'fields' );
	$fields = [];
	foreach ( $raw_fields as $id => $field ) {
		$fields[ $id ] = $field['value'];
	}

    // $text = "name: {$fields['name']} \n lastName: {$fields['lastName']} \n email: {$email['email']} \n phoneNo: {$fields['phoneNo']}\nnote: {$fields['description']}";
    // $handler->add_error_message( $text );
    
    if( !isset( $_POST['nap_ajax_nonce'] ) || !wp_verify_nonce( $_POST['nap_ajax_nonce'], 'napAjaxNonce' ) ) {
        $handler->add_error_message( "Nonce is invalid" );
        return;
    }

    $username = get_option( 'basic_auth_username_dev');
    $password = get_option( 'basic_auth_password_dev' );
    $endpoint = get_option( 'api_endpoint_dev' );
    $org = get_option( 'org' );

    $response = wp_remote_post( "https://{$endpoint}/api/customers/org/{$org}/action/AddCustomer/", array(
      'method' => 'POST',
      'headers' => array( 
        'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password ),
        'content-type' => 'application/json'
      ),
      'body' => json_encode( array( 'name' => $fields['name'], 'lastName' => $fields['lastName'], 'email' => $fields['email'], 'phoneNo' => $fields['phoneNo'], 'description' => $fields['description'] ) )
    ));

    if( $response['response']['code'] == 400 ) {
        $handler->add_error_message( $response['body'] );
    } else if (  $response['response']['code'] == 401 ) {
        $handler->add_error_message( $response['response']['message'] ); 
    } else if (  $response['response']['code'] == 200 ) {
        $handler->add_response_data( true, $response['body'] );
    }

}
add_action( 'elementor_pro/forms/new_record', 'nap_customer_register_form_submission_listener', 10, 2 );