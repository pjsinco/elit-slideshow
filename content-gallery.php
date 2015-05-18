<?php 
/**
 * @package elit
 */
?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


          <div class="story">
            <header class="story-header">
              <h5 class="story-header__kicker"><?php echo wptexturize( get_post_meta( $post->ID, 'elit_kicker', true ) ); ?></h5>
              <?php the_title('<h1 class="story-header__title">', '</h1>', true); ?>
              <h3 class="story-header__teaser"><?php echo wptexturize( get_the_excerpt() ); ?></h3>
              <div>
                <div class="story-meta">
                  <?php elit_byline(); ?>
                  <?php elit_posted_on(); ?>
                  <?php elit_comments_link(); ?>
                </div> <!-- story-meta -->
              </div>
            </header>
            <div class="story__body-text--full-width">

              <?php the_content(); ?>
            </div> <!-- story__body-text -->
            
            <footer class="story-footer--full-width"> 
              <?php elit_social_links( $meta, $link, $title, $thumb_id, false ); ?>
              <?php elit_story_footer(); ?>
            </footer>

          </div> <!-- .story -->
        </article>
