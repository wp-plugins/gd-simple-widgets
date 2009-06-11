<?php

class gdswRecentPosts extends gdsw_Widget {
    var $folder_name = "gdsw-recent-posts";
    var $defaults = array(
        "title" => "Recent Posts",
        "count" => 10,
        "hide_empty" => 0,
        "filter_category" => "",
        "display_css" => "",
        "display_excerpt" => 0,
        "display_excerpt_length" => 15,
        "display_post_date" => 0,
        "display_post_date_format" => "F j, Y"
    );

    function gdswRecentPosts() {
        $widget_ops = array('classname' => 'widget_gdsw_recentposts', 'description' => __("Expanded widget with recent posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswrecentposts', 'gdSW Recent Posts', $widget_ops, $control_ops);
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = intval(strip_tags(stripslashes($new_instance['count'])));
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));

        $instance['filter_category'] = strip_tags(stripslashes($new_instance['filter_category']));
        $instance['display_excerpt'] = isset($new_instance['display_excerpt']) ? 1 : 0;
        $instance['display_excerpt_length'] = intval(strip_tags(stripslashes($new_instance['display_excerpt_length'])));
        $instance['display_post_date'] = isset($new_instance['display_post_date']) ? 1 : 0;
        $instance['display_post_date_format'] = trim(strip_tags(stripslashes($new_instance['display_post_date_format'])));

        return $instance;
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $select = array("p.ID", "p.post_title");
        $from = array();
        $where = array();
        if ($instance["display_post_date"] == 1) $select[] = "p.post_date";
        if ($instance["display_excerpt"] == 1) {
            $select[] = "p.post_content";
            $select[] = "p.post_excerpt";
            $select[] = "'' as excerpt";
        }
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
        return $this->prepare($instance, $wpdb->get_results($sql));
    }

    function render($results, $instance) {
        echo '<div class="gdsw-widget gdsw-recent-posts '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            echo '<li>';
            echo sprintf('<a href="%s" class="gdsw-url">%s</a>', get_permalink($r->ID), $r->post_title);
            if ($instance["display_post_date"] == 1) echo sprintf(' <span class="gdws-date">[%s]</span>', $r->post_date);
            if ($instance["display_excerpt"] == 1) echo sprintf('<p class="gdws-excerpt">%s</p>', $r->excerpt);
            echo '</li>';
        }
        echo '</ul></div>';
    }
}

?>
