<?php
/**
 * Plugin Name: Process Timeline Widget
 * Description: Adds a Process Timeline custom widget to Elementor.
 * Version:     1.0.0
 * Author: Indranil Mondal
 * Author URI: https://github.com/Indranil-Mondal 
 * Requires Plugins: elementor
 * Text Domain: process-timeline-widget
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Bail if Elementor is not active
add_action( 'plugins_loaded', function() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-warning"><p><strong>Process Timeline Widget</strong> requires Elementor to be installed and activated.</p></div>';
        } );
        return;
    }

    // Register the widget
    add_action( 'elementor/widgets/register', function( $widgets_manager ) {
        require_once __DIR__ . '/widget.php';
        $widgets_manager->register( new Process_Timeline_Widget() );
    } );

    // Enqueue GSAP on the frontend
    add_action( 'wp_enqueue_scripts', function() {
        wp_enqueue_script(
            'gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
            [],
            '3.12.5',
            true
        );
        wp_enqueue_script(
            'gsap-scrolltrigger',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
            [ 'gsap' ],
            '3.12.5',
            true
        );
        wp_enqueue_script(
            'process-timeline-anim',
            plugin_dir_url( __FILE__ ) . 'animation.js',
            [ 'gsap-scrolltrigger' ],
            '1.0.0',
            true
        );
    } );
} );
