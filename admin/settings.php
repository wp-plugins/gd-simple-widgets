<form method="post">
<div id="gdpt_server" class="postbox gdrgrid frontright">
    <h3 class="hndle"><span><?php _e("Available Widgets", "gd-simple-widgets"); ?></span></h3>
    <div class="inside">
        <p class="sub"><?php _e("Unchecked widgets will not be available on the Widgets panel.", "gd-simple-widgets"); ?></p>
        <div class="table">
            <table><tbody>
                <tr class="first">
                    <td class="first b">gdSW Recent Comments</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_recent_comments"] == 1) echo " checked"; ?> type="checkbox" name="widgets_recent_comments" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Recent Posts</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_recent_posts"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_recent_posts" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Most Commented</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_most_commented"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_most_commented" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Posts Authors</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_posts_authors"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_posts_authors" /></td>
                </tr>
                <tr>
                    <td class="first b">gdSW Future Posts</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["widgets_future_posts"] == 1) echo " checked"; ?>  type="checkbox" name="widgets_future_posts" /></td>
                </tr>
            </tbody></table>
        </div>
        <div style="text-align: right">
        <input style="width: 80px; cursor: pointer;" type="submit" name="widgets_saving" value="<?php _e("Save", "gd-simple-widgets"); ?>">
        </div>
    </div>
</div>
<div id="gdpt_server" class="postbox gdrgrid frontright">
    <h3 class="hndle"><span><?php _e("Default WordPress Widgets", "gd-simple-widgets"); ?></span></h3>
    <div class="inside">
        <p class="sub"><?php _e("Unchecked widgets will not be available on the Widgets panel.", "gd-simple-widgets"); ?></p>
        <div class="table">
            <table><tbody>
                <tr class="first">
                    <td class="first b">Recent Comments</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["default_recent_comments"] == 1) echo " checked"; ?> type="checkbox" name="default_recent_comments" /></td>
                </tr>
                <tr>
                    <td class="first b">Recent Posts</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["default_recent_posts"] == 1) echo " checked"; ?>  type="checkbox" name="default_recent_posts" /></td>
                </tr>
            </tbody></table>
        </div>
        <div style="text-align: right">
        <input style="width: 80px; cursor: pointer;" type="submit" name="widgets_saving" value="<?php _e("Save", "gd-simple-widgets"); ?>">
        </div>
    </div>
</div>
<div id="gdpt_server" class="postbox gdrgrid frontright">
    <h3 class="hndle"><span><?php _e("Other Settings", "gd-simple-widgets"); ?></span></h3>
    <div class="inside">
        <div class="table">
            <table><tbody>
                <tr class="first">
                    <td class="first b">Load default CSS file</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["load_default_css"] == 1) echo " checked"; ?> type="checkbox" name="load_default_css" /></td>
                </tr>
                <tr>
                    <td class="first b">Write debug info into file</td>
                    <td class="t"></td>
                    <td class="t options"><input<?php if ($options["debug_into_file"] == 1) echo " checked"; ?> type="checkbox" name="debug_into_file" /></td>
                </tr>
            </tbody></table>
        </div>
        <div style="text-align: right">
        <input style="width: 80px; cursor: pointer;" type="submit" name="widgets_saving" value="<?php _e("Save", "gd-simple-widgets"); ?>">
        </div>
    </div>
</div>
</form>