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
        return $results;
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