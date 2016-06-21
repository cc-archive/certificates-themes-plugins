<?php
/**
 * The template used for displaying gravity form videos
 *
 * @package Cover
 */

?>

<?php if ( have_posts() ) : ?>

	<div id="posts" class="grid card-2">
	
		<?php /* Start the Loop */ ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'summary' ); ?>

		<?php endwhile; ?>
	</div>

	<?php the_posts_navigation(); ?>

<?php else : ?>

	<?php get_template_part( 'template-parts/content', 'none' ); ?>

<?php endif;
