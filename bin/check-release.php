<?php
/**
 * Release check: the values written out in the plugin header and in readme.txt must be equal to constants.php.
 *
 * Usage (from the plugin folder): php bin/check-release.php [version]
 * With [version] (e.g. the release tag without "v") it also checks that the plugin has that version.
 * Exit code 0 if everything matches, 1 otherwise. Used by bin/prepare-release.php and by the release workflow.
 *
 * @package DesignMLP\Essentials
 */

// constants.php stops if WordPress is not loaded: pretend it is, only to read the values.
define( 'ABSPATH', __DIR__ . '/' );
require dirname( __DIR__ ) . '/constants.php';

$root   = dirname( __DIR__ );
$errors = array();

/**
 * Reads a "Name: value" line from a file header (plugin header or readme.txt).
 */
function designmlp_read_header_field( string $file, string $field ): ?string {
	$content = (string) file_get_contents( $file );
	if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $field, '/' ) . ':[ \t]*(.+)$/mi', $content, $match ) ) {
		return trim( $match[1] );
	}
	return null;
}

$main   = $root . '/' . DESIGNMLP_ESSENTIALS_SLUG . '.php';
$readme = $root . '/readme.txt';

// Repository URL without the final slash, as written in the header.
$repository = rtrim( DESIGNMLP_ESSENTIALS_REPOSITORY, '/' );

$checks = array(
	array( $main, 'Plugin Name', DESIGNMLP_ESSENTIALS_NAME ),
	array( $main, 'Version', DESIGNMLP_ESSENTIALS_VERSION ),
	array( $main, 'Text Domain', DESIGNMLP_ESSENTIALS_TEXT_DOMAIN ),
	array( $main, 'Domain Path', DESIGNMLP_ESSENTIALS_LANGUAGES_PATH ),
	array( $main, 'Plugin URI', $repository ),
	array( $main, 'Update URI', $repository ),
	array( $readme, 'Stable tag', DESIGNMLP_ESSENTIALS_VERSION ),
);

foreach ( $checks as list( $file, $field, $expected ) ) {
	$found = designmlp_read_header_field( $file, $field );
	if ( $found !== $expected ) {
		$errors[] = sprintf( '%s, "%s": found "%s", expected "%s" (constants.php)', basename( $file ), $field, $found ?? '(missing)', $expected );
	}
}

$wanted = $argv[1] ?? null;
if ( null !== $wanted && $wanted !== DESIGNMLP_ESSENTIALS_VERSION ) {
	$errors[] = sprintf( 'Release version "%s" is different from DESIGNMLP_ESSENTIALS_VERSION "%s"', $wanted, DESIGNMLP_ESSENTIALS_VERSION );
}

if ( $errors ) {
	fwrite( STDERR, "Release check FAILED:\n - " . implode( "\n - ", $errors ) . "\n" );
	exit( 1 );
}

echo 'Release check OK: version ' . DESIGNMLP_ESSENTIALS_VERSION . "\n";
exit( 0 );
