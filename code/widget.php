<?php

class gdsw_Widget extends WP_Widget {
    var $folder_name = "";
    var $defaults = array();

    function gdsw_Widget() { }

    function widget($args, $instance) {
        global $gdsr, $userdata;
        extract($args, EXTR_SKIP);

        if ($this->folder_name == "gdsw-related-posts") {
            if ($instance["show_only_single"] == 1 && !is_single()) return;
        }

        $results = $this->results($instance);
        if (count($results) == 0 && $instance["hide_empty"] == 1) return;

        echo $before_widget;
        if ($instance["title"] != '') echo $before_title.$instance["title"].$after_title;
        echo $this->render($results, $instance);
        echo $after_widget;
    }

    function get_filter_name($name = "results") {
        $base = substr($this->folder_name, 5);
        return "gdsw_".$name."_".str_replace("-", "", $base);
    }

    function simple_render($instance = array()) {
        $instance = shortcode_atts($this->defaults, $instance);
        $results = $this->results($instance);
        return $this->render($results, $instance);
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/basic.php');
        if ($this->folder_name != "gdsw-simple-125-ads") include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/filter.php');
        else include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/ads.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/display.php');
    }

    function prepare($instance, $results) {
        if (count($results) == 0) return array();
        foreach ($results as $r) {
            if (isset($instance["display_post_date"]) && $instance["display_post_date"] == 1) $r->post_date = mysql2date($instance["display_post_date_format"], $r->post_date);
            if (isset($instance["display_excerpt"]) && $instance["display_excerpt"] == 1) $r->excerpt = $this->get_excerpt($instance, $r);
        }
        $results = apply_filters($this->get_filter_name(), $results, $instance);
        return $this->prepare_urls($instance, $results);
    }

    function prepare_urls($instance, $results) {
        foreach ($results as $r) {
            if (property_exists($r, "post_permalink") && $r->post_permalink == "") $r->post_permalink = get_permalink($r->ID);
            if (property_exists($r, "author_permalink") && $r->author_permalink == "") $r->author_permalink = get_author_posts_url($r->ID);
            if (property_exists($r, "comment_permalink") && $r->comment_permalink == "") $r->comment_permalink = get_comment_link($r->comment_ID);
        }
        return $results;
    }

    function prepare_sql($instance, $select, $from, $where, $group, $order, $limit) {
        $select = apply_filters($this->get_filter_name("sql_select"), $select, $instance);
        $from = apply_filters($this->get_filter_name("sql_from"), $from, $instance);
        $where = apply_filters($this->get_filter_name("sql_where"), $where, $instance);
        $group = apply_filters($this->get_filter_name("sql_group"), $group, $instance);
        $order = apply_filters($this->get_filter_name("sql_order"), $order, $instance);
        $limit = apply_filters($this->get_filter_name("sql_limit"), $limit, $instance);

        $sql = "SELECT ".$select." FROM ".$from;
        if ($where != "") $sql.= " WHERE ".$where;
        if ($group != "") $sql.= " GROUP BY ".$group;
        if ($order != "") $sql.= " ORDER BY ".$order;
        if ($limit != "") $sql.= " LIMIT ".$limit;

        return $sql;
    }

    function update($new_instance, $old_instance) { }

    function render($results, $instance) { }

    function results($instance) {
        return '';
    }

    function get_excerpt($instance, $r) {
        $text = trim($r->post_excerpt);

        if ($text == "") {
            $text = str_replace(']]>', ']]&gt;', $r->post_content);
            $text = strip_tags($text);
        }

        $text = gdFunctionsGDSW::trim_to_words($text, $instance["display_excerpt_length"]);
        return $text;
    }
}

?>