<?php

if (!defined('ABSPATH')) exit;

class GDSWDefaults {
    var $default_options = array(
        'version' => '1.7',
        'date' => '2013.06.06.',
        'status' => 'Stable',
        'product_id' => 'gd-simple-widgets',
        'build' => 1700,
        'edition' => 'lite',
        'cache_data' => 1,
        'load_default_css' => 1,
        'debug_into_file' => 0,
        'lock_popular_posts' => 0,
        'widgets_recent_comments' => 1,
        'widgets_recent_posts' => 1,
        'widgets_most_commented' => 1,
        'widgets_posts_authors' => 1,
        'widgets_future_posts' => 1,
        'widgets_popular_posts' => 1,
        'widgets_random_posts' => 1,
        'widgets_related_posts' => 1,
        'widgets_random_posts' => 1,
        'widgets_simple_125_ads' => 1,
        'default_recent_comments' => 1,
        'default_recent_posts' => 1
    );

    function __construct() { }
}

?>