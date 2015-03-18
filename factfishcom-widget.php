<?php
/*
Plugin Name: Factfish.com Widget
Plugin URI: http://www.kux.de/wordpress-plugin-json-content-importer
Description: Display content from factfish.com in Wordpress
Version: 1.0.0
Author: Bernhard Kux
Author URI: http://www.kux.de/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// block direct requests
if ( !defined('ABSPATH') ) {	die('-1'); }

/* WIDGET BEGIN */
add_action('widgets_init', create_function('', 'return register_widget("factfishcom_widget_plugin");'));
require_once plugin_dir_path( __FILE__ ) . '/class-factfishcom-widget.php';
/* WIDGET END */
?>