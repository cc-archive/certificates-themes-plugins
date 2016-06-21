<?php

add_filter( 'body_class', 'gf_gallery_body_classes' );

function gf_gallery_body_classes( $classes ) {
	// slugs for possible gallery pages, needed to add extra body class for these pages
	if ( is_page( array( 'gallery', 'what-if' ) ) ) {
		$classes[] = 'gallery_page';
	}

	return $classes;
}



// Enable additional oEmbed for other video sites
add_action( 'init', 'cc_cert_add_oembed_handlers' );

function cc_cert_add_oembed_handlers() {
	// kaltura for warwick
	wp_oembed_add_provider( 'https://warwick.mediaspace.kaltura.com/id/*', 'https://warwick.mediaspace.kaltura.com/oembed' );
}
?>
