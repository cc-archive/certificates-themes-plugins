<?php
/**
* Template Name: Certification Map
* For page structure version of the certification web version
* Use a gravity form for the feedback
*/

/* get the slug of a page's parent if it has one, otherwise return slug of page 
   h/t https://codex.wordpress.org/Function_Reference/get_post_ancestors#Get_Ancestors_Page_Slug
*/


global $post;

/* Get array of Ancestors and Parents if they exist */
$parents = get_post_ancestors( $post->ID );

/* Get the top Level page->ID count base 1, array base 0 so -1 */ 

if ($parents) {
	$id = $parents[0];
} else {
	$id =  $post->ID;
}
// store the title of the module or general certification page
$module_param = '&amp;module=' .  get_the_title();

// get us some post stuff
$parent = get_post( $id );

// set the certification
$certification = ucfirst($parent->post_name);




get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <?php get_template_part( 'template-parts/wrapper', 'top' ); ?>

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
            

			  <?php get_template_part( 'template-parts/cover', 'post' ); ?>
			  
			   <div style="text-align:center"><em>NOTE: These pages are not a layout design per se but an outline of all of types of information to be included</em></div>


              <?php get_template_part( 'template-parts/content', 'page' ); ?>

			  <div class="entry-content" id="certsuggest">
			  <h2>Feedback and Suggestions for <?php the_title( '"', '"' ); ?></h2>
			  
			  <?php echo do_shortcode('[gravityforms id="2" title="false" ajax="true" field_values="referer='  . get_permalink() . '&amp;certification=' . $certification . $module_param . '"]');?>
			  </div>

			  
            </main><!-- #main -->
        </div><!-- #primary -->

<?php endwhile; // End of the loop. ?>
<?php get_footer();
