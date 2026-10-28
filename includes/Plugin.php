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

	public function __construct() {
		// Add new modules to this list.
		$this->modules = array(
			new Modules\ViewportZoom(),
		);
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function boot(): void {
		// Plugins are loaded before the theme: modules start after the theme has been set up,
		// so they can check which theme is active and change what the theme registers.
		add_action( 'after_setup_theme', array( $this, 'init_modules' ) );
	}

	/**
	 * Starts every module that is available on this site and enabled.
	 */
	public function init_modules(): void {
		foreach ( $this->modules as $module ) {
			if ( $module->is_available() && $this->is_enabled( $module ) ) {
				$module->init();
			}
		}
	}

	/**
	 * Whether a module is enabled. All modules are enabled until the settings page exists.
	 *
	 * @param Module $module The module to check.
	 */
	private function is_enabled( Module $module ): bool {
		return true;
	}
}
