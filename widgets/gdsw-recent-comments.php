<?php

class gdswRecentComments extends WP_Widget {
    var $defaults = array(
        "title" => "Recent Comments",
        "count" => 10,
        "hide_empty" => 0,
        "filter_type" => "comm",
        "filter_category" => "",
        "display_css" => "",
        "display_gravatar" => 0,
        "display_gravatar_size" => 32
    );

    function gdswRecentComments() {
        $widget_ops = array('classname' => 'widget_gdsw_recentcomments', 'description' => __("Expanded widget with recent comments.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswrecentcomments', 'gdSW Recent Comments', $widget_ops, $control_ops);
    }

    function widget($args, $instance) {
        global $gdsr, $userdata;
        extract($args, EXTR_SKIP);

        $results = $this->results($instance);
        if (count($results) == 0 && $instance["hide_empty"] == 1) return;

        echo $before_widget.$before_title.$instance['title'].$after_title;
        echo $this->render($results, $instance);
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = intval(strip_tags(stripslashes($new_instance['count'])));
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        $instance['filter_category'] = trim(strip_tags(stripslashes($new_instance['filter_category'])));
        $instance['filter_type'] = $new_instance['filter_type'];
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));
        $instance['display_gravatar'] = isset($new_instance['display_gravatar']) ? 1 : 0;
        $instance['display_gravatar_size'] = intval(strip_tags(stripslashes($new_instance['display_gravatar_size'])));

        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-recent-comments/basic.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-recent-comments/filter.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-recent-comments/display.php');
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $select = array(
            "c.comment_ID", "c.comment_author", "c.comment_author_email", "c.comment_author_url",
            "p.ID", "p.post_title"
        );
        $from = array();
        $where = array();

        $from[] = sprintf("%scomments c INNER JOIN %sposts p ON c.comment_post_ID = p.ID", $table_prefix, $table_prefix);
        if ($instance["filter_type"] == "ping") $where[] = "comment_type = 'pingback'";
        if ($instance["filter_type"] == "comm") $where[] = "comment_type = ''";
        if ($instance["filter_category"] != "") {
            $from[] = sprintf("INNER JOIN %sterm_relationships tr ON tr.object_id = p.ID", $table_prefix);
            $from[] = sprintf("INNER JOIN %sterm_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id", $table_prefix);
            $where[] = sprintf("tt.term_id in (%s)", $instance["filter_category"]);
        }

        if (count($where) > 0) $where = " WHERE ".join(" AND ", $where);
        else $where = "";

        $sql = sprintf("SELECT DISTINCT %s FROM %s%s ORDER BY c.comment_date_gmt DESC LIMIT %s",
            join(", ", $select), join(" ", $from), $where, $instance["count"]);
        wp_gdsw_log_sql("widget_gdws_recent_comments", $sql);
        return $wpdb->get_results($sql);
    }

    function render($results, $instance) {
        echo '<div class="gdsw-recent-comments '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            echo '<li>';
            if ($instance["display_gravatar"] == 1) {
                echo '<table><tr><td>';
                echo sprintf('<a href="%s" rel="external nofollow" class="gdsw-url">%s</a>', $r->comment_author_url, get_avatar($r->comment_author_email, $instance["display_gravatar_size"]));
                echo '</td><td>';
            }
            if ($r->comment_author_url != "" && $r->comment_author_url != "http://") {
                echo sprintf('<a href="%s" rel="external nofollow" class="gdsw-url">%s</a>', $r->comment_author_url, $r->comment_author);
            } else echo $r->comment_author;
            echo sprintf(' on <a href="%s" class="gdsw-url">%s</a>', get_comment_link($r->comment_ID), $r->post_title);
            if ($instance["display_gravatar"] == 1) echo '</td></tr></table>';
            echo '</li>';
        }
        echo '</ul></div>';
    }
}

?>
