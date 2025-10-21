<?php
/**
 * Plugin Name: Z Text Upfunker
 * Contributors: zodannl, martenmoolenaar
 * Plugin URI: https://plugins.zodan.nl/wordpress-text-upfunker/
 * Tags: Text, animation, theme design, theme development, development
 * Requires at least: 5.5
 * Tested up to: 6.8
 * Description: Display text in a funky way with CSS animations
 * Version: 0.1.8
 * Stable Tag: 0.1.8
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

define( 'ZTEXTUPFUNKER_VERSION', '0.1.8' );


add_action( 'plugins_loaded', function() {
	$instance = zTextUpfunker::get_instance();
	$instance->plugin_setup();
} );

add_action( 'setup_theme', function() {
	zTextUpfunker::get_instance()->maybe_enable_text_upfunk();
} );




class zTextUpfunker {

	protected static $instance = null;
	public $plugin_version = ZTEXTUPFUNKER_VERSION;
	public $plugin_url = '';
	public $plugin_path = '';

	public static function get_instance() {
		null === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	public function __construct() {}

	public function plugin_setup() {
		$this->plugin_url = plugins_url( '/', __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ __CLASS__, 'add_plugin_settings_link' ] );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'z_text_upfunker_add_admin_scripts' ] );
			add_action( 'admin_init', [ $this, 'z_text_upfunker_register_settings' ] );
			add_action( 'admin_menu', [ $this, 'z_text_upfunker_add_admin_menu' ] );
		}

		if ( ! is_admin() ) {
			$this->maybe_enable_text_upfunk();
		}
	}

	public function maybe_enable_text_upfunk() {
		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_and_styles' ] );
		}
	}

	public static function add_plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=z_text_upfunker">' . __( 'Settings','z-text-upfunker' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function enqueue_scripts_and_styles() {
		$options = get_option( 'z_text_upfunker_plugin_options' );

		if ( empty( $options['items'] ) ) return;

		wp_register_style( 'z-text-upfunker-css', $this->plugin_url . 'assets/z-text-upfunker.min.css', false, $this->plugin_version );
		wp_register_script(
			'z-text-upfunker-js',
			$this->plugin_url . 'assets/z-text-upfunker.min.js',
			null,
			$this->plugin_version,
			true
		);

		$items = array();
		foreach( $options['items'] as $item ) {
			if ( empty( $item['elem'] ) || $item['type'] == 'none' ) continue;

			$items[] = array(
				'elem' => esc_html( $item['elem'] ),
				'type' => esc_html( $item['type'] ),
				'cycles' => intval( $item['cycles'] ),
			);
		}

		wp_localize_script( 'z-text-upfunker-js', 'zTextUpfunkerParams', array(
			'items' => $items
		) );
		
		wp_enqueue_script( 'z-text-upfunker-js' );
		wp_enqueue_style( 'z-text-upfunker-css' );
	}



	public static function z_text_upfunker_get_available_anim_types() {    
		return array(
			'none' => __('- no style (disabled)', 'z-text-upfunker'),
			'rand' => __('Random animation', 'z-text-upfunker'),
			'code' => __('Scrambled code', 'z-text-upfunker'),
			'fade' => __('Fade characters', 'z-text-upfunker'),
			'flip' => __('Flip characters', 'z-text-upfunker'),
			'sink' => __('Sink characters', 'z-text-upfunker'),
			'pop' => __('Pop characters', 'z-text-upfunker'),
			'flkr' => __('Flickr characters', 'z-text-upfunker'),
			'circ' => __('Circle characters', 'z-text-upfunker'),
		);
	}

	public function z_text_upfunker_register_settings() {
		$settings_args = array(
			'type' => 'array',
			'description' => '',
			'sanitize_callback' => [ __CLASS__, 'z_text_upfunker_plugin_options_validate' ],
			'show_in_rest' => false
		);
		register_setting( 'z_text_upfunker_plugin_options', 'z_text_upfunker_plugin_options', $settings_args );

		add_settings_section( 'z_text_upfunker_introduction_section', null, [ $this, 'z_text_upfunker_introduction_section_text' ], 'z_text_upfunker_plugin' );

		add_settings_section(
			'z_text_upfunker_main_section',
			esc_html__('All funky elements', 'z-text-upfunker'),
			[ $this, 'z_text_upfunker_main_section_text' ],
			'z_text_upfunker_plugin'
		);

		add_settings_field(
			'z_text_upfunker_setting_items',
			esc_html__('Items', 'z-text-upfunker'),
			[ $this, 'z_text_upfunker_ia_item_display' ],
			'z_text_upfunker_plugin',
			'z_text_upfunker_main_section',
			array( 'class' => 'ia_items' )
		);
	}

	public function z_text_upfunker_introduction_section_text() {
       echo '<p class="intro">' . esc_html__('The Text Upfunker animates the text of the html elements you want to spice up a bit.', 'z-text-upfunker') . '</p>';
        
        echo '<details class="z-tu-help"><summary><h2><i class="dashicons dashicons-editor-help"></i>';
        esc_html_e('How it works - documentation', 'z-text-upfunker');
        echo '</h2></summary>';
        echo '<ol>';
        echo '<li>' . esc_html__('The Text Upfunker looks for the elements configured here, so click the "Add item" button and:', 'z-text-upfunker') . '</li>';
        
        echo '<li>';
        echo wp_kses(
            __('<strong>Enter the html element (selector)</strong> that needs funk.', 'z-text-upfunker'),
            array(
                'strong' => array(),
                'code' => array(),
            )
        );
        echo '<br>';  
        echo wp_kses(
            __('For example, you could use a html tag like <code>h2</code> to have the animation applied to all secondary headings.', 'z-text-upfunker'),
            array(
                'em' => array(),
                'code' => array(),
            )
        );
        echo ' ';
        echo wp_kses(
            __('Or you could use a selector like <code>#someId</code> for all elements with id "someId" or <code>.someClassName</code> for all elements with the class "someClassName".', 'z-text-upfunker'),
            array(
                'em' => array(),
                'code' => array(),
            )
        );
        echo '<span>';
        echo wp_kses(
            __('<em>Note</em> that you can enter multiple selectors, separated by commas, like in css/javascript: <code>h2, .someClassName</code>. This would have the Upfunker looking for both all h2 elements and as all elements with "someClassName".', 'z-text-upfunker'),
            array(
                'em' => array(),
                'code' => array(),
            )
        );
        echo '</span>';
        echo '</li>';

        echo '<li>';
        echo wp_kses(
            __('<strong>Select the style</strong> that you want to apply to the selected elements above.', 'z-text-upfunker'),
            array(
                'strong' => array(),
                'code' => array(),
            )
        );
        echo '</li>';

        echo '<li>';
        echo wp_kses(
            __('<strong>Enter the maximum number of loops</strong> you want the animation to play. If nothing is entered, the animation will play infinitely.', 'z-text-upfunker'),
            array(
                'strong' => array(),
                'code' => array(),
            )
        );
        echo '</li>';

        echo '<li>';
        echo wp_kses(
            __('<em>Please note</em> that the orginal element may have nested elements, but a) only one level deep and b) some enhanced styling might break due to the css animations.', 'z-text-upfunker'),
            array(
                'em' => array(),
            )
        );
        echo '</li>';
        echo '</ol>';
       echo '</details>';


       echo '<details class="z-tu-help"><summary><h2><i class="dashicons dashicons-info"></i>';
        esc_html_e('Supported styles', 'z-text-upfunker');
        echo '</h2></summary>';

        echo '<ul class="featured-anim-list">';
        echo '<li>' . esc_html__('The plugin currently supports these animation styles:', 'z-text-upfunker') . '</li>';
  
        echo '<li><h3>' . esc_html__('Scrambled code', 'z-text-upfunker') . '</h3>';
        esc_html_e('For each word, the characters appear one by one from scrambled text.', 'z-text-upfunker');
        echo '<figure><img src="'.esc_url($this->plugin_url . 'assets/examples/scramble-1x.gif').'" alt="" title="" loading="lazy"></figure></li>';

        echo '<li><h3>' . esc_html__('Fade in', 'z-text-upfunker') . '</h3>';
        esc_html_e('The characters fade in, one by one. Looks like a soft version of text being typed.', 'z-text-upfunker');
        echo '<figure><img src="'.esc_url($this->plugin_url . 'assets/examples/fade-1x.gif').'" alt="" title="" loading="lazy"></figure></li>';

        echo '<li><h3>' . esc_html__('Pop up', 'z-text-upfunker') . '</h3>';
        esc_html_e('The characters pop up one by one. Looks like a popping version of text being typed.', 'z-text-upfunker');
        echo '<figure><img src="'.esc_url($this->plugin_url . 'assets/examples/popup-1x.gif').'" alt="" title="" loading="lazy"></figure></li>';

        echo '<li><h3>' . esc_html__('Flicker', 'z-text-upfunker') . '</h3>';
        esc_html_e('The characters appear one by one, flickering like a broken tubelight.', 'z-text-upfunker');
        echo '<figure><img src="'.esc_url($this->plugin_url . 'assets/examples/flicker-1x.gif').'" alt="" title="" loading="lazy"></figure></li>';

        echo '<li><h3>' . esc_html__('Flip in', 'z-text-upfunker') . '</h3>';
        esc_html_e('The characters appear one by one, as if they were flipped on a rolodex.', 'z-text-upfunker');
        echo '<figure><img src="'.esc_url($this->plugin_url . 'assets/examples/flip-1x.gif').'" alt="" title="" loading="lazy"></figure></li>';

        echo '<li><h3>' . esc_html__('Sink in', 'z-text-upfunker') . '</h3>';
        esc_html_e('The characters appear one by one, as if dropped from above.', 'z-text-upfunker');        
        echo '<figure><img src="'.esc_url($this->plugin_url . 'assets/examples/sink-1x.gif').'" alt="" title="" loading="lazy"></figure></li>';

        echo '<li><h3>' . esc_html__('Circle', 'z-text-upfunker') . '</h3>';
        esc_html_e('The character appear one by one, rotating like madmen.', 'z-text-upfunker');        
        echo '<figure><img src="'.esc_url($this->plugin_url . 'assets/examples/circle-1x.gif').'" alt="" title="" loading="lazy"></figure></li>';     

        echo '<li><h3>' . esc_html__('Random', 'z-text-upfunker') . '</h3>';
        esc_html_e('And finally, there is of course a "random" option, which randomly applies one of these effects.', 'z-text-upfunker') . '</li>';
        echo '</ul>';
        echo '</details>';
	}

	public function z_text_upfunker_main_section_text() {
		echo '<p>' . esc_html__('Add a new item for every element/animation combination on your pages.', 'z-text-upfunker') . '</p>';
	}

	public function z_text_upfunker_render_item_elem_input( $item_key ) {
		$options = get_option( 'z_text_upfunker_plugin_options' );
		$current_elem = isset( $options['items'][ $item_key ]['elem'] ) ? $options['items'][ $item_key ]['elem'] : '';
		echo '<input type="text" class="ztu-input" value="'. esc_attr( $current_elem ) . '" name="z_text_upfunker_plugin_options[items]['. esc_attr($item_key) .'][elem]" id="z_text_upfunker_plugin_options_item_'.  esc_attr($item_key)  .'_elem">';
	}

	public function z_text_upfunker_render_item_animation_dropdown( $item_key ) {
		$options = get_option( 'z_text_upfunker_plugin_options' );
		$selected_type = isset( $options['items'][ $item_key ]['type'] ) ? $options['items'][ $item_key ]['type'] : 'rand';
		$available_types = self::z_text_upfunker_get_available_anim_types();
		echo '<select class="ztu-input" name="z_text_upfunker_plugin_options[items]['. intval($item_key) .'][type]" id="z_text_upfunker_plugin_options_item_'. intval($item_key) .'_type">';
		foreach( $available_types as $slug => $label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $slug ),
				selected( $selected_type, $slug, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
	}

	public function z_text_upfunker_render_item_cycles_input( $item_key ) {
		$options = get_option( 'z_text_upfunker_plugin_options' );
		$current_cycles = isset( $options['items'][ $item_key ]['cycles'] ) ? $options['items'][ $item_key ]['cycles'] : 0;
		echo '<input type="number" min="0" step="1" class="ztu-input" value="'. intval( $current_cycles ) . '" name="z_text_upfunker_plugin_options[items]['. intval($item_key) .'][cycles]" id="z_text_upfunker_plugin_options_item_'.  intval($item_key)  .'_cycles">';
	}

	public function z_text_upfunker_ia_item_display( $args ) {
		$options = get_option( 'z_text_upfunker_plugin_options' );
		$last_key = 0;

		if ( !empty( $options['items'] ) ) {
			foreach ( $options['items'] as $item_key => $item ) {
				echo '<div class="z-text-upfunker-ia-item">';
				echo '<p><label>' . esc_html__('Selector(s)', 'z-text-upfunker') . '</label>';
				$this->z_text_upfunker_render_item_elem_input( $item_key );
				echo '</p>';

				echo '<p><label>' . esc_html__('Animation style', 'z-text-upfunker') . '</label>';
				$this->z_text_upfunker_render_item_animation_dropdown( $item_key );
				echo '</p>';

				echo '<p><label>' . esc_html__('Max. loops', 'z-text-upfunker') . '</label>';
				$this->z_text_upfunker_render_item_cycles_input( $item_key );
				echo '</p>';

				echo '<div class="z-text-upfunker-btn-remove-ia">-</div>';
				echo '</div>';
			}
			$last_key = max( array_keys( $options['items'] ) );
		} else {
			echo '<p id="no-funk">' . esc_html__('There are currently no elements with funk.', 'z-text-upfunker') . '</p>';
		}

		echo '<div class="z-text-upfunker-ia-item-add-box"><a href="javascript:;" class="z-text-upfunker-btn-add-ia button button-primary" data-last="' . esc_attr($last_key) . '"><i class="dashicons dashicons-plus-alt"></i> ' . esc_html__( 'Add item', 'z-text-upfunker' ) . '</a></div>';
	}

	public static function z_text_upfunker_plugin_options_validate( $input ) {
		$output = array();

		if ( ! empty( $input['items'] ) ) {
			$available_types = self::z_text_upfunker_get_available_anim_types();

			foreach( $input['items'] as $item_key => $item ) {
				$output_item = array();

				if ( isset( $item['elem'] ) ) {
					$output_item['elem'] = sanitize_text_field( $item['elem'] );
				}

				if ( isset( $item['type'] ) ) {
					$output_item['type'] = array_key_exists( $item['type'], $available_types ) ? $item['type'] : 'rand';
				}

				$output_item['cycles'] = isset( $item['cycles'] ) ? intval( $item['cycles'] ) : 0;

				$output['items'][ $item_key ] = $output_item;
			}
		}

		return $output;
	}

	public function z_text_upfunker_add_admin_menu() {
		add_options_page(
			__('WP Text Upfunker', 'z-text-upfunker'),
			'Text Upfunker',
			'manage_options',
			'z_text_upfunker',
			[ $this, 'z_text_upfunker_options_page' ]
		);
	}

	public function z_text_upfunker_options_page() {
		add_filter('admin_footer_text', [ $this, 'z_admin_footer_print_thankyou' ], 900);
		?>
		<div class="wrap">
			<h1 class="ztu-title"><?php esc_html_e('Text Upfunker settings', 'z-text-upfunker'); ?></h1>
			<form action="options.php" method="post">
				<?php
					settings_fields( 'z_text_upfunker_plugin_options' );
					do_settings_sections( 'z_text_upfunker_plugin' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function z_admin_footer_print_thankyou( $data ) {
        return '<p class="zThanks"><a href="https://zodan.nl" target="_blank" rel="noreferrer">' .
                    esc_html__('Made with', 'z-text-upfunker') . 
                    '<svg id="heart" data-name="heart" xmlns="http://www.w3.org/2000/svg" width="745.2" height="657.6" version="1.1" viewBox="0 0 745.2 657.6"><path class="heart" d="M372,655.6c-2.8,0-5.5-1.3-7.2-3.6-.7-.9-71.9-95.4-159.9-157.6-11.7-8.3-23.8-16.3-36.5-24.8-60.7-40.5-123.6-82.3-152-151.2C0,278.9-1.4,217.6,12.6,158.6,28,93.5,59,44.6,97.8,24.5,125.3,10.2,158.1,2.4,190.2,2.4s.3,0,.4,0c34.7,0,66.5,9,92.2,25.8,22.4,14.6,70.3,78,89.2,103.7,18.9-25.7,66.8-89,89.2-103.7,25.7-16.8,57.6-25.7,92.2-25.8,32.3-.1,65.2,7.8,92.8,22.1h0c38.7,20.1,69.8,69,85.2,134.1,14,59.1,12.5,120.3-3.8,159.8-28.5,69-91.3,110.8-152,151.2-12.8,8.5-24.8,16.5-36.5,24.8-88.1,62.1-159.2,156.6-159.9,157.6-1.7,2.3-4.4,3.6-7.2,3.6Z"></path></svg>' .
                    esc_html__('by Zodan', 'z-text-upfunker') .
                '</a></p>';
	}

	public function z_text_upfunker_add_admin_scripts( $hook ) {
		if ( is_admin() ) {
			$plugin_url = plugins_url( '/', __FILE__ );
			$admin_css = $plugin_url . 'assets/admin-styles.css';
			wp_enqueue_style( 'z-text-upfunker-admin-styles', esc_url($admin_css), array(), ZTEXTUPFUNKER_VERSION );

			$admin_js = $plugin_url . 'assets/admin-scripts.js';
			wp_enqueue_script( 'z-text-upfunker-admin-scripts', esc_url($admin_js), array('jquery'), ZTEXTUPFUNKER_VERSION, true );

			wp_localize_script( 'z-text-upfunker-admin-scripts', 'zTextUpfunkerAdminParams', array(
				'availableAnimTypes' => self::z_text_upfunker_get_available_anim_types()
			) );
		}
	}
}

