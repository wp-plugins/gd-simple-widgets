<?php

/**
 * Get results for any of the supported widgets.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_widget_results($widget, $instance = array()) {
    switch ($widget) {
        case "future_posts":
            return gdws_future_posts_results($instance);
            break;
        case "most_commented":
            return gdws_most_commented_results($instance);
            break;
        case "popular_posts":
            return gdws_popular_posts_results($instance);
            break;
        case "post_authors":
            return gdws_post_authors_results($instance);
            break;
        case "random_posts":
            return gdws_random_posts_results($instance);
            break;
        case "recent_comments":
            return gdws_recent_comments_results($instance);
            break;
        case "recent_posts":
            return gdws_recent_posts_results($instance);
            break;
    }
}

/**
 * Render results for any of the supported widgets.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_widget_render($widget, $instance = array(), $echo = true) {
    switch ($widget) {
        case "future_posts":
            return gdws_future_posts_render($instance, $echo);
            break;
        case "most_commented":
            return gdws_most_commented_render($instance, $echo);
            break;
        case "popular_posts":
            return gdws_popular_posts_render($instance, $echo);
            break;
        case "post_authors":
            return gdws_post_authors_render($instance, $echo);
            break;
        case "random_posts":
            return gdws_random_posts_render($instance, $echo);
            break;
        case "recent_comments":
            return gdws_recent_comments_render($instance, $echo);
            break;
        case "recent_posts":
            return gdws_recent_posts_render($instance, $echo);
            break;
    }
}

/**
 * Get results for Future Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_future_posts_results($instance = array()) {
    $widget = new gdswFuturePosts();
    return $widget->results($instance);
}

/**
 * Render results for Future Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_future_posts_render($instance = array(), $echo = true) {
    $widget = new gdswFuturePosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Most Commented widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_most_commented_results($instance = array()) {
    $widget = new gdswMostCommented();
    return $widget->results($instance);
}

/**
 * Render results for Most Commented widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_most_commented_render($instance = array(), $echo = true) {
    $widget = new gdswMostCommented();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Popular Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_popular_posts_results($instance = array()) {
    $widget = new gdswPopularPosts();
    return $widget->results($instance);
}

/**
 * Render results for Popular Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_popular_posts_render($instance = array(), $echo = true) {
    $widget = new gdswPopularPosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Post Authors widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_post_authors_results($instance = array()) {
    $widget = new gdswPostsAuthors();
    return $widget->results($instance);
}

/**
 * Render results for Post Authors widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_post_authors_render($instance = array(), $echo = true) {
    $widget = new gdswPostsAuthors();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Random Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_random_posts_results($instance = array()) {
    $widget = new gdswRandomPosts();
    return $widget->results($instance);
}

/**
 * Render results for Random Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_random_posts_render($instance = array(), $echo = true) {
    $widget = new gdswRandomPosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Recent Comments widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_recent_comments_results($instance = array()) {
    $widget = new gdswRecentComments();
    return $widget->results($instance);
}

/**
 * Render results for Recent Comments widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_recent_comments_render($instance = array(), $echo = true) {
    $widget = new gdswRecentComments();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

/**
 * Get results for Recent Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @return array results
 */
function gdws_recent_posts_results($instance = array()) {
    $widget = new gdswRecentPosts();
    return $widget->results($instance);
}

/**
 * Render results for Recent Posts widget.
 *
 * @param string $widget widget code name
 * @param array $instance widget settings
 * @param bool $echo echo results if true return if false
 * @return array results
 */
function gdws_recent_posts_render($instance = array(), $echo = true) {
    $widget = new gdswRecentPosts();
    $render = $widget->simple_render($instance);
    if ($echo) echo $render; else return $render;
}

?>