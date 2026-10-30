<?php
/**
 * Module: removes the zoom limits from the viewport meta tag added by the theme.
 *
 * Some themes print a viewport meta tag with "maximum-scale" and "user-scalable",
 * which block pinch-to-zoom on mobile (WCAG 2.2, success criterion 1.4.4).
 * The module replaces it with a meta tag without limits, for the themes listed in THEME_CALLBACKS.
 *
 * @package DesignMLP\Essentials
 */

namespace DesignMLP\Essentials\Modules;

use DesignMLP\Essentials\Module;

defined( 'ABSPATH' ) || exit;

final class ViewportZoom implements Module {

	// ---------------------------------------------------------------------------------------------
	// Module identity (every module has these). The name and description are translated texts,
	// so they are written out in get_name() and get_description() just below.
	// ---------------------------------------------------------------------------------------------

	/**
	 * Unique identifier, used in the saved settings.
	 */
	private const ID = 'viewport-zoom';

	// ---------------------------------------------------------------------------------------------
	// Values specific to how this module works.
	// ---------------------------------------------------------------------------------------------

	/**
	 * Content of the viewport meta tag printed instead of the theme's one (changeable with DESIGNMLP_ESSENTIALS_FILTER_VIEWPORT).
	 */
	private const DEFAULT_CONTENT = 'width=device-width, initial-scale=1';

	/**
	 * Priority on wp_head of the corrected meta tag: early in the head, as browsers expect it.
	 */
	private const PRIORITY = 1;

	/**
	 * Known theme functions that print a viewport meta tag on wp_head, with their hook priority.
	 * To support another theme, add its function here.
	 *
	 * @var array<string, int>
	 */
	private const THEME_CALLBACKS = array(
		// Divi 4 and Divi 5 (functions.php): "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0".
		'et_add_viewport_meta' => 10,
	);

	public function get_id(): string {
		return self::ID;
	}

	public function get_name(): string {
		return __( 'Viewport zoom', 'designmlp-essentials' );
	}

	public function get_description(): string {
		return __( 'Removes the zoom limits (maximum-scale, user-scalable) from the viewport meta tag added by the theme, so visitors can zoom the page.', 'designmlp-essentials' );
	}

	/**
	 * Available only if the active theme (or its parent theme) is one of the known themes.
	 */
	public function is_available(): bool {
		foreach ( array_keys( self::THEME_CALLBACKS ) as $callback ) {
			if ( function_exists( $callback ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Enabled by default: installing the plugin is enough to fix the problem.
	 */
	public function is_enabled_by_default(): bool {
		return true;
	}

	public function init(): void {
		// remove_action() returns true only if the theme's function was hooked: the correct meta tag is added
		// only in that case, so a site never ends up with two viewport meta tags.
		$removed = false;
		foreach ( self::THEME_CALLBACKS as $callback => $priority ) {
			if ( remove_action( 'wp_head', $callback, $priority ) ) {
				$removed = true;
			}
		}

		if ( $removed ) {
			add_action( 'wp_head', array( $this, 'print_viewport_meta' ), self::PRIORITY );
		}
	}

	/**
	 * Prints the viewport meta tag without zoom limits.
	 */
	public function print_viewport_meta(): void {
		/**
		 * Filters the content of the viewport meta tag printed instead of the theme's one.
		 *
		 * @param string $content Default "width=device-width, initial-scale=1".
		 */
		$content = apply_filters( DESIGNMLP_ESSENTIALS_FILTER_VIEWPORT, self::DEFAULT_CONTENT );

		echo '<meta name="viewport" content="' . esc_attr( $content ) . '" />' . "\n";
	}
}
