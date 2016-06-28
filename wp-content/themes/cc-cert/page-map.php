<?php
/**
* Template Name: Certification Map
* For page structure version of the certification
*/

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <?php get_template_part( 'template-parts/wrapper', 'top' ); ?>

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

				      <?php get_template_part( 'template-parts/cover', 'post' ); ?>

              <?php get_template_part( 'template-parts/content', 'page' ); ?>

              <?php get_template_part( 'template-parts/comments-map' ); ?>

            </main><!-- #main -->
        </div><!-- #primary -->

<?php endwhile; // End of the loop. ?>
<?php get_footer();
