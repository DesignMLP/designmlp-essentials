<?php
/**
 * Values chosen for this plugin, all in one place. Loaded by designmlp-essentials.php and by uninstall.php
 * (WordPress runs uninstall.php without loading the plugin).
 *
 * Not here, on purpose:
 * - the plugin header in designmlp-essentials.php, which WordPress reads as plain text: keep
 *   Plugin Name, Version, Text Domain, Domain Path, Plugin URI and Update URI equal to the values below;
 * - the text domain inside __(), esc_html__() and similar, which the translation tools read only when written out;
 * - the values of a single module, which are at the top of the module file.
 *
 * @package DesignMLP\Essentials
 */

defined( 'ABSPATH' ) || exit;

// Plugin.
define( 'DESIGNMLP_ESSENTIALS_NAME', 'DesignMLP Essentials' );
define( 'DESIGNMLP_ESSENTIALS_VERSION', '0.1.0' );
define( 'DESIGNMLP_ESSENTIALS_SLUG', 'designmlp-essentials' );
define( 'DESIGNMLP_ESSENTIALS_FILE', __DIR__ . '/' . DESIGNMLP_ESSENTIALS_SLUG . '.php' );
define( 'DESIGNMLP_ESSENTIALS_AUTOLOADER', __DIR__ . '/vendor/autoload.php' );

// Translations.
define( 'DESIGNMLP_ESSENTIALS_TEXT_DOMAIN', 'designmlp-essentials' );
define( 'DESIGNMLP_ESSENTIALS_LANGUAGES_PATH', '/languages' );

// Updates from GitHub releases.
define( 'DESIGNMLP_ESSENTIALS_REPOSITORY', 'https://github.com/DesignMLP/designmlp-essentials/' );
define( 'DESIGNMLP_ESSENTIALS_RELEASE_ZIP', DESIGNMLP_ESSENTIALS_SLUG . '.zip' );

// Settings: option shared by all DesignMLP plugins, each plugin in its own section,
// e.g. designmlp_settings = array( 'essentials' => array( 'modules' => array( 'viewport-zoom' => true ) ) ).
define( 'DESIGNMLP_ESSENTIALS_OPTION', 'designmlp_settings' );
define( 'DESIGNMLP_ESSENTIALS_OPTION_SECTION', 'essentials' );
define( 'DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES', 'modules' );
define( 'DESIGNMLP_ESSENTIALS_OPTION_KEY_SUBMITTED', 'submitted' );
define( 'DESIGNMLP_ESSENTIALS_SETTINGS_GROUP', 'designmlp_essentials' );
define( 'DESIGNMLP_ESSENTIALS_CAPABILITY', 'manage_options' );

// Hooks offered to other code (themes, child themes, other plugins).
define( 'DESIGNMLP_ESSENTIALS_FILTER_VIEWPORT', 'designmlp_viewport_content' );
