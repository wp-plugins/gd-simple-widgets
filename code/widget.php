<?php

class gdsw_Widget extends WP_Widget {
    var $folder_name = "";
    var $defaults = array();

    function gdsw_Widget() { }

    function widget($args, $instance) {
        global $gdsr, $userdata;
        extract($args, EXTR_SKIP);

        $results = $this->results($instance);
        if (count($results) == 0 && $instance["hide_empty"] == 1) return;

        echo $before_widget.$before_title.$instance["title"].$after_title;
        echo $this->render($results, $instance);
        echo $after_widget;
    }

    function form($instance) {
        $instance = wp_parse_args((array)$instance, $this->defaults);

        include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/basic.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/filter.php');
        include(GDSIMPLEWIDGETS_PATH.'widgets/'.$this->folder_name.'/display.php');
    }

    function update($new_instance, $old_instance) { }

    function prepare($instance, $results) {
        if (count($results) == 0) return array();
        foreach ($results as $r) {
            if ($instance["display_post_date"] == 1) $r->post_date = mysql2date($instance["display_post_date_format"], $r->post_date);
            if ($instance["display_excerpt"] == 1) $r->excerpt = $this->get_excerpt($instance, $r);
        }
        return $results;
    }

    function results($result) { }

    function render($results, $instance) { }

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
