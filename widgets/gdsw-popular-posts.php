<?php

class gdswPopularPosts extends WP_Widget {
    var $defaults = array(
        "title" => "Popular Posts",
        "count" => 10,
        "filter_recency" => "allp",
        "filter_type" => "postpage",
        "filter_category" => "",
        "filter_views" => "all",
        "display_css" => "",
        "display_views" => 1
    );

    function gdswPopularPosts() {
        $widget_ops = array('classname' => 'widget_gdsw_popularposts', 'description' => __("Display popular posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswpopularposts', 'gdSW Popular Posts', $widget_ops, $control_ops);
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
        $instance['filter_recency'] = $new_instance['filter_recency'];
        $instance['filter_category'] = strip_tags(stripslashes($new_instance['filter_category']));
        $instance['filter_type'] = $new_instance['filter_type'];
        $instance['filter_views'] = $new_instance['filter_views'];
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));
        $instance['display_views'] = isset($new_instance['display_views']) ? 1 : 0;

        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-popular-posts/basic.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-popular-posts/filter.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-popular-posts/display.php');
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $order = "";
        $select = array("p.ID", "p.post_title");
        $from = array(sprintf("%sposts p inner join %sgdpt_posts_views v on p.ID = v.post_id", $table_prefix, $table_prefix));
        $where = array("p.post_status = 'publish'");

        if ($instance["filter_type"] != "postpage") $where[] = sprintf("p.post_type = '%s'", $instance["filter_type"]);

        switch ($instance["filter_views"]) {
            case "all":
                $select[] = "v.usr_views + v.vst_views AS views";
                $order = "v.usr_views + v.vst_views DESC";
                break;
            case "users":
                $select[] = "v.usr_views AS views";
                $order = "v.usr_views DESC";
                break;
            case "visitors":
                $select[] = "v.vst_views AS views";
                $order = "v.vst_views DESC";
                break;
        }

        if ($instance["filter_category"] != "") {
            $from[] = sprintf("INNER JOIN %sterm_relationships tr ON tr.object_id = p.ID", $table_prefix);
            $from[] = sprintf("INNER JOIN %sterm_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id", $table_prefix);
            $where[] = sprintf("tt.term_id in (%s)", $instance["filter_category"]);
        }
        if ($instance["filter_recency"] != "allp") {
            $days = 0;
            switch ($instance["filter_recency"]) {
                case "lday":
                    $days = 1;
                    break;
                case "lwek":
                    $days = 7;
                    break;
                case "lmnt":
                    $days = 31;
                    break;
                case "lyea":
                    $days = 365;
                    break;
            }
            $where[] = sprintf("DATE_SUB(CURDATE(), INTERVAL %s DAY) < STR_TO_DATE(v.day,'%s')", $days, '%Y-%m-%d');
        }

        $sql = sprintf("SELECT DISTINCT %s FROM %s WHERE %s ORDER BY %s LIMIT %s",
            join(", ", $select), join(" ", $from), join(" AND ", $where), $order, $instance["count"]);
        wp_gdsw_log_sql("widget_gdws_popular_posts", $sql);
        return $wpdb->get_results($sql);
    }

    function render($results, $instance) {
        echo '<div class="gdsw-future-posts '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            echo '<li>';
            echo sprintf('<a href="%s" class="gdsw-url">%s</a>', get_permalink($r->ID), $r->post_title);
            if ($instance["display_views"] == 1) echo sprintf(" (%s %s)", $r->views, $r->views == 1 ? __("view", "gd-simple-widgets") : __("views", "gd-simple-widgets"));
            echo '</li>';
        }
        echo '</ul></div>';
    }
}

?>
