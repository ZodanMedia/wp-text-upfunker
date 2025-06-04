<?php
/**
 * Plugin Name: Z Text Upfunker
 * Contributors: zodannl, martenmoolenaar
 * Plugin URI: https://plugins.zodan.nl/wordpress-text-upfunker/
 * Tags: Text, animation, theme design, theme development, development
 * Requires at least: 5.5
 * Tested up to: 6.8
 * Description: Display text in a funky way with CSS animations
 * Version: 1.0
 * Stable Tag: 1.0
 * Author: Zodan
 * Author URI: https://zodan.nl
 * Text Domain: z-text-upfunker
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 */


// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

/**
 * Start: create an instance after the plugins have loaded
 * and call the switch when setting up the theme.
 * 
 */
add_action( 'plugins_loaded', function() {
	$instance = zTextUpfunker::get_instance();
	$instance->plugin_setup();
} );

add_action( 'setup_theme', function() {
	zTextUpfunker::get_instance()->maybe_enable_text_upfunk();
} );




class zTextUpfunker {

	protected static $instance = NULL;
	public $plugin_version = '1.0';
	public $plugin_url = '';
	public $plugin_path = '';

	public static function get_instance() {
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	public function __construct() {}

	public function plugin_setup() {
		$this->plugin_url = plugins_url( '/', __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->load_language( 'z-text-upfunker' );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_plugin_settings_link' ] );

		if ( is_admin() ) {
			include( $this->plugin_path . 'admin.php' );
		}

		// Front-end only logic
		if ( ! is_admin() && is_user_logged_in() ) {
			self::maybe_enable_text_upfunk();
        	// add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_plugin_settings_link' ] );
        	

		}
	}

	public function maybe_enable_text_upfunk() {
		if ( ! is_admin() && is_user_logged_in() ) {
			$options = get_option( 'z_text_upfunker_plugin_options' );

			// if no element (selector) is present, do nothing
			if ( empty( $options['elem'] ) ) return;

			// if no animation is selected, do nothing
			if ( $options['type'] == 'none' ) return;

			// else, go do funk up that text
			wp_register_script(
				'z-text-upfunker-js',
				$this->plugin_url . '/assets/z-text-upfunker.min.js',
				null,
				1.0,
				true
			);
			wp_enqueue_script( 'z-text-upfunker-js' );
			wp_localize_script( 'z-text-upfunker-js', 'zTextUpfunkerParams', array(
				'elem' => esc_html( $options['elem'] ),
				'type' => esc_html( $options['type'] ),
				'cycles' => intval( $options['cycles'] ),
			) );

			wp_register_style( 'z-text-upfunker-css', $this->plugin_url . '/assets/z-text-upfunker.min.css', false, '1.0' );
			wp_enqueue_style( 'z-text-upfunker-css' );

		}
	}


	public static function add_plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=z_text_upfunker">' . __( 'Settings','z-text-upfunker' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function load_language( $text_domain ) {
		load_plugin_textdomain( $text_domain, false, false );
	}
}