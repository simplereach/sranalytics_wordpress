<?php
    $message = '';
    if (!empty($_POST['sranalytics_submitted'])) {
        $sranalytics_pid = (!empty($_POST['sranalytics_pid'])) ? $_POST['sranalytics_pid'] : '';
        if (!$sranalytics_pid) {
            $message = _("ERROR:  You must enter a value for pid!", 'sranalytics');
        } else {
            update_option('sranalytics_pid', $sranalytics_pid);
            $message = 'You have updated your publisher ID.';
        }
    }
    if ($message) {
        print '<div id="message" class="updated below-h2">'. $message . '</div>';
    }

    $sranalytics_pid = get_option('sranalytics_pid');
?>
<div class="overview" style="margin-left:10px;">
    <h2><?php _e('SimpleReach Analytics', 'sranalytics'); ?></h2>

    <div id="sranalytics_controls" style="margin-left:5px;">
        <form name="sranalytics_form" method="post" action="<?php print str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="sranalytics_submitted" value="1" />
            <label for="sranalytics_submitted"><?php _e('Enter your Publisher ID (PID): ', 'sranalytics'); ?></label>
            <input type="text" name="sranalytics_pid" value="<?php print $sranalytics_pid; ?>" style="width:200px;" />
            <input type="submit" name="Submit" value="<?php _e('Save', 'sranalytics'); ?>" />
            <?php if (!empty($sranalytics_pid) && isset($sranalytics_pid)) { ?>
                <br />
                <span style="color:red;font-size:10px;">
                    * Do not change this unless you are absolutely sure you know what you are doing!
                </span>
            <?php } ?>
        </form>
    </div>
</div>
