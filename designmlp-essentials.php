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

define( 'DESIGNMLP_ESSENTIALS_VERSION', '0.1.0' );
define( 'DESIGNMLP_ESSENTIALS_FILE', __FILE__ );
