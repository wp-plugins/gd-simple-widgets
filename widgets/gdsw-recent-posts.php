<?php

class gdswRecentPosts extends WP_Widget {
    var $defaults = array(
        "title" => "Recent Posts",
        "count" => 10,
        "filter_category" => "",
        "display_css" => ""
    );

    function gdswRecentPosts() {
        $widget_ops = array('classname' => 'widget_gdsw_recentposts', 'description' => __("Expanded widget with recent posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswrecentposts', 'gdSW Recent Posts', $widget_ops, $control_ops);
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
        $instance['filter_category'] = strip_tags(stripslashes($new_instance['filter_category']));
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-recent-posts/basic.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-recent-posts/filter.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-recent-posts/display.php');
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $select = array(
            "p.ID", "p.post_title"
        );
        $from = array();
        $where = array();

        $from[] = sprintf("%sposts p", $table_prefix);
        $where[] = "p.post_status = 'publish'";
        $where[] = "p.post_type = 'post'";
        if ($instance["filter_category"] != "") {
            $from[] = sprintf("INNER JOIN %sterm_relationships tr ON tr.object_id = p.ID", $table_prefix);
            $from[] = sprintf("INNER JOIN %sterm_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id", $table_prefix);
            $where[] = sprintf("tt.term_id in (%s)", $instance["filter_category"]);
        }

        if (count($where) > 0) $where = " WHERE ".join(" AND ", $where);
        else $where = "";

        $sql = sprintf("SELECT DISTINCT %s FROM %s%s ORDER BY p.post_date_gmt DESC LIMIT %s",
            join(", ", $select), join(" ", $from), $where, $instance["count"]);
        wp_gdsw_log_sql("widget_gdws_recent_posts", $sql);
        return $wpdb->get_results($sql);
    }

    function render($results, $instance) {
        echo '<div class="gdsw-recent-posts '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            echo '<li>';
            echo sprintf('<a href="%s">%s</a>', get_permalink($r->ID), $r->post_title);
            echo '</li>';
        }
        echo '</ul></div>';
    }
}

?>
