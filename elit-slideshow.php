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

if ( is_admin() ) {
  // load metabox
  add_action( 'load-post.php' , 'call_elit_slideshow' );
  add_action( 'load-post-new.php' , 'call_elit_slideshow' );
}

add_action( 'init' , 'elit_featured_slideshow_cpt' );

function elit_featured_slideshow_cpt() {
  /**
   * SLIDESHOW POST custom post type
   *
   * For displaying a slideshow post
   */

  $labels = array(
    'name'               => 'Featured Slideshow',
    'singular_name'      => 'Featured Slideshow',
    'menu_name'          => 'Featured Slideshow',
    'name_admin_bar'     => 'Featured Slideshow',
    'add_new'            => 'Add new Featured Slideshow',
    'add_new_item'       => 'Add new Featured Slideshow',
    'edit_item'          => 'Edit Featured Slideshow',
    'view_item'          => 'View Featured Slideshow',
    'all_items'          => 'All Featured Slideshows',
    'search_items'       => 'Search Featured Slideshows',
    'not_found'          => 'No Featured Slideshows found',
    'not_found_in_trash' => 'No Featured Slideshows found in trash.',
  );
  
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'exclude_from_search' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_admin_bar' => true,
    'menu_position' => 20,
    'capability_type' => 'post',
    'has_archive' => false,
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'featured-slideshow'),
    'supports' => array( 'revision', 'editor', 'title', 'author', 'thumbnail', 'comments', 'excerpt' ),
    //'register_meta_box_cb' => 'add_elit_slideshow_metaboxes'
  );
  
  register_post_type( 'elit_slideshow', $args );
  //flush_rewrite_rules( 'hard' );
}

add_action( 'add_meta_boxes', 'elit_add_featured_slideshow_shortcode_meta_box' );
function elit_add_featured_slideshow_shortcode_meta_box() {
  add_meta_box(
    'elit-featured-slideshow-shortcode',
    esc_html( 'Slideshow shortcode' ),
    'elit_featured_slideshow_shortcode_meta_box',
    'elit_slideshow',
    'normal',
    'high'
  );
}

function elit_featured_slideshow_shortcode_meta_box( $object, $box ) {
  wp_nonce_field( basename(__FILE__), 'elit_slideshow_shortcode_nonce' );
  ?>
  <p>
    <label for="widefat">Ex.: [elit-slideshow ids="180298, 180291, 180265"]. The IDs are the image IDs to use in the slideshow.</label>
    <br />
    <input class="widefat" type="text" name="elit-featured-slideshow-shortcode" id="elit-featured-slideshow-shortcode" value="<?php echo esc_attr( get_post_meta( $object->ID, 'elit_featured_slideshow_shortcode', true ) ); ?>" />
  </p>
  <?php 
}

function elit_save_featured_slideshow_shortcode_meta( $post_id, $post ) {
  // verify the nonce
  if ( !isset( $_POST['elit_slideshow_shortcode_nonce'] ) || 
    !wp_verify_nonce( $_POST['elit_slideshow_shortcode_nonce'], basename( __FILE__ ) )
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
    ( isset($_POST['elit-featured-slideshow-shortcode'] ) ? $_POST['elit-featured-slideshow-shortcode'] : '' );

  // set the meta key
  $meta_key = 'elit_featured_slideshow_shortcode';

  // get the meta value as a string
  $meta_value = get_post_meta( $post_id, $meta_key, true);

  // if a new meta value was added and there was no previous value, add it
  if ( $new_meta_value && $meta_value == '' ) {
    //add_post_meta( $post_id, 'elit_foo', 'bar');
    add_post_meta( $post_id, $meta_key, $new_meta_value, true);
  } elseif ($new_meta_value && $new_meta_value != $meta_value ) {
    // so the new meta value doesn't match the old one, so we're updating
    update_post_meta( $post_id, $meta_key, $new_meta_value );
  } elseif ( $new_meta_value == '' && $meta_value) {
    // if there is no new meta value but an old value exists, delete it
    delete_post_meta( $post_id, $meta_key, $meta_value );
  }
}
add_action( 'save_post', 'elit_save_featured_slideshow_shortcode_meta', 10, 2 );

add_filter( 'template_include', 'include_template_function', 1 );
function include_template_function( $template_path ) {
  if ( get_post_type() == 'elit_slideshow' ) {
    if ( is_single() ) {
      // check if the file exists in the theme,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array( 'single-elit_slideshow.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . 'single-elit_slideshow.php';
      }
    }
  }

  return $template_path;
}


class ElitSlideshow {
  private $ids;      // array
  private $features; // array

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

    global $wp_filter;
  }

  // instantantiate our an instance of our class
  // http://wordpress.stackexchange.com/questions/70055/
  //    best-way-to-initiate-a-class-in-a-wp-plugin
  public function init() {
    $class = __CLASS__;
    new $class;
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

    $output  = '<div class="elit-slideshow__wrapper">';
    $output .= '<a class=\'elit-slideshow__nav prev\'>Prev</a>';
    $output .= '<a class=\'elit-slideshow__nav elit-slideshow__next next\'>Next</a>';
    $output .= '<div class="owl-carousel" id="elit-slideshow">';
    
    foreach ( $this->ids as $id ) {
      $attachment = get_post( $id );
      $image_url = wp_get_attachment_image_src( $id, 'elit-super', false ); 
  
      $output .= "<div class=\"elit-slideshow__item\">";
      $output .= '<img class="image__img elit-slideshow__img" src="' . $image_url[0] .  '" />';
      $output .= '<figcaption class="elit-slideshow__caption">';
      $output .= $attachment->post_excerpt;
      $output .= sprintf(
        ' <small>(%s)</small>', 
        get_post_meta( $attachment->ID, 'elit_image_credit', true )
      );
      $output .= '</p>';
      $output .= '</div>';
  
    }
    //$output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";
//    $output .= "<div class='elit-slideshow__info'>";
//    $output .= "<p class=\"elit-slideshow__caption\"></p>";
//    $output .= "</div>";

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
