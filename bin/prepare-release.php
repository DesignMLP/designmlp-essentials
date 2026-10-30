<?php
/**
 * Prepares a release on this PC, before the commit and the GitHub release.
 *
 * Usage (from the plugin folder): php bin/prepare-release.php 1.0.0
 *
 * 1. Writes the new version in constants.php, in the plugin header and in the "Stable tag" of readme.txt.
 * 2. In CHANGELOG.md turns "## [Unreleased]" into "## [1.0.0] - YYYY-MM-DD", with a new empty "Unreleased" above.
 * 3. Rebuilds the "Changelog" section of readme.txt from CHANGELOG.md (CHANGELOG.md is the original).
 * 4. Runs bin/check-release.php.
 * Then: look at the differences in GitHub Desktop, commit "Release 1.0.0", push, publish the release "v1.0.0".
 *
 * @package DesignMLP\Essentials
 */

$root    = dirname( __DIR__ );
$version = $argv[1] ?? '';

/**
 * Stops with a message and exit code 1.
 */
function designmlp_fail( string $message ): void {
	fwrite( STDERR, 'Prepare release FAILED: ' . $message . "\n" );
	exit( 1 );
}

/**
 * Replaces exactly one match of a regular expression in a file.
 */
function designmlp_replace_in_file( string $file, string $pattern, string $replacement ): void {
	$content = (string) file_get_contents( $file );
	$updated = preg_replace( $pattern, $replacement, $content, 1, $count );
	if ( 1 !== $count ) {
		designmlp_fail( 'pattern not found in ' . basename( $file ) . ': ' . $pattern );
	}
	file_put_contents( $file, $updated );
}

// Versions: MAJOR.MINOR.PATCH (1.0.1 fixes, 1.1.0 new features, 2.0.0 changes that break something).
if ( ! preg_match( '/^\d+\.\d+\.\d+$/', $version ) ) {
	designmlp_fail( 'give the new version as MAJOR.MINOR.PATCH, e.g. php bin/prepare-release.php 1.0.0' );
}

$changelog_file = $root . '/CHANGELOG.md';
$readme_file    = $root . '/readme.txt';
$changelog      = (string) file_get_contents( $changelog_file );

// The changes of this release must already be written under "Unreleased".
if ( ! preg_match( '/^## \[Unreleased\]\s*\n(.*?)(?=^## \[|\z)/ms', $changelog, $unreleased ) || '' === trim( $unreleased[1] ) ) {
	designmlp_fail( 'CHANGELOG.md has no changes under "## [Unreleased]"' );
}
if ( false !== strpos( $changelog, '## [' . $version . ']' ) ) {
	designmlp_fail( 'CHANGELOG.md already has version ' . $version );
}

// 1. Version in the three places.
designmlp_replace_in_file( $root . '/constants.php', "/(define\( 'DESIGNMLP_ESSENTIALS_VERSION', ')[^']+(' \);)/", '${1}' . $version . '${2}' );
designmlp_replace_in_file( $root . '/designmlp-essentials.php', '/^( \* Version:[ \t]+).+$/m', '${1}' . $version );
designmlp_replace_in_file( $readme_file, '/^(Stable tag:[ \t]+).+$/m', '${1}' . $version );

// 2. CHANGELOG.md: "Unreleased" becomes the new version with today's date.
$changelog = preg_replace( '/^## \[Unreleased\]\s*\n/m', "## [Unreleased]\n\n## [" . $version . '] - ' . gmdate( 'Y-m-d' ) . "\n\n", $changelog, 1 );
file_put_contents( $changelog_file, $changelog );

// 3. readme.txt: "Changelog" section rebuilt from CHANGELOG.md, newest version first.
//    "## [1.0.0] - 2026-11-05" becomes "= 1.0.0 =", "### Added" + "- text" becomes "* Added: text".
preg_match_all( '/^## \[(\d+\.\d+\.\d+)\][^\n]*\n(.*?)(?=^## \[|\z)/ms', $changelog, $versions, PREG_SET_ORDER );
$blocks = array();
foreach ( $versions as $entry ) {
	$block = '= ' . $entry[1] . " =\n";
	$type  = '';
	foreach ( explode( "\n", $entry[2] ) as $line ) {
		if ( preg_match( '/^### (.+)$/', $line, $heading ) ) {
			$type = trim( $heading[1] ) . ': ';
		} elseif ( preg_match( '/^- (.+)$/', $line, $item ) ) {
			$block .= '* ' . $type . trim( $item[1] ) . "\n";
		}
	}
	$blocks[] = $block;
}
$readme = (string) file_get_contents( $readme_file );
$readme = preg_replace( '/^== Changelog ==.*\z/ms', "== Changelog ==\n\n" . implode( "\n", $blocks ), $readme, 1, $count );
if ( 1 !== $count ) {
	designmlp_fail( 'readme.txt has no "== Changelog ==" section' );
}
file_put_contents( $readme_file, $readme );

echo 'Prepared version ' . $version . ": constants.php, plugin header, readme.txt, CHANGELOG.md.\n";

// 4. Final check, in a separate PHP process (constants.php has just changed).
passthru( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __DIR__ . '/check-release.php' ) . ' ' . escapeshellarg( $version ), $status );
exit( $status );
