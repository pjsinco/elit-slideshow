<?php
/**
 * Plugin Name: Elit Slideshow
 * Plugin URI: https://github.com/pjsinco/elit-slideshow
 * Description: Slideshow plugin
 * Version: 0.1.0
 * Author: Patrick Sinco
 * Author URI: 
 * License: GPL2
 */

function elit_check_for_slideshow( $single_template ) {
  global $post;
  $slideshow = get_post_meta($post->ID, 'elit_featured_slideshow', true);

  if ( !empty( $slideshow ) && has_shortcode( $slideshow, 'elit-slideshow' ) ) {
    add_action( 'wp_head' , 'elit_add_no_fouc_snippet' );
    $single_template = get_template_directory() . '/single-elit_slideshow.php';
  }

  return $single_template;
  
}
add_filter( 'single_template', 'elit_check_for_slideshow' );

/**
 * Prevent flash of unstyled content (fouc)
 *
 * https://gist.github.com/johnpolacek/3827270
 *
 */
function elit_add_no_fouc_snippet() {
  $output  = '<style type="text/css">' . PHP_EOL;
  $output .= '.no-fouc { display: none; }' . PHP_EOL;
  $output .= '</style>' . PHP_EOL;

  $output .= '<script>' . PHP_EOL;
  $output .= 'document.documentElement.className = \'no-fouc\'' . PHP_EOL;
  $output .= '</script>' . PHP_EOL;

  echo $output;
}

class ElitSlideshow {
  private $ids;      // array
  private $features; // array
  private $note; // array

  public function __construct() {
    // Owl Carouseljs options
    $this->features = array(
      'singleItem' => true,
      'slideSpeed' => 350,   // not user changeable
      'paginationSpeed' => 350,   // not user changeable
      'transitionStyle' => 'fade',   // not user changeable
      'addClassActive' => true,   // not user changeable
    );

    add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
    add_shortcode( 'elit-slideshow', array( $this, 'elit_slideshow_shortcode' ) );
    add_action( 'add_meta_boxes' , array( $this, 'add_meta_box' ) );
    add_action( 'save_post' , array( $this, 'save_meta' ) );

    global $wp_filter;
  }


  // instantantiate our an instance of our class
  // http://wordpress.stackexchange.com/questions/70055/
  //    best-way-to-initiate-a-class-in-a-wp-plugin
  public static function init() {
    $class = __CLASS__;
    new $class;
  }

  public function add_meta_box() {
    add_meta_box(
      'elit-featured-slideshow',
      esc_html( 'Featured slideshow shortcode' ),
      array( $this, 'render_meta_box_content' ),
      'post',
      'side',
      'low'
    );
  }

  public function render_meta_box_content( $object, $box ) {
    wp_nonce_field( basename(__FILE__), 'elit_featured_slideshow_nonce' );
    ?>
    <p>
      <label for="widefat">Ex.: [elit-slideshow ids="180298, 180291, 180265"]. The IDs are the image IDs to use in the slideshow.</label>
      <br />
      <textarea class="widefat"  name="elit-featured-slideshow" id="elit-featured-slideshow" rows="5"><?php echo esc_attr( get_post_meta( $object->ID, 'elit_featured_slideshow', true ) ); ?></textarea>
    </p>
    <?php 
  }
  public function save_meta( $post_id ) {
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
  
  public function register_scripts() {
    if ( is_admin() ) { 
      return;
    }

    wp_register_script( 
      'owl-carousel-js', 
      plugin_dir_url( __FILE__ ) . 'inc/owl-carousel/owl.carousel.min.js',
      array( 'jquery' ),
      false,
      true
    );

    wp_register_script( 
      'elit-slideshow-js', 
      plugin_dir_url( __FILE__ ) . 'js/elit-slideshow.js',
      array( 'owl-carousel-js' ),
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

    wp_register_style(
      'owl-carousel-theme',
      plugin_dir_url( __FILE__ ) . 'inc/owl-carousel/owl.theme.css',
      array(),
      false,
      'screen'
    );

    wp_register_style(
      'owl-carousel-transitions',
      plugin_dir_url( __FILE__ ) . 'inc/owl-carousel/owl.transitions.css',
      array(),
      false,
      'screen'
    );

    wp_register_style(
      'owl-carousel',
      plugin_dir_url( __FILE__ ) . 'inc/owl-carousel/owl.carousel.css',
      array(),
      false,
      'screen'
    );
  }

  public function elit_filter_the_content($content) {
    $note  = '<div class="elit-slideshow__note-wrapper">';
    $note .= '<p class="elit-slideshow__note">' . $this->note;
    $note .= '</p></div>';
    return $note . $content;
  }

  public function elit_slideshow_shortcode( $atts ) {
    $a = shortcode_atts( array( 
      'note' => '',
      'ids' => array(), 
      'features' => array(), 
      ), $atts
    );

    $this->note = $a['note'];
    
    // if we have a note, we're going to add it by filtering the_content
    if (!empty($this->note)) {
        add_filter('the_content', array($this, 'elit_filter_the_content'));
    }
    
    $this->ids = explode( ',', $a['ids'] );

    if ( !empty( $a['features'] ) ) {
      $this->set_features( $a['features'] );
    }

    $output  = '<div class="elit-slideshow__wrapper">';
    $output .= '<a class=\'elit-slideshow__nav elit-slideshow__prev prev\'>Prev</a>';
    $output .= '<a class=\'elit-slideshow__nav elit-slideshow__next next\'>Next</a>';
    $output .= '<div class="owl-carousel" id="elit-slideshow">';
    
    foreach ( $this->ids as $id ) {
      $attachment = get_post( $id );
      $image_url = wp_get_attachment_image_src( $id, 'elit-large', false ); 
  
      $output .= "<div class=\"elit-slideshow__item\">";
      $output .= "<div class='elit-slideshow__item-title'>$attachment->post_content</div>";
      $output .= '<img class="image__img elit-slideshow__img" src="' . $image_url[0] .  '" />';
      $output .= '<figcaption class="elit-slideshow__caption">';
      $output .= $attachment->post_excerpt;
      $credit = get_post_meta( $attachment->ID, 'elit_image_credit', true );
      if ( !empty( $credit ) ) {
        $output .= sprintf( '<span class="elit-slideshow__credit">%s</span>', $credit );
      }
      $output .= '</figcaption>';
      $output .= '</div>';
    }
    $output .= "</div>";
    $output .= "</div>";

    wp_enqueue_script( 'owl-carousel-js' );
    wp_enqueue_script( 'elit-slideshow-js' );
    wp_enqueue_style( 'elit-slideshow-style' );


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

} // eoc

add_action( 'plugins_loaded', array( 'ElitSlideshow', 'init' ) );
