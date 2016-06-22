<?php
/**
* Template Name: GF Video Gallery
* Custom video gallery based on submissions to a Gravity Form
*/

get_header(); 

// configuration, terribly hard coded now

$gf_id = 1; 		// gravity form ID
$gf_name = 1; 		// field id for name
$gf_video = 2; 		// field id for video url 
$gf_location = 6; 	// field id for geo location
$gf_verified = 5;	// field id for verified
$gf_interest = 8;	// field id for interest area
$items_per_row = 3; // kind of obvious?
$rows_to_show = 2;	// this too

// Use Gravity form API to get all entry data for active items

// sort by the name field
$sorting = array( 'key' => $gf_name, 'direction' => 'ASC' );

// Hello API?
$gf_results = GFAPI::get_entries( $gf_id, array(  'status'  => 'active'  ), $sorting, array( 'offset' => 0, 'page_size' => 100) );

// walk the results to save only ones marked as active
// store in array with the stuff we need

foreach ( $gf_results as $item) {
	if ($item[$gf_verified] == "yes") $finds[] = array (
			'name' => $item[$gf_name],
			'url' => $item[$gf_video],
			'loc' => $item[$gf_location],
			'interest' => $item[$gf_interest]
		);
}

$random_finds = array_rand( $finds , $items_per_row * $rows_to_show  );

?>


<?php while ( have_posts() ) : the_post(); ?>

    <?php get_template_part( 'template-parts/wrapper', 'top' ); ?>

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

				<?php get_template_part( 'template-parts/cover', 'post' ); ?>

				 <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-content gallery-text">
						<?php the_content(); ?>

						<hr />
						<p>There are <strong><?php echo count($finds)?></strong>  videos available-- <strong><?php echo $items_per_row * $rows_to_show ?></strong> chosen at random are displayed and the full list follows.</p>
					</div><!-- .entry-content -->
				</article><!-- #post-## -->
<?php endwhile; // End of the loop. ?>

		<?php if (count ( $finds ) ) :?>
			
			<div id="posts" class="grid card-<?php echo $items_per_row?>">
			
				<?php foreach ( $random_finds as $item) {?>
				
					<article class="hentry">
						<header class="entry-header">
							<h1 class="entry-title"><?php echo $finds[$item]['name']?></h1>
							<h2 class="entry-subtitle"><?php  if  ( $finds[$item]['loc'] ) { echo $finds[$item]['loc']; } else {  echo '&nbsp';} ?><br />Certificaton Interest: CC-<?php echo strtoupper( $finds[$item]['interest'] )?> </h2>
						</header>
						 <div class="entry-summary">
							<?php echo wp_oembed_get( $finds[$item]['url'] );?>
						</div>
					</article>
				<?php }?>	
			</div>
			
			<div id="moreposts" class="grid card-1">
				<article class="hentry">
					<header class="entry-header">
					<h1 class="entry-title">All Videos A-Z</h1>

						<ol>
			
						<?php 
							foreach ( $finds as $item ) {
							
								// enable class if link is YouTube or vimeo, otherwise spawn new window
								$lightbox_class = ( is_embed_video( $item['url'] ) ) ? 'rel="wp-video-lightbox" ' : ' target="_blank"';
								
								echo '<li><a href="' . $item['url'] . '" ' . $lightbox_class . '>' . $item['name'] . '</a> &bull; ' . $item['loc'] . ' &bull; Certificaton Interest: CC-' . strtoupper( $item['interest'] ) . '</li>';
							}
						?>
						</ol>		
			
			
					</header>
				</article>
			</div>
			
					
		<?php else:?>
			<p>Sorry, no videos found.</p>
		<?php endif?>
		

            </main><!-- #main -->
        </div><!-- #primary -->

<?php get_footer();
