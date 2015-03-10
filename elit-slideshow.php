<?php
/**
 * Plugin Name: Elit Slideshow
 * Plugin URI: 
 * Description: Slideshow plugin
 * Version: 0.0.1
 * Author: Patrick Sinco
 * Author URI: 
 * License: GPL2
 */

class Elit_Slideshow {

  private $ids;      // array
  private $features; // array

  public function __construct() {
    // ResponsiveSlides.js options
    $this->features = array(
      'auto'     => false,
      'speed'    => 500,   // not user changeable
      'timeout'  => 4000,  // not user changeable
      'pager'    => false,
      'nav'      => true,
      'random'   => false,
      'pause'    => false,
      'maxwidth' => '768', // not user changeable
    );

    add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
    add_shortcode( 'elit-slideshow', array( $this, 'elit_slideshow_shortcode' ) );
  }

  public function register_scripts() {
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

  public function register_styles() {
    if ( is_admin() ) { 
      return;
    }

    wp_register_style(
      'elit-slideshow-style',
      plugin_dir_url( __FILE__ ) . 'css/elit-slideshow.css',
      array(),
      false,
      'screen'
    );
  }

  public function elit_slideshow_shortcode( $atts ) {
    $a = shortcode_atts( array( 
      'ids' => array(), 
      'features' => array(), 
      ), $atts
    );

    $this->ids = explode( ',', $a['ids'] );

    if ( !empty( $a['features'] ) ) {
      $this->set_features( $a['features'] );
    }

    $output  = '<div class="elit-slideshow">';
    $output .= '<div class="elit-slideshow__wrapper">';
    $output .= '<ul class="elit-slideshow__list" id="elit-slideshow">';
    
    foreach ( $this->ids as $id ) {
      $attachment = get_post( $id );
      $image_url = wp_get_attachment_image_src( $id, 'elit-large', false ); 
  
      $output .= '<li class="elit-slideshow__list-item">';
      $output .= '<img class="image__img elit-slideshow__img" src="' . $image_url[0] .  '" />';
      $output .= '<p class="elit-slideshow__caption">';
      $output .= $attachment->post_excerpt;
      $output .= sprintf(
        ' <small>(%s)</small>', 
        get_post_meta( $attachment->ID, 'elit_image_credit', true )
      );
      $output .= '</p>';
      $output .= '</li>';
  
    }
    $output .= "</ul>";
    $output .= "</div>";
    $output .= "</div>";

    //$this->create_script();
  
    wp_enqueue_script( 'responsive-slides-js' );
    wp_enqueue_style( 'elit-slideshow-style' );
    add_action( 'wp_footer', array( $this, 'create_script' ), 20 );

    return $output;
  }

  /**
   * Set the user-set options for the slideshow
   *
   */
  public function set_features( $features ) {
    $f = explode( ',', $features );
    foreach ( $f as $feature ) {
      $feature = trim( $feature );
      $this->features[$feature] = true;
    }
  }

  public function create_script() {
    $script  = '<script>';
    $script .= 'jQuery(document).ready(function() {';
    $script .= 'jQuery(\'#elit-slideshow\').responsiveSlides(';
    $script .= json_encode( $this->features );
    $script .= ');';
    $script .= '});';
    $script .= '</script>';

    echo $script;
  }

} // eoc

//$elit_responsive_slider = new Elit_Slideshow();
$GLOBALS['elit-slideshow'] = new Elit_Slideshow();
