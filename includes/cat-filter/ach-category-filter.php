<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
    add_action( 'plugins_loaded', array( 'ach_Category_Filter', 'get_instance' ) );
}

class ach_Category_Filter {

    /**
     * Instance of this class.
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts and styles.
     */
    private function __construct() {
        // Load admin JavaScript.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    /**
     * Return an instance of this class.
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if ( null === self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific JavaScript.
     */
    public function enqueue_admin_scripts( $screen ) {
        $screen = get_current_screen();

        if ( 'post' === $screen->base || 'edit' === $screen->base ) {
			wp_enqueue_script( '-admin-script', ACH_URL.'includes/cat-filter/admin.js', array(), '1.0.0', true );
            wp_localize_script( '-admin-script', 'fc_plugin', $this->get_plugin_settings() );
        }
    }

    /**
     * Get JS accessible settings
     */
    public function get_plugin_settings() {
        $screen = get_current_screen();

        return array(
            'placeholder' => esc_html__( 'Filter %s', 'ach_toolkit' ),
            'screenName'  => $screen->base
        );
    }

}

?>
