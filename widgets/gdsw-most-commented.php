<?php

class gdswMostCommented extends WP_Widget {
    var $defaults = array(
        "title" => "Most Commented",
        "count" => 10,
        "filter_published" => "allp",
        "filter_category" => "",
        "display_css" => "",
        "display_comments_count" => 1
    );

    function gdswMostCommented() {
        $widget_ops = array('classname' => 'widget_gdsw_mostcommented', 'description' => __("Most commented posts.", "gd-simple-widgets"));
        $control_ops = array('width' => 400);
        $this->WP_Widget('gdswmostcommented', 'gdSW Most Commented', $widget_ops, $control_ops);
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
        $instance['filter_published'] = $new_instance['filter_published'];
        $instance['filter_category'] = strip_tags(stripslashes($new_instance['filter_category']));
        $instance['display_css'] = trim(strip_tags(stripslashes($new_instance['display_css'])));
        $instance['display_comments_count'] = isset($new_instance['display_comments_count']) ? 1 : 0;

        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-most-commented/basic.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-most-commented/filter.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-most-commented/display.php');
    }

    function results($instance) {
        global $wpdb, $table_prefix;

        $select = array(
            "p.ID", "p.post_title", "p.comment_count"
        );
        $from = array(sprintf("%sposts p", $table_prefix));
        $where = array("p.post_status = 'publish'", "p.comment_count > 0");

        if ($instance["filter_category"] != "") {
            $from[] = sprintf("INNER JOIN %sterm_relationships tr ON tr.object_id = p.ID", $table_prefix);
            $from[] = sprintf("INNER JOIN %sterm_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id", $table_prefix);
            $where[] = sprintf("tt.term_id in (%s)", $instance["filter_category"]);
        }
        if ($instance["filter_published"] != "allp") {
            $days = 0;
            switch ($instance["filter_published"]) {
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
            $where[] = sprintf("DATE_SUB(CURDATE(), INTERVAL %s DAY) < p.post_date", $days);
        }

        $sql = sprintf("SELECT DISTINCT %s FROM %s WHERE %s ORDER BY p.comment_count DESC LIMIT %s",
            join(", ", $select), join(" ", $from), join(" AND ", $where), $instance["count"]);
        wp_gdsw_log_sql("widget_gdws_popular_posts", $sql);
        return $wpdb->get_results($sql);
    }

    function render($results, $instance) {
        echo '<div class="gdsw-popular-posts '.$instance["display_css"].'"><ul>';
        foreach ($results as $r) {
            echo '<li>';
            echo sprintf('<a href="%s" class="gdsw-url">%s</a>', get_comment_link($r->ID), $r->post_title);
            if ($instance["display_comments_count"] == 1) echo sprintf(" [%s]", $r->comment_count);
            echo '</li>';
        }
        echo '</ul></div>';
    }
}

?>
