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
    ),
    $atts
  );

  $ids = explode( ',', $a['ids'] );

  $output  = '<div class="elit-slideshow">';
  $output .= '<ul class="elit-slideshow__list" id="elit-slideshow">';
  
  //foreach ( $ids as $id ) {
    $output .= '<li class="elit-slideshow__item">';
    $output .= '<img src="http://elit.dev/wp-content/uploads/2015/03/do-day-mckenna.jpg" />';
    $output .= '</li>';

    $output .= '<li class="elit-slideshow__item">';
    $output .= '<img src="http://elit.dev/wp-content/uploads/2015/03/do-day-hashtag.jpg" />';
    $output .= '</li>';

    $output .= '<li class="elit-slideshow__item">';
    $output .= '<img src="http://elit.dev/wp-content/uploads/2015/03/do-day-7.jpg" />';
    $output .= '</li>';

  //}
  $output .= "</ul>";
  $output .= "</div>";

  echo $output;

  wp_enqueue_script( 'responsive-slides-js' );
  add_action( 'wp_footer', 'elit_call_responsive_slides_js', 50);
  
}

add_shortcode( 'elit-slideshow' , 'elit_responsive_slides_shortcode' );

function elit_call_responsive_slides_js() {
  $output  = '<script>';
  $output .= 'jQuery(function() {';
  $output .= 'jQuery(\'#elit-slideshow\').responsiveSlides();';
  $output .= '});';
  $output .= '</script>';

  echo $output;
}
