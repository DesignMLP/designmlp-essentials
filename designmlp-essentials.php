<?php
/**
 * Plugin Name:       DesignMLP Essentials
 * Plugin URI:        https://github.com/DesignMLP/designmlp-essentials
 * Description:       Recurring fixes for DesignMLP websites, split into modules that can be enabled one by one.
 * Version:           0.1.0
 * Requires at least: 7.0
 * Requires PHP:      8.2
 * Author:            DesignMLP
 * Author URI:        https://designmlp.com/
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       designmlp-essentials
 * Domain Path:       /languages
 * Update URI:        https://github.com/DesignMLP/designmlp-essentials
 *
 * @package DesignMLP\Essentials
 */

namespace DesignMLP\Essentials;

// Prevent direct access: this file must be loaded by WordPress.
defined( 'ABSPATH' ) || exit;

// All the values chosen for the plugin (keep the header above equal to them).
require_once __DIR__ . '/constants.php';

// The Composer autoloader is created by "composer install" and shipped inside the plugin zip.
// If it is missing (e.g. the plugin was installed from the GitHub source code), show a notice instead of a fatal error.
if ( ! is_readable( DESIGNMLP_ESSENTIALS_AUTOLOADER ) ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-error"><p>'
				. esc_html__( 'DesignMLP Essentials cannot start: the vendor folder is missing. Run "composer install" in the plugin folder.', 'designmlp-essentials' )
				. '</p></div>';
		}
	);
	return;
}

require DESIGNMLP_ESSENTIALS_AUTOLOADER;

( new Plugin() )->boot();
