<?php
/**
 * Runs only when the plugin is deleted from the Plugins page (not when it is deactivated):
 * removes the data the plugin saved in the database.
 *
 * @package DesignMLP\Essentials
 */

// WordPress defines this constant only while deleting the plugin: stop if the file is opened in any other way.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Same name as Settings::OPTION. The autoloader is not loaded here, so the name is written out.
delete_option( 'designmlp_settings' );
