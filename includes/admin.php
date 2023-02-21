<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

$current = sanitize_text_field($_GET['tab']);
if( $current === '') {
    $current = 'manage';
}

?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <?php 

        if ( isset( $_GET['updated'] ) && $_GET['updated'] === 'true' ) {
            echo "<div class=\"updated\"><p>Saved.</p></div>";
        }

        if ( isset( $_GET['updated'] ) && $_GET['updated'] === 'false' ) {
            echo "<div class=\"error\"><p>Error.</p></div>";
        }

    ?>
    <h2 class="nav-tab-wrapper">
      <a href="?page=admin-ui&tab=manage" class="nav-tab <?php echo $current === 'manage' ? 'nav-tab-active' : ''; ?>">Manage Environment</a>
      <a href="?page=admin-ui&tab=dev" class="nav-tab <?php echo $current === 'dev' ? 'nav-tab-active' : ''; ?>">Development</a>
      <a href="?page=admin-ui&tab=prod" class="nav-tab <?php echo $current === 'prod' ? 'nav-tab-active' : ''; ?>">Production</a>
    </h2>
    <form action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" method="post">
       <input type="hidden" name="action" value="process_form">
       <?php wp_nonce_field( 'nap_admin_settings',  'nap_admin_settings_nonce' ); ?>
       <?php 
            switch($current) { 
              case 'manage': 
        ?>
            <input type="hidden" name="tab" value="manage">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="active_environment">Active:</label></th>
                        <td>
                            <select name="active_environment" id="active_environment">
                                <option value="dev" <?php if( get_option('active_environment') == 'dev') echo ' selected="selected"'; ?>>Development</option>
                                <option value="prod" <?php if( get_option('active_environment') == 'prod') echo ' selected="selected"'; ?>>Production</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="org">Organization:</label></th>
                        <td><input name="org" type="text" id="org" value="<?php echo get_option('org'); ?>" class="regular-text">
                    </tr>
                </tbody>
            </table>
        <?php 
            break; 
        ?>
        <?php 
            case 'dev':
        ?>
            <input type="hidden" name="tab" value="dev">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="api_endpoint_dev">API Endpoint:</label></th>
                        <td><input name="api_endpoint_dev" type="text" id="api_endpoint_dev" value="<?php echo get_option('api_endpoint_dev'); ?>" class="regular-text">
                    </tr>
                    <tr>
                        <th scope="row"><label for="basic_auth_username_dev">Basic Auth Username:</label></th>
                        <td><input name="basic_auth_username_dev" type="text" id="basic_auth_username_dev" value="<?php echo get_option('basic_auth_username_dev'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="basic_auth_password_dev">Basic Auth Password:</label></th>
                        <td><input name="basic_auth_password_dev" type="text" id="basic_auth_password_dev" value="<?php echo get_option('basic_auth_password_dev'); ?>" class="regular-text">
                    </tr>
                    <tr>
                        <th scope="row"><label for="secret_key_dev">Secret Key:</label></th>
                        <td><input name="secret_key_dev" type="text" id="secret_key_dev" value="<?php echo get_option('secret_key_dev'); ?>" class="regular-text">
                        <p class="description">The 256 bit/32 byte key to use as a cipher key.</p></td>
                    </tr>
                </tbody>
            </table>
        <?php  
            break;
        ?>
        <?php 
            case 'prod':
        ?>
            <input type="hidden" name="tab" value="prod">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="api_endpoint_prod">API Endpoint:</label></th>
                        <td><input name="api_endpoint_prod" type="text" id="api_endpoint_prod" value="<?php echo get_option('api_endpoint_prod'); ?>" class="regular-text">
                    </tr>
                    <tr>
                        <th scope="row"><label for="basic_auth_username_prod">Basic Auth Username:</label></th>
                        <td><input name="basic_auth_username_prod" type="text" id="basic_auth_username_prod" value="<?php echo get_option('basic_auth_username_prod'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="basic_auth_password_prod">Basic Auth Password:</label></th>
                        <td><input name="basic_auth_password_prod" type="text" id="basic_auth_password_prod" value="<?php echo get_option('basic_auth_password_prod'); ?>" class="regular-text">
                    </tr>
                    <tr>
                        <th scope="row"><label for="secret_key_prod">Secret Key:</label></th>
                        <td><input name="secret_key_prod" type="text" id="secret_key_prod" value="<?php echo get_option('secret_key_prod'); ?>" class="regular-text">
                        <p class="description">The 256 bit/32 byte key to use as a cipher key.</p></td>
                    </tr>
                </tbody>
            </table>
        <?php  
            break;
          }
        ?>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save changes"></p>
    </form>
</div><!-- .wrap -->