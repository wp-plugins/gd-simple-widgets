<?php

/*
Plugin Name: GD Simple Widgets
Plugin URI: http://www.dev4press.com/plugins/gd-simple-widgets/
Description: Collection of powerful, easy to use widgets that expand default widgets with few more must-have widgets for posts, authors and comments.
Version: 1.7
Author: Milan Petrovic
Author URI: http://www.dev4press.com/

== Copyright ==

Copyright 2008 - 2012 Milan Petrovic (email: milan@gdragon.info)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

if (!defined('GDSIMPLEWIDGETS_LOG_PATH')) {
    define('GDSIMPLEWIDGETS_LOG_PATH', dirname(__FILE__).'/debug.txt');
}

if (!defined('GDSIMPLEWIDGETS_DEBUG_ACTIVE')) {
    define('GDSIMPLEWIDGETS_DEBUG_ACTIVE', true);
}

require_once(dirname(__FILE__).'/code/defaults.php');
require_once(dirname(__FILE__).'/code/widget.php');

require_once(dirname(__FILE__).'/gdragon/gd_debug.php');
require_once(dirname(__FILE__).'/gdragon/gd_functions.php');
require_once(dirname(__FILE__).'/gdragon/gd_wordpress.php');

require_once(dirname(__FILE__).'/integration.php');

if (!class_exists('GDSimpleWidgets')) {
    class GDSimpleWidgets {
        public $plugin_url;
        public $plugin_path;
        public $plugin_name;
        public $admin_plugin;
        public $admin_page;
        public $related_post_id;
        public $cache_active = false;

        public $cached = array(
            'posts' => array(),
            'authors' => array(),
            'comments' => array()
        );

        public $o;
        public $l;

        public $default_options;

        function __construct() {
            $this->plugin_name = plugin_basename(__FILE__);

            $gdd = new GDSWDefaults();
            $this->default_options = $gdd->default_options;
            define('GDSIMPLEWIDGETS_INSTALLED',$this->default_options['edition'].'_'. $this->default_options['version']." ".$this->default_options["status"]);

            $this->install_plugin();
            $this->plugin_path_url();
            $this->actions_filters();

            define('GDSIMPLEWIDGETS_DEBUG_SQL', $this->o['debug_into_file'] == 1);
        }

        private function install_plugin() {
            $this->o = get_option('gd-simple-widgets');

            if (!is_array($this->o)) {
                update_option('gd-simple-widgets', $this->default_options);
                $this->o = get_option('gd-simple-widgets');
            } else if ($this->o['build'] != $this->default_options['build'] ||
                $this->o['edition'] != $this->default_options['edition']) {
                $this->o = gdFunctionsGDSW::upgrade_settings($this->o, $this->default_options);

                $this->o['version'] = $this->default_options['version'];
                $this->o['date'] = $this->default_options['date'];
                $this->o['status'] = $this->default_options['status'];
                $this->o['build'] = $this->default_options['build'];
                $this->o['edition'] = $this->default_options['edition'];
                $this->o['product_id'] = $this->default_options['product_id'];

                update_option('gd-simple-widgets', $this->o);
            }

            $this->cache_active = $this->o['cache_data'] == 1;
        }

        private function plugin_path_url() {
            $this->plugin_url = WP_PLUGIN_URL.'/gd-simple-widgets/';
            $this->plugin_path = dirname(__FILE__).'/';

            define('GDSIMPLEWIDGETS_URL', $this->plugin_url);
            define('GDSIMPLEWIDGETS_PATH', $this->plugin_path);
        }

        private function actions_filters() {
            add_filter('plugin_row_meta', array(&$this, 'plugin_links'), 10, 2);
            add_filter('plugin_action_links', array(&$this, 'plugin_actions'), 10, 2);

            add_action('init', array(&$this, 'init'));
            add_action('widgets_init', array(&$this, 'widgets_init'));
            add_action('wp_head', array(&$this, 'wp_head'));

            add_action('admin_init', array(&$this, 'admin_init'));
            add_action('admin_menu', array(&$this, 'admin_menu'));

            add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
            add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));
        }

        public function admin_enqueue_scripts() {
            if ($this->admin_plugin) {
                wp_enqueue_style('gd-simple-widgets-admin', $this->plugin_url.'css/admin_main.css');
            }

            if ($this->admin_page == 'widgets.php' || $this->admin_page == 'themes.php' || $this->admin_page == 'plugins.php') {
                wp_enqueue_style('gd-simple-widgets-widgets', $this->plugin_url.'css/admin_widgets.css');
            }
        }

        public function wp_enqueue_scripts() {
            if ($this->o['load_default_css'] == 1 && !is_admin()) {
                wp_enqueue_style('gd-simple-widgets', $this->plugin_url.'css/simple_widgets.css');
            }
        }

        public function init() {
            $this->l = get_locale();

            if(!empty($this->l)) {
                load_plugin_textdomain('gd-simple-widgets', false, 'gd-simple-widgets/languages');
            }

            $this->o['lock_popular_posts'] = 1;

            if (defined('PRESSTOOLS_INSTALLED')) {
                $version = str_replace('.', '', substr(PRESSTOOLS_INSTALLED, 0, 5));

                if ($version >= 120) {
                    $this->o['lock_popular_posts'] = 0;
                }
            }

            if ($this->o['lock_popular_posts'] == 1) {
                $this->o['widgets_popular_posts'] = 0;
            }

            define('GDSIMPLEWIDGETS_ENCODING', get_option('blog_charset'));
        }

        public function widgets_init() {
            if ($this->o['widgets_recent_comments'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-recent-comments.php');
                register_widget('gdswRecentComments');
            }

            if ($this->o['widgets_recent_posts'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-recent-posts.php');
                register_widget('gdswRecentPosts');
            }

            if ($this->o['widgets_most_commented'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-most-commented.php');
                register_widget('gdswMostCommented');
            }

            if ($this->o['widgets_posts_authors'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-posts-authors.php');
                register_widget('gdswPostsAuthors');
            }

            if ($this->o['widgets_future_posts'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-future-posts.php');
                register_widget('gdswFuturePosts');
            }

            if ($this->o['widgets_popular_posts'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-popular-posts.php');
                register_widget('gdswPopularPosts');
            }

            if ($this->o['widgets_random_posts'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-random-posts.php');
                register_widget('gdswRandomPosts');
            }

            if ($this->o['widgets_related_posts'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-related-posts.php');
                register_widget('gdswRelatedPosts');
            }

            if ($this->o['widgets_simple_125_ads'] == 1) {
                require_once(GDSIMPLEWIDGETS_PATH.'widgets/gdsw-simple-125-ads.php');
                register_widget('gdswSimple125Ads');
            }

            if ($this->o['default_recent_comments'] == 0) {
                unregister_widget('WP_Widget_Recent_Comments');
            }

            if ($this->o['default_recent_posts'] == 0) {
                unregister_widget('WP_Widget_Recent_Posts');
            }
        }

        public function wp_head() {
            global $wp_query;

            $post_obj = $wp_query->get_queried_object();
            $this->related_post_id = is_object($post_obj) && isset($post_obj->ID) ? $post_obj->ID : 0;
        }

        public function plugin_links($links, $file) {
            static $this_plugin;
            global $gdtt;

            if (!$this_plugin) {
                $this_plugin = $this->plugin_name;
            }

            if ($file == $this_plugin){
                $links[] = '<a href="http://www.dev4press.com/plugins/gd-simple-widgets/faq/">'.__("FAQ", "gd-simple-widgets").'</a>';
                $links[] = '<a target="_blank" style="color: #cc0000; font-weight: bold;" href="http://www.dev4press.com/plugins/gd-simple-widgets/buy/">'.__("Upgrade to PRO", "gd-taxonomies-tools").'</a>';
            }
            return $links;
        }

        public function plugin_actions($links, $file) {
            static $this_plugin;
            global $gdtt;

            if (!$this_plugin) {
                $this_plugin = $this->plugin_name;
            }

            if ($file == $this_plugin){
                $settings_link = '<a href="admin.php?page=gd-simple-widgets">'.__("Settings", "gd-simple-widgets").'</a>';
                array_unshift($links, $settings_link);
            }
            return $links;
        }

        public function admin_init() {
            global $parent_file;
            $this->admin_page = $parent_file;

            if (isset($_GET['page']) && substr($_GET['page'], 0, 17) == 'gd-simple-widgets') {
                $this->admin_plugin = true;
            }

            $this->settings_operations();
        }

        public function settings_operations() {
            if (isset($_POST['widgets_saving'])) {
                $this->save_setting_checkbox('load_default_css');
                $this->save_setting_checkbox('cache_data');
                $this->save_setting_checkbox('debug_into_file');
                $this->save_setting_checkbox('widgets_recent_comments');
                $this->save_setting_checkbox('widgets_related_posts');
                $this->save_setting_checkbox('widgets_recent_posts');
                $this->save_setting_checkbox('widgets_most_commented');
                $this->save_setting_checkbox('widgets_posts_authors');
                $this->save_setting_checkbox('widgets_future_posts');
                $this->save_setting_checkbox('widgets_popular_posts');
                $this->save_setting_checkbox('widgets_random_posts');
                $this->save_setting_checkbox('widgets_related_posts');
                $this->save_setting_checkbox('widgets_simple_125_ads');
                $this->save_setting_checkbox('default_recent_comments');
                $this->save_setting_checkbox('default_recent_posts');
                $this->save_setting_checkbox('default_random_posts');

                update_option('gd-simple-widgets', $this->o);
                wp_redirect_self();
                exit;
            }
        }

        public function save_setting_checkbox($name) {
            $this->o[$name] = isset($_POST[$name]) ? 1 : 0;
        }

        public function admin_menu() {
            add_options_page('GD Simple Widgets', 'GD Simple Widgets', 'activate_plugins', 'gd-simple-widgets', array(&$this, 'admin_widgets_panel'));
        }

        public function admin_widgets_panel() {
            $options = $this->o;

            include($this->plugin_path.'admin/panel.php');
        }
    }

    $gdsw_debug = new gdDebugGDSW(GDSIMPLEWIDGETS_LOG_PATH);
    $gdsw = new GDSimpleWidgets();

    function wp_gdsw_dump($msg, $obj, $block = 'none', $mode = 'a+') {
        if (GDSIMPLEWIDGETS_DEBUG_ACTIVE == 1) {
            global $gdsw_debug;
            $gdsw_debug->dump($msg, $obj, $block, $mode);
        }
    }

    function wp_gdsw_log_sql($msg, $obj, $block = 'none', $mode = 'a+') {
        if (GDSIMPLEWIDGETS_DEBUG_SQL) wp_gdsw_dump($msg, $obj, $block, $mode);
    }
}

?>