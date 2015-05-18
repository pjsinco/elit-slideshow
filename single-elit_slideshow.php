<?php get_header(); ?>

<?php get_template_part('sidebar', 'leaderboard'); ?>

    <div id="main" class="content">
      <section id="primary" class="content__primary">

        <?php while(have_posts()): the_post(); ?>

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
        <?php
            $shortcode = 
              get_post_meta( $post->ID, 'elit_featured_slideshow_shortcode', true );
            if ( !empty( $shortcode ) ):
              echo do_shortcode( $shortcode );
            endif;
          ?>
               <?php the_content(); ?>
             </div> <!-- story__body-text -->
             
             <footer class="story-footer--full-width"> 
               <?php elit_social_links( $meta, $link, $title, $thumb_id, false ); ?>
               <?php elit_story_footer(); ?>
             </footer>

           </div> <!-- .story -->
         </article>
          <?php //get_template_part('content', get_post_format()); ?>

    			<?php
    				// If comments are open or we have at least one comment, load up the comment template
    				if ( comments_open() || get_comments_number() ) :
    					comments_template('/comments-full-width.php');
    				endif;
    			?>

        <?php endwhile; ?>

      </section> <!-- #primary -->


<!--       temp; make into a sidebar template? -->
      <section id="secondary" class="content__secondary">

<?php get_sidebar('article_full_width'); ?>
      </section>
    </div> <!-- #main -->

<?php get_footer(); ?>
