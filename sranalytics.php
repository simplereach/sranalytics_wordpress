<?php
/**
 Plugin Name: SimpleReach Analytics
 Plugin URI: https://www.simplereach.com
 Description: After installation, you must click '<a href='options-general.php?page=SimpleReach-Analytics'>Settings &rarr; SimpleReach Analytics</a>' to turn on the Analytics.
 Version: 0.0.9
 Author: SimpleReach
 Author URI: https://www.simplereach.com
 */

define('SRANALYTICS_PLUGIN_VERSION', '0.0.9');
define('SRANALYTICS_PLUGIN_URL', PLugin_dir_url(__FILE__));
define('SRANALYTICS_PLUGIN_SUPPORT_EMAIL', 'support@simplereach.com');

/**
 * Insert analytics code onto the post page
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param String $content the_content() output
 * @return String The content and The Slide code (if applicable)
 */
function sranalytics_insert_js() {
  // Do not show SimpleReach tags by default
  $sranalytics_show_beacon = false;

  // Get the options
  $sranalytics_pid = get_option('sranalytics_pid');
  $sranalytics_show_on_tac_pages = get_option('sranalytics_show_on_tac_pages');
  $sranalytics_show_on_wp_pages = get_option('sranalytics_show_on_wp_pages');
  $sranalytics_show_on_attachment_pages = get_option('sranalytics_show_on_attachment_pages');
  $sranalytics_show_everywhere = get_option('sranalytics_show_everywhere');
  $sranalytics_force_http = get_option('sranalytics_force_http');
  $sranalytics_disable_iframe_loading = get_option('sranalytics_disable_iframe_loading');

  // Try and check the validity of the PID
  if (empty($sranalytics_pid) || strlen($sranalytics_pid) != 24) {
    return False;
  }

  // Show everywhere
  if ($sranalytics_show_everywhere) {
    $sranalytics_show_beacon = true;
  }
  //Show on attachment pages if option set 
  if (is_attachment() && $sranalytics_show_on_attachment_pages) {
    $sranalytics_show_beacon = true;
  }

  // Ensure we show on post pages
  if (is_single()) {
    $sranalytics_show_beacon = true;
  }

  // Ensure we show on WP pages if we are supposed to
  if (is_page() && $sranalytics_show_on_wp_pages) {
    $sranalytics_show_beacon = true;
  }

  // Ensure we show on WP pages if we are supposed to
  if (is_page() && $sranalytics_show_on_wp_pages) {
    $sranalytics_show_beacon = true;
  }
  global $post;

  $post_id = $post->ID;

  // If the post isn't published yet, don't show the __reach_config
  // attachments don't have published status though so always show for them.
  if ($post->post_status != 'publish' && !is_attachment()) {
    return False;
  }

  // Prep the variables
  $title = sranalytics_get_post_title($post);
  $authors = sranalytics_get_post_authors($post);
  $tags = sranalytics_get_post_tags($post);
  $channels = sranalytics_get_post_channels($post);
  $published_date = $post->post_date_gmt;
  $canonical_url = get_permalink($post->ID);



  // Show the tags if we are on a tag/author/category page and we are supposed to
  if ((is_category() || is_author() || is_tag()) && ($sranalytics_show_on_tac_pages || $sranalytics_show_everywhere)) {
    $sranalytics_show_beacon = true;
    $channels = "[]";
    $authors = "[]";
    $tags = "[]";

    //handle archive-style pages. WordPress has a different pattern for retrieving each one

    if (is_tag()) {
      $tag_name = single_cat_title( '', false );
      $tag = get_term_by('name', $tag_name, 'post_tag');
      $tag_url = get_tag_link($tag->term_id);
      $escaped_tag = addslashes($tag_name);

      $title = "Tag:" .  $escaped_tag;
      $tags = "['${escaped_tag}']";
      $canonical_url = $tag_url;

    } elseif (is_author()) {
      $author_id = get_the_author_meta('ID');
      $author_name = addslashes(get_the_author());

      $title = "Author: ${author_name}";
      $authors = "['${author_name}']";
      $canonical_url = get_author_posts_url($author_id); 

    } elseif (is_category()) {
      $channel_name = single_cat_title( '', false );
      $category_id = get_cat_ID($channel_page);
      $escaped_channel_name = addslashes($channel_name);

      $title = "Category: ${escaped_channel_name}";
      $channels = "['${escaped_channel_name}']";
      $canonical_url = get_category_link( $category_id );

    } else {
      // We should NEVER get here
      $title = "Unkown Page Type";
    }

    // If we are on a page, then we need to add it
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    if ($paged > 1) {
      $title = "${title} - Page ${paged}";
    }
  }

  // Handle the homepage properly if we are supposed to fire on it
  if ((is_home() || is_page('home')) && $sranalytics_show_everywhere) {
    $title = "Homepage";
    $channels = "[]";
    $authors = "[]";
    $tags = "[]";
    $canonical_url = get_home_url();
  }

  // Disable the iframe loading if the settings say so
  $iframe = $sranalytics_disable_iframe_loading;

  //force https to http if option is checked
  if($sranalytics_force_http){
    $pattern = '/^https:\/\//';
    $canonical_url = preg_replace( $pattern , "http://" , $canonical_url);
  }

  //sanitize all the things
  $canonical_url = addslashes($canonical_url);
  $title = addslashes($title);

  $javascript_array = array(
    'version' => SRANALYTICS_PLUGIN_VERSION,
    'pid' => $sranalytics_pid,
    'iframe' => $iframe,
    'title' => $title ,
    'url' => $canonical_url,
    'date' => $published_date,
    'channels' => $channels,
    'tags' => $tags,
    'authors' => $authors
  );


  // Get the JS ready to go
if ($sranalytics_show_beacon) {
  wp_register_script( 'sranalytics', plugins_url('javascripts/sranalytics.js', __FILE__) );
  wp_localize_script( 'sranalytics', 'sranalytics', $javascript_array );
  wp_enqueue_script( 'sranalytics' );
} else {
  return false;
}
}

/**
 * Return the title for the post
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Post object being shown on the page
 * @return String Title of the post with slashes escaped
 */
function sranalytics_get_post_title($post) {
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
function sranalytics_get_post_authors($post) {
    $author = addslashes(get_author_name($post->post_author));
    return array($author);
}



/**
 * Get the post categories and return them in stringified array form
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Wordpress Post
 * @return String $array A string representation of the array of categories
 */
function sranalytics_get_post_channels($post) {
    $post_categories = wp_get_post_categories($post->ID);
    $myCats = array();
    foreach ($post_categories as $c) {
        $cat = get_category($c);
        $myCats[] = addslashes($cat->slug);
    }

    return $myCats;
}


/**
 * Return the tags for the post
 *
 * @author Eric Lubow <elubow@simplereach.com>
 * @param Object $post Post object being shown on the page
 * @return String Tags of the post with slashes escaped
 */
function sranalytics_get_post_tags($post) {
    $wptags = wp_get_post_tags($post->ID);
    $myTags = array();

    foreach ($wptags as $tag) {
        $myTags[] = (is_object($tag)) ? addslashes($tag->name) : addslashes($tag);
    }

    return $myTags;
}

/**
 * Add the SimpleReach admin section
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function sranalytics_load_admin() {
  include_once('sranalytics_admin.php');
}

/**
 * Add the SimpleReach admin options to the Settings Menu
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function sranalytics_admin_actions() {
  add_options_page("SimpleReach Analytics", "SimpleReach Analytics", 1, "SimpleReach-Analytics", "sranalytics_load_admin");
}

/**
 * Setup the locales for i18n
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function sranalytics_textdomain() {
    $locale        = apply_filters( 'sranalytics_locale', get_locale() );
    $mofile        = sprintf( 'sranalytics-%s.mo', $locale );
    $mofile_local  = basename(dirname(__FILE__)) . '/lang/' . $mofile;

    if ( file_exists( $mofile_local ) ) {
        return load_textdomain( 'sranalytics', $mofile_local );
    } else {
        return false;
    }
}

// Determine when specific methods are supposed to fire
add_action('wp_head', 'sranalytics_insert_js', 1);
add_action('admin_menu','sranalytics_admin_actions');
add_action('plugins_loaded', 'sranalytics_textdomain');
?>
