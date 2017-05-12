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

function is_embed_video ( $url ) {
	// tests if URl is for a youtube or vimeo site to use in lightbox pluging
	// ---- h/t https://gist.github.com/mjangda/1623788

	// valid domain list
	$video_domains = array( 'youtube.com', 'youtu.be', 'vimeo.com', 'www.youtube.com' );
	
	// parse domain
	$domain = parse_url( $url, PHP_URL_HOST );
	
	// Check if we match the domain exactly
	if ( in_array( $domain, $video_domains ) ) return true;
}


function url_is_ok_video ( $url ) {
// Use for gravity form to verify video is from a URL we can autoembed in Wordpress
	$allowed_videos = array(
					'youtube.com',
					'youtu.be',
					'vimeo.com',
					'soundcloud.com',
					'kaltura.com'
	);

	// walk the array til we get a match
	foreach( $allowed_videos as $fragment ) {
  		if  (strpos( $url, $fragment ) !== false ) {
			return ( true );
		}
	}	
	
	// no matches, the URL is not for an allowable video
	return ( false );
}


/* -----  Tie our validation function to the 'gform_validation' hook              ----- */ 
add_filter( 'gform_validation_1', 'validate_whatif' );


/* -----  Form Validation... ACTIVATE                                             ----- */

function validate_whatif( $validation_result ) {

// h/t https://www.gravityhelp.com/documentation/article/using-the-gravity-forms-gform-validation-hook/

    // Get the form object from the validation result
    $form = $validation_result['form'];

    // Loop through the form fields
    foreach( $form['fields'] as &$field ) {
        
        // If the field does not have our designated CSS class, skip it
        if ( strpos( $field->cssClass, 'validate-it' ) === false ) {
            continue;
        }  
        
        // Get the submitted value from the $_POST
        $field_value = rgpost( "input_{$field['id']}" );  
        
        // check the URL for the field (we are looking for the video url field
        $is_valid = url_is_ok_video( $field_value );
        
        // If the urk is valid we don't need to do anything, skip it
        if ( $is_valid ) {
            continue;
        }
        
        // The field failed validation, so first we'll need to fail the validation for the entire form
        $validation_result['is_valid'] = false;
        
        // Next we'll mark the specific field that failed and add a custom validation message
        $field->failed_validation = true;
        $field->validation_message = 'The URL you entered is not from an accepted video service. Please try again';
    }
    
    // Assign our modified $form object back to the validation result
   	 $validation_result['form'] = $form;
    
    // Return the validation result
    return $validation_result;
    
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

/* ----- shortcode to time buddy widget -------- */
add_shortcode("timebuddy", "do_timebuddy");  

function do_timebuddy ( $atts ) {  

	// generate a Timebuddy widget because WP strips script tags
 	extract( shortcode_atts( array( "params" => ""), $atts ) );  
 	
 	if ( empty( $params ) ) {
 		return "Missing parameter! set <code>params=\"xxxxx\" from the Timebuddy generated embed code for <code>src=\"</code> e.g. <code>params=\"h=2643743&md=2/24/2017&mt=19.50&ml=1.00&sts=0&sln=0&wt=ew-ltc\"</code> after the \"?\"";
 	} else {
 	
 		return '<span class="wtb-ew-v1" style="width: 560px; display:inline-block"><script src="http://www.worldtimebuddy.com/event_widget.js?' . $params . '"></script><i><a target="_blank" href="http://www.worldtimebuddy.com/">Time converter</a> at worldtimebuddy.com</i><noscript><a href="http://www.worldtimebuddy.com/">Time converter</a> at worldtimebuddy.com</noscript><script>window[wtb_event_widgets.pop()].init()</script></span>';
	
	}

}
 
?>
