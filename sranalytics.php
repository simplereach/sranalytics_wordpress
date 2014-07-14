<?php
/**
 Plugin Name: SimpleReach Analytics
 Plugin URI: https://www.simplereach.com
 Description: After installation, you must click '<a href='options-general.php?page=SimpleReach-Analytics'>Settings &rarr; SimpleReach Analytics</a>' to turn on the Analytics.
 Version: 0.0.6
 Author: SimpleReach
 Author URI: https://www.simplereach.com
 */

define('SRANALYTICS_PLUGIN_VERSION', '0.0.7');
define('SRANALYTICS_PLUGIN_URL', PLugin_dir_url(__FILE__));
define('SRANALYTICS_PLUGIN_SUPPORT_EMAIL', 'support@simplereach.com');

/**
 * Insert analytics code onto the post page
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param String $content the_content() output
 * @return String The content and The Slide code (if applicable)
 */
function sranalytics_insert_js()
{
    // Do not show SimpleReach tags by default
    $sranalytics_show_beacon = false;

    // Get the options
    $sranalytics_pid = get_option('sranalytics_pid');

    $sranalytics_show_on_tac_pages_string = get_option('sranalytics_show_on_tac_pages');
    $sranalytics_show_on_tac_pages = ($sranalytics_show_on_tac_pages_string === 'true');

    $sranalytics_show_on_attachments_string = get_option('sranalytics_show_on_attachments');
    $sranalytics_show_on_attachments = ($sranalytics_show_on_attachments_string === 'true');

    $sranalytics_show_on_wp_pages_string = get_option('sranalytics_show_on_wp_pages');
    $sranalytics_show_on_wp_pages = ($sranalytics_show_on_wp_pages_string === 'true');

    $sranalytics_show_everywhere_string = get_option('sranalytics_show_everywhere');
    $sranalytics_show_everywhere = ($sranalytics_show_everywhere_string === 'true');

    $sranalytics_force_http_string = get_option('sranalytics_force_http');
    $sranalytics_force_http = ($sranalytics_force_http_string === 'true');

    $sranalytics_disable_iframe_loading_string = get_option('sranalytics_disable_iframe_loading');
    $sranalytics_disable_iframe_loading = ($sranalytics_disable_iframe_loading_string === 'true');

    // Try and check the validity of the PID
    if (empty($sranalytics_pid) or strlen($sranalytics_pid) != 24) {
        return False;
    }

    // Show everywhere 
    if ($sranalytics_show_everywhere) {
    	$sranalytics_show_beacon = true;
    } else {
    	// Skip attachment pages
        if (is_attachment()) { 
		if ($sranalytics_show_on_attachments) {
		    $sranalytics_show_beacon = true;
		} else {
		    return False;
		}
	}

    	// Ensure we show on post pages
        if (is_single()) {
            $sranalytics_show_beacon = true;
        }

    	// Ensure we show on WP pages if we are supposed to
    	if (is_page() and $sranalytics_show_on_wp_pages) {
    	    $sranalytics_show_beacon = true;
    	}
    }

    global $post;
    $post_id = $post->ID;

    // If the post isn't published yet, don't show the __reach_config
    // get_post_status will get parent's status if it's an attachment page
    if (get_post_status($post->ID) != 'publish') {
        return False;
    }

    $SRANALYTICS_PLUGIN_VERSION = SRANALYTICS_PLUGIN_VERSION;

    // Prep the variables
    $title = sranalytics_get_post_title($post);
    $authors = sranalytics_get_post_authors($post);
    $tags = sranalytics_get_post_tags($post);
    $channels = sranalytics_get_post_channels($post);
    $published_date = $post->post_date_gmt;
    $canonical_url = addslashes(get_permalink($post->ID));

    //force https to http if option is checked
    if($sranalytics_force_http){
        $pattern = '/^https:\/\//';
        $canonical_url = preg_replace( $pattern , "http://" , $canonical_url);
    }

    // Show the tags if we are on a tag/author/category page and we are supposed to
    if ((is_category() or is_author() or is_tag()) and ($sranalytics_show_on_tac_pages or $sranalytics_show_everywhere)) {
	$sranalytics_show_beacon = true;
	$channels = "[]";
	$authors = "[]";
	$tags = "[]";

	// Set the title
	if (is_tag()) {
		$tag_page = get_query_var('tag');
		$title = "Tag: ${tag_page}";
		$tags = "['${tag_page}']";
        } elseif (is_author()) {
		$author_page = get_query_var('author');
		$title = "Author: ${author_page}";
		$authors = "['${author_page}']";
	} elseif (is_category()) {
       		$channel_page = get_query_var('category');
		$title = "Category: ${channel_page}";
		$channels = "['${channel_page}']";
	} else {
		// We should NEVER get here
		$title = "Unkown Page Type";
        }

	// If we are on a page, then we need to add it
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	if ($paged > 1) {
		$title = "${title} - Page ${paged}";
	}

	// TODO Set the published_date
    }

// Handle the homepage properly if we are supposed to fire on it
if ((is_home() or is_page('home')) and ($sranalytics_show_on_tac_pages or $sranalytics_show_everywhere)) {
	$title = "Homepage";
	$channels = "[]";
	$authors = "[]";
	$tags = "[]";
}

// Disable the iframe loading if the settings say so
if ($sranalytics_disable_iframe_loading == 'true') {
      $iframe = "iframe: false";
} else {
      $iframe = "iframe: true";
}

// TODO If we are using the global tag, it needs to somehow be inserted here

// Get the JS ready to go
$rv = <<< SRANALYTICS_SCRIPT_TAG
<!-- SimpleReach Analytics Plugin Version: {$SRANALYTICS_PLUGIN_VERSION} -->
<script type='text/javascript' id='simplereach-analytics-tag'>
    __reach_config = {
      pid: '{$sranalytics_pid}',
      {$iframe},
      title: '{$title}',
      url: '{$canonical_url}',
      date: '{$published_date}',
      authors: {$authors},
      channels: {$channels},
      tags: {$tags}
    };
    (function(){
      var s = document.createElement('script');
      s.async = true;
      s.type = 'text/javascript';
      s.src = document.location.protocol + '//simple-cdn.s3.amazonaws.com/js/reach.js';
      (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(s);
    })();
</script>
SRANALYTICS_SCRIPT_TAG;

    if ($sranalytics_show_beacon) {
    	echo $rv;
    } else {
	return False;
    }
}

/**
 * Return the title for the post
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Post object being shown on the page
 * @return String Title of the post with slashes escaped
 */
function sranalytics_get_post_title($post)
{
    $title = $post->post_title;
    return addslashes($title);
}


/**
 * Get the post authors and return them in stringified array form
 * NOTE: Wordpress can currently only have on author per post.
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Wordpress Post
 * @return String $array A string representation of the array of authors
 */
function sranalytics_get_post_authors($post)
{
    $author = "'".addslashes(get_author_name($post->post_author))."'";
    return "[{$author}]";
}



/**
 * Get the post categories and return them in stringified array form
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Wordpress Post
 * @return String $array A string representation of the array of categories
 */
function sranalytics_get_post_channels($post)
{
    $post_categories = wp_get_post_categories($post->ID);
    $myCats = array();
    foreach ($post_categories as $c) {
        $cat = get_category($c);
        $myCats[] = "'".addslashes($cat->slug)."'";
    }
    $cats = join(',', $myCats);

    return "[{$cats}]";
}


/**
 * Return the tags for the post
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Post object being shown on the page
 * @return String Tags of the post with slashes escaped
 */
function sranalytics_get_post_tags($post)
{
    $wptags = wp_get_post_tags($post->ID);
    $myTags = array();

    foreach ($wptags as $tag) {
        $myTags[] = (is_object($tag)) ? "'".addslashes($tag->name)."'" : "'".addslashes($tag)."'";
    }
	$tags = join(',', $myTags);
	
    return "[{$tags}]";
}



/**
 * Add the SimpleReach admin section
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function sranalytics_load_admin()
{
    include_once('sranalytics_admin.php');
}

/**
 * Add the SimpleReach admin options to the Settings Menu
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function sranalytics_admin_actions()
{
    add_options_page("SimpleReach Analytics", "SimpleReach Analytics", 1, "SimpleReach-Analytics", "sranalytics_load_admin");
}

/**
 * Setup the locales for i18n
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function sranalytics_textdomain()
{
    $locale        = apply_filters( 'sranalytics_locale', get_locale() );
    $mofile        = sprintf( 'sranalytics-%s.mo', $locale );
    $mofile_local  = SRanalytics_PLUGIN_DIR . '/lang/' . $mofile;

    if (file_exists($mofile_local)) {
        return load_textdomain( 'sranalytics', $mofile_local );
    } else {
        return false;
    }
}

/**
 * Get the current page URL
 *
 * @author Eric Lubow <engineering@simplereach.com>
 * @param None
 * @return None
 */
function current_page_url() {
	$pageURL = 'http';
	if( isset($_SERVER["HTTPS"]) ) {
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

// Determine when specific methods are supposed to fire
add_action('wp_head', 'sranalytics_insert_js', 1);
add_action('admin_menu','sranalytics_admin_actions');
?>
