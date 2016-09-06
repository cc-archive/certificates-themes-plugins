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

function is_embed_video ($url) {
	// tests if URl is for a youtube or vimeo site
	// ---- h/t https://gist.github.com/mjangda/1623788

	// valid domain list
	$video_domains = array( 'youtube.com', 'youtu.be', 'vimeo.com', 'www.youtube.com' );
	
	// parse domain
	$domain = parse_url( $url, PHP_URL_HOST );
	
	// Check if we match the domain exactly
	if ( in_array( $domain, $video_domains ) ) return true;

}

/* ----- shortcode to generate module navigation links for the draft certification pages -------- */
add_shortcode("modnav", "cc_cert_rbox");  

function cc_cert_rbox ( $atts ) {  

	// generates output for the objective sidebat
 	extract( shortcode_atts( array( "title" => 'Navigation',  "type" => 'module' ), $atts ) );  
 	
 	// for objective pages, we want to do the shortcode for siblings; if it is "module" then do subpages
 	$linklist = ( $type == 'module' ) ? do_shortcode( '[siblings]') : do_shortcode( '[subpages]');
 	
 	// go dog go
 	
 	if ( $type == 'module' ) {
 		// link title to parent
 		$parent_id = wp_get_post_parent_id( get_the_ID() );
 		
 		if ($parent_id) {
 			$title = '<a href="' . get_permalink( $parent_id) . '">' . $title . '</a>';
 		}
 	}
 	
 	return '<div id="rbox"><h3>' . $title . '</h3>' . $linklist . '</div>';

 }
 
/* ----- change content on output as needed                                      ----- */  

function stripFirstLine( $text ) {
	// removes first line of text from string
	// ----- h/t http://stackoverflow.com/a/7740485/2418186
	return substr( $text, strpos($text, "\n")+1 );
}


add_filter( 'the_content', 'cc_cert_content_filter', 20 );

function cc_cert_content_filter( $content ) {


	if ( is_page_template( 'page-cert.php' ) ) {
	
		// replace local links in markdown tp work as sub page links in WP
		$content = str_replace ( '.md', '/', $content);
	
		// replace all URLs for the repo image with one for GH pages (so we can link)
		$content = str_replace ( 'https://github.com/creativecommons/cc-cert-map/blob/master/img/' , 'https://creativecommons.github.io/cc-cert-map/img/' , $content);
		
		$content = do_shortcode('[siblings depth="1" class="topnav"]') . $content;
	
   } elseif ( is_page_template( 'page-map.php' ) ) {
        // remove first line that has # Header)

		$content = stripFirstLine( $content );
		
		// replace all URLs for the repo image with one for GH pages (so we can link)
		$content = str_replace ( 'https://github.com/creativecommons/cc-cert-map/blob/master/img/' , 'https://creativecommons.github.io/cc-cert-map/img/' , $content);
		
		// set up shortcode stuff to prefix content, based on parent ID
		$parent_id = wp_get_post_parent_id( get_the_ID() );
		
		// insert short codes
		$content = do_shortcode('[pagelist child_of="' . wp_get_post_parent_id($parent_id) . '" depth="1" class="topnav"]') . do_shortcode('[modnav title="' . get_the_title($parent_id)  . '" type="module"]') . $content;
		
	}
	
    // Send back the content, pronto!
    return $content;
}
 
/* ----- create cookie to remember user name and email for gravity form collecting resources ----- */ 
add_action("gform_pre_submission_2", "cookify_gf_form");
 
 
function cookify_gf_form($form_meta) {
	$saveVars = array("name", "email");
	
    foreach($form_meta["fields"] as $field) {
		if (in_array($field["inputName"], $saveVars)) {
			setcookie("gf_".$field["inputName"], $_POST["input_" . $field["id"]], time() + 31536000, COOKIEPATH, COOKIE_DOMAIN, false, true);
		}
	}
}

add_filter("gform_field_value_name", "populate_name");

function populate_name() {
	if (isset($_COOKIE["gf_name"]))  return $_COOKIE["gf_name"];
}

add_filter("gform_field_value_email", "populate_email");

function populate_email() {
	if (isset($_COOKIE["gf_email"])) return $_COOKIE["gf_email"];
}
 
?>
