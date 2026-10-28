<?php
/**
 * Contract that every module of the plugin must follow.
 *
 * @package DesignMLP\Essentials
 */

namespace DesignMLP\Essentials;

defined( 'ABSPATH' ) || exit;

interface Module {

	/**
	 * Unique identifier, used in the saved settings (e.g. "viewport-zoom").
	 */
	public function get_id(): string;

	/**
	 * Name shown in the settings page.
	 */
	public function get_name(): string;

	/**
	 * Short description shown in the settings page.
	 */
	public function get_description(): string;

	/**
	 * Whether the module can work on this site (e.g. the active theme is one the module supports).
	 */
	public function is_available(): bool;

	/**
	 * Hooks the module into WordPress. Called only if the module is available and enabled.
	 */
	public function init(): void;
}
