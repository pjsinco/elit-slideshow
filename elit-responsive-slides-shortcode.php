<?php 

/**
 * Shortcode for Elit Responsive Slides
 *
 * @package Elit Responsive Slides
 */

function elit_responsive_slides_shortcode( $atts ) {

  $a = shortcode_atts( 
    array(
      'ids' => '',
      'auto' => true,
      'timeout' => 4000,
      'pager' => true,
      'nav' => true,
      'random' => false,
      'pause' => true,
      'pauseControls' => true,
      'prevText' => 'Previous',
      'nextText' => 'Next',
      'maxwidth' => '728',
      //'navContainer' => '',
      //'manualControls' => '',
      'namespace' => 'rslides',
      //'before' => '',
      //'after' => '',
    ),
    $atts
  );

  $ids = explode( ',', $a['ids'] );

  $output  = '<div class="elit-slideshow">';
  $output .= '<ul class="elit-slideshow__list" id="elit-slideshow">';
  
  foreach ( $ids as $id ) {
    $attachment = get_post( $id );
    $image_url = wp_get_attachment_image_src( $id, 'elit-large', false ); 
    $output .= '<li class="elit-slideshow__list-item">';
    $output .= '<img class="image__img elit-slideshow__img" src="' . $image_url[0] .  '" />';
    $output .= '</li>';

  }
  $output .= "</ul>";
  $output .= "</div>";


  wp_enqueue_script( 'responsive-slides-js' );
  //add_action( 'wp_footer', 'elit_call_responsive_slides_js', 50);
  add_action( 
    'wp_footer', 
    function() use ( $a ) {
      $script  = '<script>';
      $script .= 'jQuery(function() {';
      $script .= 'jQuery(\'#elit-slideshow\').responsiveSlides({';
      $script .= 'auto: ' . $a['auto'] . ',';
      //$script .= 'auto: true,';
      $script .= 'timeout: ' . $a['timeout'] . ',';
      //$script .= 'timeout: 4000,';
      $script .= 'pager: ' . $a['pager'] . ',';
      //$script .= 'pager: true,';
      $script .= 'nav: ' . $a['nav'] . ',';
      //$script .= 'maxwidth: ' . $a['maxwidth'] . ',';
      $script .= 'maxwidth: "728"';
      $script .= '});';
      $script .= '});';
      $script .= '</script>';
      echo $script;
    } 
  );
  return $output;
}
add_shortcode( 'elit-slideshow' , 'elit_responsive_slides_shortcode' );

