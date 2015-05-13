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

function call_elit_slideshow() {
  $GLOBALS['elit-slideshow'] = new Elit_Slideshow();
}

if ( is_admin() ) {
  // load metabox
  add_action( 'load-post.php' , 'call_elit_slideshow' );
  add_action( 'load-post-new.php' , 'call_elit_slideshow' );
}

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

    add_action( 
      'add_meta_boxes', 
      array( $this, 'elit_add_featured_slideshow_meta_box' )
    );

    add_action( 
      'save_post', 
      array( $this, 'elit_save_featured_slideshow_meta' )
    );

    add_shortcode( 'elit-slideshow', array( $this, 'elit_slideshow_shortcode' ) );
    global $wp_filter;
    //d( $wp_filter);
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

  /**
   * Meta box for setting a featured slideshow, one that appears
   * above the headline in the story.
   *
   */
  
  function elit_featured_slideshow_meta_box_setup() {
  }
  
  function elit_add_featured_slideshow_meta_box() {
    add_meta_box(
      'elit-featured-slideshow',
      esc_html( 'Featured slideshow' ),
      array( $this, 'elit_featured_slideshow_meta_box' ),
      'post',
      'side',
      'low'
    );
  }
  
  function elit_featured_slideshow_meta_box( $object, $box ) {
    wp_nonce_field( basename(__FILE__), 'elit_featured_slideshow_nonce' );
    ?>
    <p>
      <label for="widefat">Enter the shortcode for a featured slideshow, one that appears above the headline.</label>
      <br />
      <textarea class="widefat"  name="elit-featured-slideshow" id="elit-featured-slideshow" rows="5"><?php echo esc_attr( get_post_meta( $object->ID, 'elit_featured_slideshow', true ) ); ?></textarea>
    </p>
    <?php 
  }

  public function elit_save_featured_slideshow_meta( $post_id ) {
    global $post;
    // verify the nonce
    if ( !isset( $_POST['elit_featured_slideshow_nonce'] ) || 
      !wp_verify_nonce( $_POST['elit_featured_slideshow_nonce'], basename( __FILE__ ) )
    ) {
        // instead of just returning, we return the $post_id
        // so other hooks can continue to use it
        return $post_id;
    }
  
    // get post type object
    $post_type = get_post_type_object( $post->post_type );
  
    // if the user has permission to edit the post
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
      return $post_id;
    }
  
    // get the posted data and sanitize it
    $new_meta_value = 
      ( isset($_POST['elit-featured-slideshow'] ) ? $_POST['elit-featured-slideshow'] : '' );

  
    // set the meta key
    $meta_key = 'elit_featured_slideshow';
  
    // get the meta value as a string
    $meta_value = get_post_meta( $post_id, $meta_key, true);
  
    // if a new meta value was added and there was no previous value, add it
    if ( $new_meta_value && $meta_value == '' ) {
      add_post_meta( $post_id, $meta_key, $new_meta_value, true);
    } elseif ($new_meta_value && $new_meta_value != $meta_value ) {
      // so the new meta value doesn't match the old one, so we're updating
      update_post_meta( $post_id, $meta_key, $new_meta_value );
    } elseif ( $new_meta_value == '' && $meta_value) {
      // if there is no new meta value but an old value exists, delete it
      delete_post_meta( $post_id, $meta_key, $meta_value );
    }
  }


  
} // eoc

$elit_responsive_slider = new Elit_Slideshow();
$GLOBALS['elit-slideshow'] = new Elit_Slideshow();
