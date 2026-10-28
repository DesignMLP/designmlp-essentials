<?php
/**
 * Main plugin class: knows the modules and starts the ones that can run.
 *
 * @package DesignMLP\Essentials
 */

namespace DesignMLP\Essentials;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	/**
	 * All the modules of the plugin.
	 *
	 * @var Module[]
	 */
	private array $modules = array();

	/**
	 * Settings page and saved state of the modules.
	 */
	private Settings $settings;

	public function __construct() {
		// Add new modules to this list.
		$this->modules = array(
			new Modules\ViewportZoom(),
		);

		$this->settings = new Settings( $this->modules );
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function boot(): void {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Plugins are loaded before the theme: modules start after the theme has been set up,
		// so they can check which theme is active and change what the theme registers.
		add_action( 'after_setup_theme', array( $this, 'init_modules' ) );

		if ( is_admin() ) {
			$this->settings->boot();
		}
	}

	/**
	 * Loads the translations shipped in the languages folder.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain( 'designmlp-essentials', false, dirname( plugin_basename( DESIGNMLP_ESSENTIALS_FILE ) ) . '/languages' );
	}

	/**
	 * Starts every module that is available on this site and enabled.
	 */
	public function init_modules(): void {
		foreach ( $this->modules as $module ) {
			if ( $module->is_available() && $this->settings->is_enabled( $module ) ) {
				$module->init();
			}
		}
	}
}
