<?php
/**
 * Automatic updates from the GitHub releases of the plugin, with the Plugin Update Checker library.
 *
 * WordPress shows "Update available" like for the plugins of wordpress.org, with the changelog of readme.txt.
 *
 * @package DesignMLP\Essentials
 */

namespace DesignMLP\Essentials;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
use YahnisElsts\PluginUpdateChecker\v5p7\Vcs\Api;

defined( 'ABSPATH' ) || exit;

final class Updater {

	/**
	 * Starts the update checks (by default every 12 hours) on the public GitHub repository:
	 * no token is needed on the websites.
	 */
	public function boot(): void {
		$checker = PucFactory::buildUpdateChecker(
			DESIGNMLP_ESSENTIALS_REPOSITORY,
			DESIGNMLP_ESSENTIALS_FILE,
			DESIGNMLP_ESSENTIALS_SLUG
		);

		// Use only releases with the zip attached by the release workflow: never fall back to the "Source code"
		// archive, which has no vendor folder and would install a plugin that cannot start.
		$zip_name_regex = '/' . preg_quote( DESIGNMLP_ESSENTIALS_RELEASE_ZIP, '/' ) . '$/';
		$checker->getVcsApi()->enableReleaseAssets( $zip_name_regex, Api::REQUIRE_RELEASE_ASSETS );

		// Without releases, the library would look at the tags and then at the main branch itself:
		// a version number raised on main before a release would offer an update without the vendor folder.
		add_filter( $checker->getUniqueName( 'vcs_update_detection_strategies' ), array( $this, 'only_releases' ) );
	}

	/**
	 * Keeps only the "latest release" strategy among the ways the library looks for a new version.
	 *
	 * @param array<string, callable> $strategies Update detection strategies, by name.
	 * @return array<string, callable>
	 */
	public function only_releases( array $strategies ): array {
		return array_intersect_key( $strategies, array( Api::STRATEGY_LATEST_RELEASE => true ) );
	}
}
