<?php
    $message = '';
    if (!empty($_POST['srtracker_submitted'])) {
        $srtracker_userid = (!empty($_POST['srtracker_userid'])) ? $_POST['srtracker_userid'] : '';
        if (!$srtracker_userid) {
            $message = _("ERROR:  You must enter a value for userid!", 'srtracker');
        } else {
            update_option('srtracker_userid', $srtracker_userid);
            $message = 'userid saved!'; 
        }
    }
    if ($message) {
        print '<div id="message" class="updated below-h2">'. $message . '</div>';
    }
?>
<div class="overview">
    <h2><?php _e('SimpleReach Tracker', 'srtracker'); ?></h2>

    <div id="srtracker_controls">
        <form name="srtracker_form" method="post" action="<?php print str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="srtracker_submitted" value="1" />
            <label for="srtracker_submitted"><?php _e('Enter your userid', 'srtracker'); ?></label>
            <input type="text" name="srtracker_userid" value="<?php print get_option('srtracker_userid') ;?>" />
            <input type="submit" name="Submit" value="<?php _e('Save', 'srtracker'); ?>" />
        </form>
    </div>
</div>
