<?php

/**
 * Settings page for Z Text Upfunker
 *
 * Author: Zodan
 * Author URI: https://zodan.nl
 * License: GPL2+
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}





/*
 * Register all settings
 *
 *
 */
if ( !function_exists( 'z_text_upfunker_register_settings' ) ) {

    function z_text_upfunker_register_settings() {
		
		$settings_args = array(
			'type' => 'array',
			'description' => '',
			'sanitize_callback' => 'z_text_upfunker_plugin_options_validate',
			'show_in_rest' => false
		);
        register_setting( 'z_text_upfunker_plugin_options', 'z_text_upfunker_plugin_options', $settings_args);

		// Voeg settings section toe
		add_settings_section(
			'z_text_upfunker_main_section',
			 esc_html__('Global settings', 'z-text-upfunker'),
			'z_text_upfunker_main_section_text',
			'z_text_upfunker_plugin'
		);

        // Field: Element selection
		add_settings_field(
			'z_text_upfunker_select_elem',
            '<label for"z_text_upfunker_plugin_options_elem">'. esc_html__('Element to animate', 'z-text-upfunker') . '</label><span class="description">' .
                esc_html__( 'css selector', 'z-text-upfunker' ) . '</span>', 
			'z_text_upfunker_render_element_input',
			'z_text_upfunker_plugin',
			'z_text_upfunker_main_section'
		);

        // Field: Animation type selection
		add_settings_field(
			'z_text_upfunker_select_animation_type',
            '<label for"z_text_upfunker_plugin_options_type">'. esc_html__('Animation style', 'z-text-upfunker') . '</label>',
			'z_text_upfunker_render_animation_dropdown',
			'z_text_upfunker_plugin',
			'z_text_upfunker_main_section'
		);

        // Field: Number of cycles (maxLoops)
        add_settings_field(
            'z_text_upfunker_input_cycles',
            '<label for"z_text_upfunker_plugin_options_cycles">'. esc_html__('Max. number of loops', 'z-text-upfunker') . '</label><span class="description">' . esc_html__('Use a zero for infinite loops.', 'z-text-upfunker' )   ,
            'z_text_upfunker_render_cycles_input',
            'z_text_upfunker_plugin',
            'z_text_upfunker_main_section'
        );

		// Voeg settings section toe
		add_settings_section(
			'z_text_upfunker_faq_section',
			 esc_html__('Frequently asked questions', 'z-text-upfunker'),
			'z_text_upfunker_faq_section_text',
			'z_text_upfunker_plugin'
		);
    }

    add_action( 'admin_init', 'z_text_upfunker_register_settings' );



    function z_text_upfunker_main_section_text() { 
        echo '<p>' . esc_html__('Here you can set all the options for using the WordPress Text Upfunker.', 'z-text-upfunker') . '</p>';
        echo '<ol>';
        echo '<li>';
        echo esc_html__('Enter the html element (selector) that always has the funky animation.', 'z-text-upfunker') . ' ';
        echo wp_kses(
            __('For example, you could use a html tag like <code>h2</code> to have the animation applied to all secondary headings.', 'z-text-upfunker'),
            array(
                'strong' => array(),
                'em' => array(),
                'br' => array(),
                'code' => array(),
            )
        );
        echo '<br>';   
        echo wp_kses(
            __('Or you could use a selector like <code>#someId</code> for all elements with id "someId" or <code>.someClassName</code> for all elements with the class "someClassName".', 'z-text-upfunker'),
            array(
                'strong' => array(),
                'em' => array(),
                'br' => array(),
                'code' => array(),
            )
        );
        echo '</li>';
        echo '<li>' . esc_html__('Select the style that you want to apply to the selected elements above.', 'z-text-upfunker') . '</li>';
        echo '<li>' . esc_html__('Enter the maximum number of loops you want the animation to play. If nothing is entered, the animation will play infinitely.', 'z-text-upfunker') . '</li>';
        echo '</ol>';
        echo '<p>' . esc_html__('Have fun!', 'z-text-upfunker') . '</p>';
        echo '<p>&nbsp;</p>';
    }

    function z_text_upfunker_render_animation_dropdown() {
        $options = get_option( 'z_text_upfunker_plugin_options' );
        $selected_type = isset( $options['type'] ) ? $options['type'] : 'rand';

        $available_types = array(
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
        echo '<select name="z_text_upfunker_plugin_options[type]" id="z_text_upfunker_plugin_options_type">'; 
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

    function z_text_upfunker_render_element_input() {
        $options = get_option( 'z_text_upfunker_plugin_options' );
        $enabled_elem = isset( $options['elem'] ) ? $options['elem'] : '';

        echo '<input type="text" value="'. esc_attr( $enabled_elem ) . '" name="z_text_upfunker_plugin_options[elem]" id="z_text_upfunker_plugin_options_elem">';
        echo ' ('.esc_html__('Leave empty to disable the Text Upfunker', 'z-text-upfunker').')';
    }

    function z_text_upfunker_render_cycles_input() { 
        $options = get_option( 'z_text_upfunker_plugin_options' );
        $current_cycles = isset( $options['cycles'] ) ? $options['cycles'] : 0;

        echo '<input type="number" min="0" value="'. intval( $current_cycles ) . '" name="z_text_upfunker_plugin_options[cycles]" id="z_text_upfunker_plugin_options_cycles">';
        echo ' '.esc_html__('times', 'z-text-upfunker');
    }


    function z_text_upfunker_plugin_options_validate( $input ) {
        $output = array();

        if ( isset( $input['elem'] ) ) {
            $output['elem'] = sanitize_text_field( $input['elem'] );
        }

        if ( isset( $input['type'] ) ) {
            $available_types = array(
                'none',
                'rand',
                'code',
                'fade',
                'flip',
                'sink',
                'pop',
                'flkr',
                'circ',
            );
            if( in_array( $input['type'], $available_types) ) {
                $output['type'] = $input['type'];
            } else {
                $output['type'] = 'rand'; // make random the default
            }
        }

        if ( isset( $input['cycles'] ) ) {
            $output['cycles'] = intval( $input['cycles'] );
        } else {
            $output['cycles'] = 0;
        }

        return $output;
    }


    function z_text_upfunker_faq_section_text() { 
        echo '<details class="z-ts-faq"><summary><h3>';
        esc_html_e('Can I use different animation styles for different elements?', 'z-text-upfunker');
        echo '</h3></summary><p>';
        echo wp_kses(
            __('Not <strong>yet</strong> unfortunately.<br>But expect that to be the first new feature we will add to the next version.', 'z-text-upfunker'),
            array(
                'strong' => array(),
                'em' => array(),
                'br' => array(),
                'code' => array(),
            )
        );
        echo '</p>';
        echo '</details>';

        echo '<details class="z-ts-faq"><summary><h3>';
        esc_html_e('Can I apply the funky animation to multiple (different) elements?', 'z-text-upfunker');
        echo '</h3></summary><p>';
        esc_html_e('Yes, you can.', 'z-text-upfunker');
        echo '<br>';
        esc_html_e('By entering multiple selectors, separated by a comma, you can have the animation applied to all those elements.', 'z-text-upfunker');
        echo '</p><p>';
        echo wp_kses(
             __('For example, enter <code>h1, h2, .someClassName</code> to apply the animation to all h1 and h2 elements and to all elements with the class "someClassName".', 'z-text-upfunker'),
            array(
                'strong' => array(),
                'em' => array(),
                'br' => array(),
                'code' => array(),
            )
        );
        echo '</p>';
        echo '</details>';

        echo '<details class="z-ts-faq"><summary><h3>';
        esc_html_e('Which animation types are available?', 'z-text-upfunker');
        echo '</h3></summary><p>';
        esc_html_e('Currently you can have the words and characters appear from scrambled code.', 'z-text-upfunker');
        esc_html_e('Or you can have them: Fade in, Flip in, Sink in, Pop up, Flicker or Circle in.', 'z-text-upfunker');
        echo '</p>';
        echo '</details>';



    }



    function z_text_upfunker_add_admin_menu() {
        add_options_page(
            __('WP Text Upfunker', 'z-text-upfunker'),
            'Text Upfunker',
            'manage_options',
            'z_text_upfunker',
            'z_text_upfunker_options_page'
        );
    }
    add_action( 'admin_menu', 'z_text_upfunker_add_admin_menu' );


    function z_text_upfunker_options_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Text Upfunker settings', 'z-text-upfunker'); ?></h1>
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


    /*
    * Enqueue scripts and styles
    *
    *
    */
    add_action( 'admin_enqueue_scripts', 'z_text_upfunker_add_admin_scripts' );
    function z_text_upfunker_add_admin_scripts( $hook ) {
        if ( is_admin() ) {
            $plugin_url = plugins_url( '/', __FILE__ );
            $admin_css = $plugin_url . 'assets/admin-styles.css';
            wp_enqueue_style( 'z-text-upfunker-admin-styles', esc_url($admin_css), array(), '1.0' );
        }
    }


}
