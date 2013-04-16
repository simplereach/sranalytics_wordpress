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

        $sranalytics_show_global_tag = (!empty($_POST['sranalytics_show_global_tag'])) ? $_POST['sranalytics_show_global_tag'] : '';
        if (update_option('sranalytics_show_global_tag', $sranalytics_show_global_tag)) {
        	$message = 'Settings updated';
	}

        $sranalytics_global_tag = (!empty($_POST['sranalytics_global_tag'])) ? $_POST['sranalytics_global_tag'] : '';
        if (update_option('sranalytics_global_tag', $sranalytics_global_tag)) {
            $message = 'Settings updated';
        }

    }
    if ($message) {
        print '<div id="message" class="updated below-h2">'. $message . '</div>';
    }

    $sranalytics_pid = get_option('sranalytics_pid');
    $sranalytics_show_global_tag = get_option('sranalytics_show_global_tag');
    $sranalytics_global_tag = get_option('sranalytics_global_tag');
?>
<form name="sranalytics_form" method="post" action="<?php print str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<div class='wrap'>   
    <h2><?php _e('SimpleReach Analytics', 'sranalytics'); ?></h2>
    <ul>
	<li>
	    <p>This tag will go on every page in main section (non-admin) of your site including the homepage.</p>
	    <input type="checkbox" id='sranalytics_show_global_tag' name="sranalytics_show_global_tag" value="true" <?php if ($sranalytics_show_global_tag) { print 'CHECKED=CHECKED'; } ?> />
	    <label for='sranalytics_show_global_tag'>Use Global Tag</label>
	</li>
	<li>
            <label for='sranalytics_global_tag'>Global Tag:</label>
            <input type="text" name="sranalytics_global_tag" value="<?php print $sranalytics_global_tag; ?>" style="width:200px;" />
	</li>

	<li><hr /></li>
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
</form>
