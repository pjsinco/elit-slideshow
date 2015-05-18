<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package elit
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<div class="comments__wrapper">
  
  <div id="comments" class="comments<?php echo (has_post_format('gallery') ? '--full-width' : ''); ?>">
  	<?php if ( have_comments() ) : ?>
      <h2 class="comments__title">
        <span class="comments__count">

          <?php //echo get_comments_number(); ?>
          <?php 
            $num_comments = get_comments_number();
            if ( $num_comments == 0 ) {

            } else {
              echo $num_comments . '</span>' .
                ( ( $num_comments == 1 ) ? ' comment' : ' comments' );
            }
          ?>
  		</h2>
  
  		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
  		<nav id="comment-nav-above" class="comment-navigation" role="navigation">
  			<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'elit' ); ?></h1>
  			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'elit' ) ); ?></div>
  			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'elit' ) ); ?></div>
  		</nav><!-- #comment-nav-above -->
  		<?php endif; // check for comment navigation ?>

  		<ol class="comments__list">
  			<?php
  				wp_list_comments( array(
  					'style'      => 'ol',
  					'short_ping' => true,
            'callback'   => 'elit_comment',
  				) );
  			?>
  		</ol><!-- .comment-list -->
  
  		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
  		<nav id="comment-nav-below" class="comment-navigation" role="navigation">
  			<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'elit' ); ?></h1>
  			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'elit' ) ); ?></div>
  			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'elit' ) ); ?></div>
  		</nav><!-- #comment-nav-below -->
  		<?php endif; // check for comment navigation ?>
  
  	<?php endif; // have_comments() ?>
  
  	<?php
  		// If comments are closed and there are comments, leave a little note
  		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
  	?>
  		<p class="no-comments"><?php _e( 'Comments are closed.', 'elit' ); ?></p>
  	<?php endif; ?>
  
  		<div class="comments__respond">
    <?php 
      $commenter = wp_get_current_commenter();
      $req = get_option( 'require_name_email' );
      $fields = array(
        'author' => 
          '<p class="comment-form__author">
            <label for="author" class="comment-form__label">Your name' .
              ($req ? '<span class="comment-form__required">*</span>' : '' ) .  '</span> 
            </label>
            <input name="author" type="text" size="30" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '">
          </p>',

        'email' =>
          '<p class="comment-form__email">
            <label for="email" class="comment-form__label">Your email' .
              ($req ? '<span class="comment-form__required">*</span>' : '' ) .  '</span> 
            </label>
            <input name="email" type="text" size="30" id="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '">
          </p>',
        
      );

      $args = array(
        'fields' => $fields,
        'comment_notes_before' => '',
        'comment_notes_after' => '',
        'title_reply' => 'Leave a comment',
        'label_submit' => 'Submit comment',
      );
    ?>
  	<?php comment_form( $args ); ?>
  		</div>
  
  </div><!-- #comments -->
</div><!-- #comments__wrapper -->

