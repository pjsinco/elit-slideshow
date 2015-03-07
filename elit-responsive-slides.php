<?php
/**
 * Plugin Name: Elit Responsive Slides
 * Plugin URI: 
 * Description: For using ResponsiveSlides.js
 * Version: 0.0.1
 * Author: Patrick Sinco
 * Author URI: 
 * License: GPL2
 */

class Elit_Responsive_Slides {

  public function __construct() {
    add_action( 'wp_enqueue_scripts' , array( $this, 'add_scripts' ) );
    //add_action( 'wp_enqueue_scripts' , array( $this, 'add_styles' ) );
    add_action( 'plugins_loaded' , array( $this, 'load_shortcode' ) );
  }

  public function add_scripts() {
    if ( is_admin() ) { 
      return;
    }

    wp_register_script( 
      'responsive-slides-js', 
      plugin_dir_url( __FILE__ ) . 'js/responsiveslides.min.js',
      array( 'jquery' ),
      false,
      true
    );
  }

  public function add_styles() {
    if ( is_admin() ) { 
      return;
    }
  }

  public function load_shortcode() {
    require_once( 'elit-responsive-slides-shortcode.php' );
  }
} // eoc

$elit_responsive_slider = new Elit_Responsive_Slides();
