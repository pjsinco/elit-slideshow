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

  private $namespace;
  private $maxwidth; 

  public function __construct() {
    $this->namespace = 'rslides';
    $this->maxwidth = '768';

    add_action( 'wp_enqueue_scripts' , array( $this, 'add_scripts' ) );
    add_action( 'wp_enqueue_scripts' , array( $this, 'add_styles' ) );
    add_shortcode( 'elit-slideshow', 
      array($this, 'elit_responsive_slides_shortcode' ) );
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

    wp_register_style(
      'elit-responsive-slides-style',
      plugin_dir_url( __FILE__ ) . 'css/elit-responsive-slides.css',
      array(),
      false,
      'screen'
    );
  }

  public function elit_responsive_slides_shortcode( $atts ) {
    $a = $this->get_atts( $atts );
    $ids = explode( ',', $a['ids'] );

    $output  = '<div class="elit-slideshow">';
    $output .= '<div class="elit-slideshow__wrapper">';
    $output .= '<ul class="elit-slideshow__list" id="elit-slideshow">';
    
    foreach ( $ids as $id ) {
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
  
  
    wp_enqueue_script( 'responsive-slides-js' );
    wp_enqueue_style( 'elit-responsive-slides-style' );
    add_action( 
      'wp_footer', 
      function() use ( $a ) {
        $script  = '<script>';
        $script .= 'jQuery(document).ready(function() {';
        $script .= 'jQuery(\'#elit-slideshow\').responsiveSlides({';
        $script .= 'auto: ' . ($a['auto'] ? 'true' : 'false') . ', ';
        $script .= 'timeout: ' . (int) $a['timeout'] . ', ';
        $script .= 'pager: ' . (int) $a['pager'] . ', ';
        $script .= 'nav: ' . (int) $a['nav'] . ', ';
        $script .= 'random: ' . (int) $a['random'] . ', ';
        $script .= 'pause: ' . (int) $a['pause'] . ', ';
        $script .= 'pauseControls: ' . (int) $a['pauseControls'] . ', ';
        $script .= "prevText: '{$a['prevText']}', ";
        $script .= "nextText: '{$a['nextText']}', ";
        $script .= "maxwidth: '{$a['maxwidth']}', ";
        $script .= "namespace: '{$a['namespace']}'";
        $script .= '});';
        $script .= '});';
        $script .= '</script>';
        echo $script;
      },
      20
    );
    return $output;
  }

/**
 * Parse the attributes passed in with the shortcode
 *
 */
function get_atts( $atts ) {


  $settings = shortcode_atts( 
    array(
      'ids' => '',
      'auto' => false,
      'timeout' => 4000,
      'pager' => false,
      'nav' => true,
      'random' => false,
      'pause' => true,
      'pauseControls' => true,
      'prevText' => 'Previous',
      'nextText' => 'Next',
      'maxwidth' => $this->maxwidth,
      'namespace' => $this->namespace,
    ),
    $atts
  );


  return $settings;
}

} // eoc

//$elit_responsive_slider = new Elit_Responsive_Slides();
$GLOBALS['elit-slideshow'] = new Elit_Responsive_Slides();

