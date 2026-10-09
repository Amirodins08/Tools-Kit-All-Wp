<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Adds the captcha to the login form and deletes old captchas from the database
add_action( 'login_form', 'ach_login_form_captcha', 9999999999 );

// Adds the captcha to the WooCommerce login form and deletes old captchas from the database
add_action( 'woocommerce_login_form', 'ach_woo_login_form_captcha', 9999999999 );

// Validates the login captcha before wordpress checks the user and pass
add_filter( 'authenticate', 'ach_validate_login_form', 10, 3 );

// Outputs the HTML code needed for displaying the correct security code and the request ID.
function ach_output_captcha_code() {
    $request_id = md5( mt_rand( 1, 999999999 ) . 'mki3uflxnq29r4dzplf9u6671qmn43cvjg85jdxbv130oppr4zxs3edjfv85dnswk5tkg96' );
    $answer_pool = mt_rand( 100000000, 999999999 );
    $start = mt_rand( 0, 6 );
    $answer = substr( (string) $answer_pool, $start, 3 );

    // Make sure that on a multisite the new global database table from version 1.2.0 is created
    if ( is_multisite() && get_site_option( 'ach-new-database-table-created' ) !== 'yes' ) {
        ach_create_captcha_db_table();
    }

    global $wpdb;
    $table_name = ach_get_table_name();
    $wpdb->insert( $table_name,	array( 'request_id' => $request_id, 'answer' => $answer, 'unix_time_added' => time() ), array( '%s', '%s', '%d' ) );
    $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $table_name . ' WHERE unix_time_added < %d', ( time() - 600 ) ) );

    ?>
    <span class="ach-code-span">
        <script type="text/javascript">
            var answerPool = "<?php echo intval( $answer_pool ); ?>";
            for ( var i = 0; i < answerPool.length; i++ ) {
                if ( i >= <?php echo intval( $start ); ?> && i < <?php echo intval( $start + 3 ); ?> ) {
                    document.write( answerPool.charAt( i ) );
                }
            }
        </script>
        <noscript><?php esc_html_e( 'Your browser does not support JavaScript!', 'ach_toolkit' ); ?></noscript>
    </span>
    <input name="ach-captcha-request" type="hidden" value="<?php echo esc_attr( $request_id ); ?>" />
    <?php
}

// Adds the captcha to the login form and deletes old captchas from the database
function ach_login_form_captcha() {
    ?>
    <p class="ach-code-paragraph">
        <?php ach_output_captcha_code(); ?>
    </p>
    <p>
        <input id="ach-captcha-answer" autocomplete="off" name="ach-captcha-answer" placeholder="<?php esc_html_e( 'Enter the security code', 'ach_toolkit' ) ?>" size="20" type="text" />
    </p>
    <?php
}

// Adds the captcha to the WooCommerce login form and deletes old captchas from the database
function ach_woo_login_form_captcha() {
    ?>
    <p class="ach-code-paragraph woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <?php ach_output_captcha_code(); ?>
    </p>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <input id="ach-captcha-answer" class="woocommerce-Input woocommerce-Input--text input-text" autocomplete="off"
            name="ach-captcha-answer" placeholder="<?php esc_html_e( 'Enter the security code', 'ach_toolkit' ) ?>" size="20" type="text" />
    </p>
<?php
}

/**
 * Validates the login captcha before wordpress checks the user and pass
 * @param object $user
 * @param string $username
 * @param string $password
 * @return object
 */
function ach_validate_login_form( $user, $username, $password ) {

    // If the username or password are not sent we do nothing (and return $user), this way we avoid errors to be shown before the user clicks the button to log in
    if ( ! isset( $username ) || '' == $username || ! isset( $password ) || '' == $password ) {
        return $user;
    }

    // The request data is empty or missing
    if ( ! isset( $_POST['ach-captcha-request'] ) || '' == $_POST['ach-captcha-request'] ) {
        ach_remove_authenticate_and_increase_blocked();
        $user = new WP_Error( 'denied', '<strong>' . esc_html__( 'ERROR', 'ach_toolkit' ) . '</strong>: '
            . esc_html__( 'Missing request ID.', 'ach_toolkit' ) );
        return $user;
    }

    // The answer data is empty or missing
    if ( ! isset( $_POST['ach-captcha-answer'] ) || '' == $_POST['ach-captcha-answer'] ) {
        ach_remove_authenticate_and_increase_blocked();
        $user = new WP_Error( 'denied', '<strong>' . esc_html__( 'ERROR', 'ach_toolkit' ) . '</strong>: '
            . esc_html__( 'Please enter the security code.', 'ach_toolkit' ) );
        return $user;
    }

    // Make sure that on a multisite the new global database table from version 1.2.0 is created
    if ( is_multisite() && get_site_option( 'ach-new-database-table-created' ) !== 'yes' ) {
        ach_create_captcha_db_table();
    }

    $request_id = sanitize_html_class( $_POST['ach-captcha-request'] );
    $answer = intval( $_POST['ach-captcha-answer'] );
    global $wpdb;
    $table_name = ach_get_table_name();
    $results = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $table_name . ' WHERE request_id = %s', $request_id ), ARRAY_A );

    // There is no such request ID. It is invalid or has expired.
    if ( null === $results ) {
        ach_remove_authenticate_and_increase_blocked();
        $user = new WP_Error( 'denied', '<strong>' . esc_html__( 'ERROR', 'ach_toolkit' ) . '</strong>: '
            . esc_html__( 'Invalid or expired request.', 'ach_toolkit' ) );
        return $user;
    }

    // The answer is incorrect or does not match the request ID
    if ( intval( $results['answer'] ) !== intval( $answer ) ) {
        ach_remove_authenticate_and_increase_blocked();
        $user = new WP_Error( 'denied', '<strong>' . esc_html__( 'ERROR', 'ach_toolkit' ) . '</strong>: '
            . esc_html__( 'This is not the correct security code.', 'ach_toolkit' ) );
        $wpdb->delete( $table_name, array( 'request_id' => $request_id ), array( '%s' ) );
        return $user;
    }

    // At this point the answer is correct. We delete the database data for this request and answer so it cannot be used again.
    $wpdb->delete( $table_name, array( 'request_id' => $request_id, 'answer' => $answer ), array( '%s', '%s' ) );

    ach_increase_option_number( 'passed' );

    // Return $user to allow wordpress to check password and username
    return $user;
}

// We remove further authentication and increase the counter for blocked requests
function ach_remove_authenticate_and_increase_blocked() {
    remove_action( 'authenticate', 'wp_authenticate_username_password', 20 );
    remove_action( 'authenticate', 'wp_authenticate_email_password', 20 );
    ach_increase_option_number( 'blocked' );
}

// Increases the number in an option value (used to count passed and blocked requests for the current month)
function ach_increase_option_number( $name ) {
    $value = intval( get_option( 'ach-' . $name . '-' . date( 'm' ) . '-' . date( 'Y' ) ) ) + 1;
    update_option( 'ach-' . $name . '-' . date( 'm' ) . '-' . date( 'Y' ), $value );
}

/**
 * Returns the id of the main site of the multisite network (which is usually 1)
 * @return int
 */
function ach_get_main_site_id() {
    global $wp_version;
    if ( version_compare( $wp_version, '4.9', '>=' ) ) {
        return get_main_site_id();
    } else {
        global $current_site;
        return intval( $current_site->blog_id );
    }
}
