<?php
    $message = '';
    if (!empty($_POST['sranalytics_submitted'])) {
        $sranalytics_pid = (!empty($_POST['sranalytics_pid'])) ? $_POST['sranalytics_pid'] : '';
        if (!$sranalytics_pid) {
            $message = _("ERROR:  You must enter a value for pid!", 'sranalytics');
        } else {
            update_option('sranalytics_pid', $sranalytics_pid);
            $message = 'Settings updated';
        }

        $sranalytics_show_on_tac_pages = (!empty($_POST['sranalytics_show_on_tac_pages'])) ? $_POST['sranalytics_show_on_tac_pages'] : '';
        if (update_option('sranalytics_show_on_tac_pages', $sranalytics_show_on_tac_pages)) {
        	$message = 'Settings updated';
	}

        $sranalytics_show_on_wp_pages = (!empty($_POST['sranalytics_show_on_wp_pages'])) ? $_POST['sranalytics_show_on_wp_pages'] : '';
        if (update_option('sranalytics_show_on_wp_pages', $sranalytics_show_on_wp_pages)) {
        	$message = 'Settings updated';
	}

        $sranalytics_show_everywhere = (!empty($_POST['sranalytics_show_everywhere'])) ? $_POST['sranalytics_show_everywhere'] : '';
        if (update_option('sranalytics_show_everywhere', $sranalytics_show_everywhere)) {
        	$message = 'Settings updated';
	}

        $sranalytics_disable_iframe_loading = (!empty($_POST['sranalytics_disable_iframe_loading'])) ? $_POST['sranalytics_disable_iframe_loading'] : '';
        if (update_option('sranalytics_disable_iframe_loading', $sranalytics_disable_iframe_loading)) {
        	$message = 'Settings updated';
	}

    }
    if ($message) {
        print '<div id="message" class="updated below-h2">'. $message . '</div>';
    }

    // Set the variables
    $sranalytics_pid = get_option('sranalytics_pid');

    $sranalytics_show_on_tac_pages_string = get_option('sranalytics_show_on_tac_pages');
    $sranalytics_show_on_tac_pages = ($sranalytics_show_on_tac_pages_string === 'true');
    if (empty($sranalytics_show_on_tac_pages)) {
    	$sranalytics_show_on_tac_pages = false;
    }

    $sranalytics_show_everywhere_string = get_option('sranalytics_show_everywhere');
    $sranalytics_show_everywhere = ($sranalytics_show_everywhere_string === 'true');
    if (empty($sranalytics_show_everywhere)) {
    	$sranalytics_show_everywhere = false;
    }

    $sranalytics_show_on_wp_pages_string = get_option('sranalytics_show_on_wp_pages');
    $sranalytics_show_on_wp_pages = ($sranalytics_show_on_wp_pages_string === 'true');
    if (empty($sranalytics_show_on_wp_pages)) {
    	$sranalytics_show_on_wp_pages = false;
    }

    $sranalytics_disable_iframe_loading_string = get_option('sranalytics_disable_iframe_loading');
    $sranalytics_disable_iframe_loading = ($sranalytics_disable_iframe_loading_string === 'true');
    if (empty($sranalytics_show_on_wp_pages)) {
    	$sranalytics_disable_iframe_loading = false;
    }
?>

<div class='overview'>
  <h2><?php _e('SimpleReach Analytics', 'sranalytics_admin_title'); ?></h2>
</div>

<form name="sranalytics_form" method="post" action="<?php print str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<div id='poststuff' class='wrap'>   
<div id='post-body' class='metabox-holder colums-2'>
<div id='post-body-content'>

  <div class='postbox'>
    <h3 class='hndle'><span><?php _e('Publisher ID', 'sranalytics_admin_settings_publisher_id'); ?></span></h3>
    <div class='inside'>
      <ul>
          <li>
              <div id="sranalytics_controls">
                  <input type="hidden" name="sranalytics_submitted" value="1" />
                  <label for="sranalytics_submitted"><?php _e('Enter your Publisher ID (PID): ', 'sranalytics'); ?></label>
                  <input type="text" name="sranalytics_pid" value="<?php print $sranalytics_pid; ?>" style="width:200px;" />
                  <?php if (!empty($sranalytics_pid) && isset($sranalytics_pid)) { ?>
                      <br />
                      <span style="color:red;font-size:10px;">
                          * Do not change this unless you are absolutely sure you know what you are doing!
                      </span>
                  <?php } ?>
              </div>
          </li>
          <li><input class='button-primary' type="submit" name="Submit" value="<?php _e('Save', 'sranalytics'); ?>" /></li>
      </ul>
    </div>
  </div>

  <div class='postbox'>
    <h3 class='hndle'><span><?php _e('iFrame Settings', 'sranalytics_admin_settings_box_title'); ?></span></h3>
    <div class='inside'>
      <ul>
          <li>
              <input type="checkbox" id='sranalytics_disable_iframe_loading' name="sranalytics_disable_iframe_loading" value="true" <?php if ($sranalytics_disable_iframe_loading) { print 'CHECKED=CHECKED'; } ?> />
              <label for='sranalytics_disable_iframe_loading'>Disable iFrame loading of the SimpleReach code (<span style='color:red;font-size:10px;'><strong>WARNING</strong>: disabling will make your analytics less accurate</span>)</label>
          </li>
          <li><input class='button-primary' type="submit" name="Submit" value="<?php _e('Save', 'sranalytics'); ?>" /></li>
      </ul>
    </div>
  </div>

  <div class='postbox'>
    <h3 class='hndle'><span><?php _e('Advanced Tracking Settings', 'sranalytics_admin_settings_box_title'); ?></span></h3>
    <div class='inside'>
      <p>WordPress posts are tracked by default. If you'd like to track additional parts of your site, please use the settings below.</p>
      <ul>
          <li>
              <input type="checkbox" id='sranalytics_show_on_wp_pages' name="sranalytics_show_on_wp_pages" value="true" <?php if ($sranalytics_show_on_wp_pages) { print 'CHECKED=CHECKED'; } ?> />
              <label for='sranalytics_show_on_wp_pages'>Track WordPress pages</label>
          </li>

          <li>
              <input type="checkbox" id='sranalytics_show_on_tac_pages' name="sranalytics_show_on_tac_pages" value="true" <?php if ($sranalytics_show_on_tac_pages) { print 'CHECKED=CHECKED'; } ?> />
              <label for='sranalytics_show_on_tac_pages'>Track author, category, and tag pages</label>
          </li>

          <li>
              <input type="checkbox" id='sranalytics_show_everywhere' name="sranalytics_show_everywhere" value="true" <?php if ($sranalytics_show_everywhere) { print 'CHECKED=CHECKED'; } ?> />
              <label for='sranalytics_show_everywhere'>Track everything, including the home page (includes WordPress, author, category, tag, and search results pages)</label>
          </li>
          <li><input class='button-primary' type="submit" name="Submit" value="<?php _e('Save', 'sranalytics'); ?>" /></li>
      </ul>
    </div>
  </div>
</div>

<div id='postbox-container-1' class='postbox-container'>
<div class='meta-box-sortables ui-sortable'>
  <div class='postbox'>
    <h3 class='hndle'>Support</h3>
    <div class='inside'>
      <p>
	Questions? Comments? We can be contacted via <a href='mailto:support@simplereach.com'>SimpleReach Support</a>.
      </p>
    </div>
  </div>
</div>
</div>

</div>
</div>
</form>
