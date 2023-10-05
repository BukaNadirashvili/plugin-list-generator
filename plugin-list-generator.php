<?php

namespace PluginListGenerator;

use PluginListGenerator\Form;
/*
Plugin Name: Plugin List Generator
Description: Generate a list of plugins in PDF format.
Author: Bondo Nadirashvili
Text Domain: plugin-list-generator
Version: 1.0.0
*/


define('PLUGIN_LIST_GENERATOR_DIR_PATH', plugin_dir_path( __FILE__ ));
define('PLUGIN_LIST_GENERATOR_DIR_URL', plugin_dir_url( __FILE__ ));

// Require autoload
require PLUGIN_LIST_GENERATOR_DIR_PATH . "/vendor/autoload.php";

// Create object for admin form  
new Form;

add_action('plugins_loaded', function(){

  // Load Plugin List Generator textdomain.
  load_plugin_textdomain('plugin-list-generator', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');

});