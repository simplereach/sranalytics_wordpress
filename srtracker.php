<?php
/**
 Plugin Name: The Tracker by SimpleReach
 Plugin URI: https://www.simplereach.com
 Description: 
    After installation, you must click 
    '<a href='options-general.php?page=SimpleReach-Tracker'>Settings &rarr; SimpleReach Tracker</a>' 
    to turn on The Tracker.
 Version: 0.0.1
 Author: SimpleReach
 Author URI: https://www.simplereach.com
 */

define('SRTRACKER_PLUGIN_VERSION', '0.0.1');
define('SRTRACKER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SRTRACKER_PLUGIN_SUPPORT_EMAIL', 'support@simplereach.com');

/**
 * Insert The Slide by SimpleReach onto the post page
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param String $content the_content() output
 * @return String The content and The Slide code (if applicable)
 */
function srtracker_insert_js($content)
{
    $srtracker_userid = get_option('srtracker_userid');
    if (empty($srtracerk_userid)) {
        return $content;
    }

    // Return the content on anything other than post pages
    if (!is_single() && !is_page()) {
        return $content;
    }

    GLOBAL $post;
    $post_id = $post_id->ID;

    // If the post isn't published yet, we don't need a slide
    if ($post->post_status != 'publish') {
        return $content;
    }

    $SRTRACKER_PLUGIN_VERSION = SRTRACKER_PLUGIN_VERSION;

// Get the JS ready to go
$rv = <<< SRTRACKER_SCRIPT_TAG
<!-- SimpleReach Tracker Plugin Version: {$SRTRACKER_PLUGIN_VERSION} -->
<script type='text/javascript' id='simplereach-tracker-tag'></script>
SRTRACKER_SCRIPT_TAG;

    return $content . $rv;
}

/**
 * Add the SimpleReach admin section
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function srtracker_load_admin()
{
    include_once('srtracker_admin.php');
}

/**
 * Add the SimpleReach admin options to the Settings Menu
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function srtracker_admin_actions()
{
    add_options_page("SimpleReach Tracker", "SimpleReach Tracker", 1, "SimpleReach-Tracker", "srtracker_load_admin");
}

/**
 * Setup the locales for i18n
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function srtracker_textdomain()
{
    $locale        = apply_filters( 'srtracker_locale', get_locale() );
    $mofile        = sprintf( 'srtracker-%s.mo', $locale );
    $mofile_local  = SRTRACKER_PLUGIN_DIR . '/lang/' . $mofile;
    
    if (file_exists($mofile_local)) {
        return load_textdomain( 'srtracker', $mofile_local );
    } else {
        return false;
    }
}

/**
 * Run the appropriate actions on hooks
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function srtracker_loaded()
{
    do_action('srtracker_loaded');
}

add_filter('the_content', 'srtracker_insert_js');
add_action('admin_menu','srtracker_admin_actions');
?>
