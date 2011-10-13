<?php
/**
 Plugin Name: SimpleReach Analytics
 Plugin URI: https://www.simplereach.com
 Description: After installation, you must click '<a href='options-general.php?page=SimpleReach-Analytics'>Settings &rarr; SimpleReach Analytics</a>' to turn on the Analytics.
 Version: 0.0.1
 Author: SimpleReach
 Author URI: https://www.simplereach.com
 */

define('SRanalytics_PLUGIN_VERSION', '0.0.1');
define('SRanalytics_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SRanalytics_PLUGIN_SUPPORT_EMAIL', 'support@simplereach.com');

/**
 * Insert analytics code onto the post page
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param String $content the_content() output
 * @return String The content and The Slide code (if applicable)
 */
function sranalytics_insert_js($content)
{
    $sranalytics_userid = get_option('sranalytics_pid');
    if (empty($srtracerk_pid)) {
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

    $SRANALYTICS_PLUGIN_VERSION = SRANALYTICS_PLUGIN_VERSION;

// Get the JS ready to go
$rv = <<< SRANALYTICS_SCRIPT_TAG
<!-- SimpleReach Analytics Plugin Version: {$SRANALYTICS_PLUGIN_VERSION} -->
<script type='text/javascript' id='simplereach-analytics-tag'></script>
SRANALYTICS_SCRIPT_TAG;

    return $content . $rv;
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
 * Run the appropriate actions on hooks
 *
 * @author Malaney Hill <engineering@simplereach.com>
 * @param None
 * @return None
 */
function sranalytics_loaded()
{
    do_action('sranalytics_loaded');
}

add_filter('the_content', 'sranalytics_insert_js');
add_action('admin_menu','sranalytics_admin_actions');
?>
