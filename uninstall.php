<?php
/**
 * Runs only when the plugin is deleted from the Plugins page (not when it is deactivated):
 * removes the data the plugin saved in the database.
 *
 * @package DesignMLP\Essentials
 */

// WordPress defines this constant only while deleting the plugin: stop if the file is opened in any other way.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// WordPress runs this file without loading the plugin: load the constants and the autoloader to use the Settings class.
require_once __DIR__ . '/constants.php';
require DESIGNMLP_ESSENTIALS_AUTOLOADER;

// Removes only this plugin's section of the option shared by the DesignMLP plugins
// (and the whole option if no other DesignMLP plugin has settings in it).
\DesignMLP\Essentials\Settings::delete_section();
