<?php
/**
* Template Name: Certification Module Unit
* For page structure version of a unit within a certification module
* Add a gravity form for the feedback for cc version
*/



/* ----- Get parent info only for use with feedback gravity form ----- */
global $post;

// do we have a parent?
$pid = wp_get_post_parent_id( get_the_ID() );

if ( !$pid ) {
	// if we do not have a parent, use the current page
	$pid =  $post->ID;
}

// get us some post stuff so we can fetch a title
$parent = get_post( $pid );

// set the certification name
$certification = ucfirst($parent->post_name);

// add a flag for the gform to indicate this is a module page and to include its title
$extra_param = '&amp;unit=' .  get_the_title();

/* ----- End parent info only for use with feedback gravity form ----- */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <?php get_template_part( 'template-parts/wrapper', 'top' ); ?>

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
            

			  <?php get_template_part( 'template-parts/cover', 'post' ); ?>

              <?php get_template_part( 'template-parts/content', 'page' ); ?>


			  <!-- start form for feedback -->
			  <div class="entry-content" id="certsuggest">
			  	<h2>Feedback and Suggestions for <?php the_title( '"', '"' ); ?></h2>
			  
			  	<?php echo do_shortcode('[gravityforms id="2" title="false" ajax="true" field_values="referer='  . get_permalink() . '&amp;certification=' . $certification . $extra_param . '"]');?>
			  </div>
			    <!-- end  form for feedback -->

			  
            </main><!-- #main -->
        </div><!-- #primary -->

<?php endwhile; // End of the loop. ?>
<?php get_footer();
