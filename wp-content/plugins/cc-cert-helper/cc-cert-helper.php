<?php

/**
 * CC  Certification Helper
 *
 *
 * @link              http://cog.dog/
 * @since             1.0.0
 * @package           cc_cert_helper
 *
 * @wordpress-plugin
 * Plugin Name:       Creative Commons  Certification Helper
 * Plugin URI:        http://github.com/cogdog/cc-cert-helper
 * Description:       This plugin adds functionality such as navigation and post-processing for GitHub / markkdown source material for sites showing the Creative Commons Certification.
 * Version:           1.5.0
 * Author:            Alan Levine
 * Author URI:        http://cog.dog/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt

 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('cc_cert_helper_PLUGIN_VERSION', '1.5');


// -- default style sheet (should be eventually a plugin option)

function cc_cert_helper_add_stylesheet() {
	wp_enqueue_style( 'cc-cert-helper-style', plugins_url( '/css/cc-cert.css', __FILE__ ), false, cc_cert_helper_PLUGIN_VERSION, 'all' );
}

add_action('wp_enqueue_scripts', 'cc_cert_helper_add_stylesheet', 90);


function is_cert_page( $cert_page_type ) {
		
	// does it match allowable types?
	return ( in_array( $cert_page_type , array('cover', 'index', 'module', 'unit') ) );
}

if ( !function_exists('cc_cert_helper_get_top_parent') ) {
	function cc_cert_helper_get_top_parent ( $page_id ) {
		// the top parent in a chain of pages
		// ----- h/t http://wordpress.stackexchange.com/a/43374/14945
		$ancestors = get_post_ancestors( $page_id ); 

		//$ancestors is an array of post IDs starting with the current post going up the root
		//'Pop' the root ancestor out or returns the current ID if the post has no ancestors.
		$root_id = ( !empty ($ancestors ) ? array_pop( $ancestors ): $page_id);

		return ($root_id);
	}
}

if ( !function_exists('cc_cert_helper_get_navbox') ) {
	function cc_cert_helper_get_navbox ( $cert_title,  $type = 'module', $mod_title = '', $contents_id = 0) {  
	// construct sidebar navigation for certification index, modules, and units
	
		if ( $type == 'module') {
	
			$out = '<div id="rbox"><h3>' . $cert_title . ' Modules</h3>';

			// links are the other modules
			$out .= do_shortcode( '[pagelist child_of="parent" depth="1" class="modlist"]');
		
			$out .= '<h3>' . $mod_title . ' Module Units</h3>';
		
			// Add links to units in this module
			$out .=  do_shortcode( '[pagelist child_of="current" depth="1" class="unitlist"]') . '</div>';
		
		} elseif ( $type == 'unit') {

			$out = '<div id="rbox"><h3>' . $cert_title . ' Modules</h3>';

			// links are the other units
			$out .=  do_shortcode( '[pagelist child_of="' . $contents_id . '" depth="1" class="modlist"]');

			// now let's do the unit

			$out .= '<h3>' . $mod_title . ' Module Unit</h3>';
	
			 // links are the other units
			$out .= do_shortcode( '[siblings depth="1"  class="unitlist"]') . '</div>';
		}
	
	
		return $out;

	 }
 }
 
 
if ( !function_exists('cc_cert_helper_stripFirstLine') ) {
	function cc_cert_helper_stripFirstLine( $text ) {
		// removes first line of text from string
		// ----- h/t http://stackoverflow.com/a/7740485/2418186
		return substr( $text, strpos($text, "\n")+1 );
	}
}



if ( !function_exists('cc_cert_helper_get_cert_footer') ) {
	// get the common footer and special contents for the page, wrap in a div, and send back
	function cc_cert_helper_get_cert_footer ( $page_meta ) {
	
		$footer_str = '';
		
		// add page specific footer
		if ( $page_meta['footer'] ) {
			$footer_str = '<div id="cc_cert_page_footer">' . $page_meta['footer']  . '</div>' . "\n";
		}
		
		// add version info
		if ( $page_meta['vers'] and $page_meta['src']  ) {
			$footer_str .= '<div id="cc_cert_commonfooter">' . $page_meta['vers'] . '</div>' . "\n"; 
		}
	
		if ( empty( $footer_str ) ) {
			return '';
		} else {
			return '<div id="cc_cert_footer">' . $footer_str . "</div>\n";
		}
	}
}

/* ----- 

		filter the content on output to add shortcodes for navigation, and add any extra
		content to append after but not muck with the post content                                                    

----- */



if ( !function_exists('cc_cert_helper_content_filter') ) {

	add_filter( 'the_content', 'cc_cert_helper_content_filter', 20 );

	function cc_cert_helper_content_filter( $content ) {

		// only for Page main loops
		if ( is_page() && in_the_loop() && is_main_query() ) {
		
			global $post;
		
			// look for post meta
			$cert_meta = get_post_meta( $post->ID, '_cc_cert', true );


			// make sure we have a valid cert page type
			if ( is_cert_page( $cert_meta['page'] ) ) {
		
			
				// assemble the footer from the parts
				$the_footer = cc_cert_helper_get_cert_footer($cert_meta);
			
			
			
				switch ( $cert_meta['page'] )	{
		
					case 'cover':
						// add footer only to cover page
						$content = $content .  $the_footer;
			
						break;

					case 'index':
						// top level content pages
						$topnav = do_shortcode('[pagelist child_of="parent" depth="1" class="topnav"]');
			
						// all we need is nav and a pretty footer
						$content = $topnav . $content .  $the_footer;
		
						break;
				
					case 'module': 
						// page is a module in a certification
			
						$root_id = cc_cert_helper_get_top_parent($post->ID);
						$parent_id = wp_get_post_parent_id( $post->ID );
			
						// build the top menu
						$topnav = do_shortcode('[pagelist child_of = "' . $root_id . '" depth="1" class="topnav"]');

						// insert short codes for navigation to other modules and below it to the units in this module
						$content = $topnav . cc_cert_helper_get_navbox( get_the_title( $root_id ), 'module', get_the_title( $post->ID )  ) . $content . $the_footer;		
				
						break;	
				
					case 'unit': 
						// set up shortcode stuff to add navigation stuff to units, based on parent structure

						$parent_id = wp_get_post_parent_id( get_the_ID() ); // this should be the module for this unit

						$grandparent_id = wp_get_post_parent_id( $parent_id ); // this should be the content page id
			
						if ( get_post_ancestors( $grandparent_id ) ) {
							$topnav = do_shortcode('[pagelist child_of="' . wp_get_post_parent_id( $grandparent_id ) . '" depth="1" class="topnav"]') ;
			
						} else {
							$topnav = do_shortcode('[pagelist include="' . $grandparent_id . '" number="1" class="topnav"]');
						}	
			
						$content = $topnav .  cc_cert_helper_get_navbox( get_the_title( $grandparent_id ), 'unit', get_the_title( $parent_id ), $grandparent_id ) . $content .  $the_footer;	
				
						break;
				
					} // switch
			} // is_cert_page
		
		} // is_page()
	
		// Send back the content, pronto!
		return $content;
	} // function
}	

/* ------ meta box ------------------------------------------------------------------- */

function add_cc_cert_meta_box() {
	// add a meta box is we are on a certification page 
	global $post;
	
	// look for certificate metadata
	$cert_meta = get_post_meta( $post->ID, '_cc_cert', true );
	
	if ( empty( $cert_meta ) ) {
		//see if we are in the cert structure
		
		// find page top parent and check it's metadata for a custom field with the cert type
		$root_id = cc_cert_helper_get_top_parent($post->ID);
		$root_meta = get_post_meta( $root_id, 'iscert', true );
		
		if ( $root_meta ) {
			// enable the meta with basic data
			$cert_meta = array(
				'spec' 		=> $root_meta,
				'page'		=> '',
				'vers' 		=> '',
				'footer'	=> '',
				'src'		=> ''
			);
			
			// update the post meta
			update_post_meta( $post->ID, '_cc_cert' , $cert_meta );
		}
	}
	
	// good to go to create the metadata and enable metabox
	if ( !empty( $cert_meta ) ) {
    	add_meta_box("cc-cert-meta-box", "CC Certification Options", "cc_cert_meta_box_markup", "page", "normal", "high", null);
    }
    
}

add_action("add_meta_boxes", "add_cc_cert_meta_box");


function cc_cert_meta_box_markup( $post ) {

	// blessed is the nonce
	wp_nonce_field(basename(__FILE__), "cc-cert-meta-box-nonce");
	
	// get the meta from a serialized array of meta data
	$cert_meta = get_post_meta( $post->ID, '_cc_cert', true );
	
    ?>
        <div id="cc-cert-meta">
        
        
            <h4><?php _e( 'Certification', 'cc-cert' )?></h4>
			<div class="cc-cert-elements" style="margin-left:1em;">

        	<?php if ( current_user_can( 'switch_themes') ): ?>

  				<label for="cc-cert-meta-spec-core">
					<input type="radio" name="cc-cert-meta-spec" id="cc-cert-meta-spec-core" value="core" <?php if ( $cert_meta['spec'] == 'core' ) echo 'checked'?>> <?php _e( 'Core ', 'cc-cert' )?> &nbsp; 
				</label>

				<label for="cc-cert-meta-spec-edu">
					<input type="radio" name="cc-cert-meta-spec" id="cc-cert-meta-spec-edu" value="edu" <?php if ( $cert_meta['spec'] == 'edu' ) echo 'checked'?>> <?php _e( 'Education ', 'cc-cert' )?> &nbsp; 
				</label>

				<label for="cc-cert-meta-spec-lib">
					<input type="radio" name="cc-cert-meta-spec" id="cc-cert-meta-spec-lib" value="module" <?php if ( $cert_meta['spec'] == 'lib' ) echo 'checked'?>> <?php _e( 'Library ', 'cc-cert' )?> &nbsp; 
				</label>

				<label for="cc-cert-meta-spec-gov">
					<input type="radio" name="cc-cert-meta-spec" id="cc-cert-meta-spec-gov" value="unit" <?php if ( $cert_meta['spec'] == 'gov' ) echo 'checked'?>> <?php _e( 'Government', 'cc-cert' )?>
				</label>
        	
        	<?php else:?>
        		<?php echo $cert_meta['spec']; ?>
        	
        	<?php endif?>

			</div>
 
         	<h4><?php _e( 'Page Type', 'cc-cert' )?></h4>
			<div class="cc-cert-elements" style="margin-left:1em;">
			
			<?php if ( current_user_can( 'switch_themes') ): ?>

				<label for="cc-cert-meta-page-cover">
					<input type="radio" name="cc-cert-meta-page" id="cc-cert-meta-page-cover" value="cover" <?php if ( $cert_meta['page'] == 'cover' ) echo 'checked'?>> <?php _e( 'Cover ', 'cc-cert' )?> &nbsp; 
				</label>

				<label for="cc-cert-meta-page-index">
					<input type="radio" name="cc-cert-meta-page" id="cc-cert-meta-page-index" value="index" <?php if ( $cert_meta['page'] == 'index' ) echo 'checked'?>> <?php _e( 'Index ', 'cc-cert' )?> &nbsp; 
				</label>

				<label for="cc-cert-meta-page-module">
					<input type="radio" name="cc-cert-meta-page" id="cc-cert-meta-page-module" value="module" <?php if ( $cert_meta['page'] == 'module' ) echo 'checked'?>> <?php _e( 'Module ', 'cc-cert' )?> &nbsp; 
				</label>

				<label for="cc-cert-meta-page-unit">
					<input type="radio" name="cc-cert-meta-page" id="cc-cert-meta-page-unit" value="unit" <?php if ( $cert_meta['page'] == 'unit' ) echo 'checked'?>> <?php _e( 'Unit', 'cc-cert' )?>
				</label>

			
			<?php else:?>
			
				<?php echo $cert_meta['page']; ?>
				
			<?php endif?>
			</div>

			<h4><?php _e( 'Page Specific Footer', 'cc-cert' )?></h4>
			<p>Add attribution for featured image or any other end notes for this page.</p>
			<div class="cc-cert-elements" style="margin-left:1em;">
				<label for="cc-cert-meta-footer">
				<textarea id="cc-cert-meta-footer" name="cc-cert-meta-footer" rows="8" style="width:100%"><?php echo $cert_meta['footer']; ?></textarea>
				</label>
			</div>
			
			
			<h4><?php _e( 'GitHub Source URL', 'cc-cert' )?></h4>
			<div class="cc-cert-elements" style="margin-left:1em;">
			
			<?php if ( current_user_can( 'switch_themes') ): ?>

				<label for="cc-cert-meta-src">
				<input type="text" name="cc-cert-meta-src" id="cc-cert-meta-src" value="<?php echo $cert_meta['src']; ?>" style="width:100%" /><br />
				<input type="checkbox" name="cc-cert-gh-refresh" value="git" /> Refresh content on save? <strong>Note: This will delete any edits in content</strong> Make sure the JetPack <code>Markdown Module</code> is enabled first. Disable module after changes to revert to HTML.
				</label>
			
			<?php else:?>
				<?php echo $cert_meta['src']; ?>
			<?php endif?>
			</div>

			<h4><?php _e( 'Version', 'cc-cert' )?></h4>
			<div class="cc-cert-elements" style="margin-left:1em;">
			<?php echo $cert_meta['vers']; ?>
			</div>
		
        </div>
    <?php      
}


function cc_cert_meta_box_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    
    // got nonce?    
    $is_valid_nonce = ( isset( $_POST[ 'cc-cert-meta-box-nonce' ] ) && wp_verify_nonce( $_POST[ 'cc-cert-meta-box-nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits if faux save status 
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) return;
	
	// editors only
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;
	
	
	// get the existing meta from a serialized array of meta data
	$cert_meta = get_post_meta( $post_id , '_cc_cert', true );

	// Value for certificate specialty code
	if ( isset( $_POST[ 'cc-cert-meta-spec' ] ) ) {
		$cert_meta['spec'] = $_POST[ 'cc-cert-meta-spec' ];
	}
	// Value for certificate page type
	if ( isset( $_POST[ 'cc-cert-meta-page' ] ) ) {
		$cert_meta['page'] = $_POST[ 'cc-cert-meta-page' ];
	}
	
	// Footer string
	if ( isset( $_POST[ 'cc-cert-meta-footer' ] ) ) {
	
		// what's allowed in html for footer
		$allowables = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
		);
		
		// strip out HTML not allowed
		$footer_str = wp_kses( $_POST[ 'cc-cert-meta-footer' ], $allowables);
		
		// remove tabs, whitespace, newlines
		preg_replace("/\s+/", " ", $footer_str);
		
        $cert_meta['footer'] = $footer_str;
    }
    
    // GitHub URL 
	if ( isset( $_POST[ 'cc-cert-meta-src' ] ) ) {
        $cert_meta['src'] = sanitize_text_field( $_POST[ 'cc-cert-meta-src' ] );
    }

    
    // update  meta data
    update_post_meta( $post_id, '_cc_cert', $cert_meta );

   if ( isset($_POST[ 'cc-cert-gh-refresh' ]) and $_POST[ 'cc-cert-gh-refresh' ] == 'git' ) {
   		// process the changes in the page if an admin requested it
		cc_cert_converter_cleaner( $post_id );
	}

}

// only use  on cert pages
add_action( 'save_post_page', 'cc_cert_save_it' );

function cc_cert_save_it( $post_id ) {
		// update page meta data
		cc_cert_meta_box_save( $post_id );
}

function cc_cert_converter_cleaner( $post_id ) {
	
	$cert_meta = get_post_meta( $post_id, '_cc_cert', true ); 
	
	// do we have a URL to look for remote content?
	if ( $cert_meta['src'] ) {
		
		$url_parts =  explode('/', $cert_meta['src']);
		
		/* bust that url into parts split by "/" - it will look like 
		   https://github.com/<owner>/<repo>/blob/master/directory/file.md 
		   array (
			  0 => 'https:',
			  1 => '',
			  2 => 'github.com',
			  3 => owner,
			  4 => repo,
			  5 => 'blob',
			  6 => 'master',
			  7 => 'directory',
			  8 => 'file.md',
			)
		*/
		
		// get the owner
		$g_owner = $url_parts[3];

		// get the repo name
		$g_repo = $url_parts[4];
		
		// slide off the array from element 7
		$content_parts = array_splice( $url_parts, 7);
		
		$g_content_path = implode('/', $content_parts);
		
		
		// start the curl fetching
		// ---- h/t http://dobsondev.com/2015/04/03/api-calls-with-curl/
		$curl = curl_init();
				
		// authentication hardwired now, needs to be theme option. Removed for GitHub posting
		
		$cuser = '';
		$ctoken = '';
						
		// set up for curling for content
		curl_setopt_array( $curl, array(
		  CURLOPT_RETURNTRANSFER => 1,
		  CURLOPT_USERAGENT => $g_repo,
		  CURLOPT_URL => 'https://api.github.com/repos/' . $g_owner . '/' . $g_repo . '/contents/' . $g_content_path . '?client_id=' . $cuser . '&client_secret=' . $ctoken
		));
						
		 $response = curl_exec($curl);
		 
		 $content_response_array = json_decode($response);
		 
		// set up for curling for version
		curl_setopt_array($curl, array(
		  CURLOPT_RETURNTRANSFER => 1,
		  CURLOPT_USERAGENT => $g_repo,
		  CURLOPT_URL => 'https://api.github.com/repos/' . $g_owner . '/' . $g_repo . '/releases/latest' . '?client_id=' . $cuser . '&client_secret=' . $ctoken
		));
						
		 $response2 = curl_exec($curl);
		 
		 
		 $version_response_array = json_decode($response2);
		 

		 
		// use response if we got one and the return URL matches what we sought
		if ($response AND $content_response_array->html_url == $cert_meta['src']) {
			
			$content = base64_decode( $content_response_array->content );
			
			// make a version string if we got one
			if ( $response2 ) {
				// format the version string
				
				$cert_meta['vers'] = 'Creative Commons Certification: ' . $version_response_array->name . ' <a href="https://github.com/' . $g_owner . '/' . $g_repo . '">' . $version_response_array->tag_name . '</a> (' . date("M d, Y", strtotime( $version_response_array->published_at )) . ')';
				
				// update the post meta
				update_post_meta( $post_id, '_cc_cert' , $cert_meta );


				
			}
			
			
		} else {
			// failed curl use the exisiting content
			
			$the_post = get_post( $post_id,  OBJECT, 'edit');
			$content = $the_post->post_content . '<p style="text-align:center; font-size:0.8em; margin-top:200px; color:"#FFA9BE">GH error: '  . $content_response_array->message   . '</p>';
			
		} // $response content
		
	} else {
		// get us some content to update the page
		$the_post = get_post( $post_id,  OBJECT, 'edit');
		$content = $the_post->post_content;
	} // is g_repo
	
	// remove first line if it has a header tag
	if ( substr($content, 0, 1) == "#") $content = cc_cert_converter_stripFirstLine( $content );

	// replace local links in markdown tp work as sub page links in WP
	$content = str_replace ( '/index.md', '/', $content);
	$content = str_replace ( '.md', '/', $content);
		
	// URLs to GH hosted images, first URLs are the needle, second is replace
	// cant grep for my life 
	// When I grow up tp ne a real plugin, these will be user options.

	// all the repos
	$certs = ['core', 'lib', 'edu', 'gov'];

	for ($i = 0; $i < count ( $certs ); $i++ ) {
		// replace all URLs for the repo image with one for GH pages (so we can display)
		$content = str_replace ( 'https://github.com/creativecommons/cc-cert-' . $certs[$i] . '/blob/master/images', 'https://creativecommons.github.io/cc-cert-' . $certs[$i] . '/images' , $content);
	}

	// this aint working

	// $replace = preg_replace('\'https://github.com/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/blob/master/images\'',  'https://\\1.github.io/\\2/images', $content, -1, $rcount);
	

	// unhook this function so it doesn't loop infinitely
	remove_action( 'save_post_page', 'cc_cert_save_it' );

	// update the post, which calls save_post again
	wp_update_post( array( 'ID' => $post_id, 'post_content' => $content ) );

	// re-hook this function
	add_action( 'save_post_page', 'cc_cert_save_it' );
		
} // function

if ( !function_exists('cc_cert_converter_stripFirstLine') ) {
	function cc_cert_converter_stripFirstLine( $text ) {
		// removes first line of text from string
		// ----- h/t http://stackoverflow.com/a/7740485/2418186
		return substr( $text, strpos($text, "\n")+1 );
	}
}
