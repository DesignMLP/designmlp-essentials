<?php
/**
 * Settings page (Settings > DesignMLP Essentials) and saved state of the modules.
 *
 * The settings are saved in the option shared by all DesignMLP plugins (DESIGNMLP_ESSENTIALS_OPTION),
 * in this plugin's section (DESIGNMLP_ESSENTIALS_OPTION_SECTION). A plugin must read and write only its own
 * section and keep the others unchanged.
 *
 * @package DesignMLP\Essentials
 */

namespace DesignMLP\Essentials;

defined( 'ABSPATH' ) || exit;

final class Settings {

	/**
	 * All the modules of the plugin.
	 *
	 * @var Module[]
	 */
	private array $modules;

	/**
	 * @param Module[] $modules All the modules of the plugin.
	 */
	public function __construct( array $modules ) {
		$this->modules = $modules;
	}

	/**
	 * Registers the settings page with WordPress.
	 */
	public function boot(): void {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( DESIGNMLP_ESSENTIALS_FILE ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Whether a module is enabled: the saved value, or the module's default if nothing was saved for it.
	 *
	 * @param Module $module The module to check.
	 */
	public function is_enabled( Module $module ): bool {
		$saved = $this->get_saved_modules();

		if ( array_key_exists( $module->get_id(), $saved ) ) {
			return (bool) $saved[ $module->get_id() ];
		}

		return $module->is_enabled_by_default();
	}

	/**
	 * Adds the page under Settings. Page title and menu item are the plugin name, which is not translated.
	 */
	public function add_page(): void {
		add_options_page(
			DESIGNMLP_ESSENTIALS_NAME,
			DESIGNMLP_ESSENTIALS_NAME,
			DESIGNMLP_ESSENTIALS_CAPABILITY,
			DESIGNMLP_ESSENTIALS_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Registers the option with the Settings API, which saves the form and checks permissions and nonce.
	 */
	public function register_setting(): void {
		register_setting(
			DESIGNMLP_ESSENTIALS_SETTINGS_GROUP,
			DESIGNMLP_ESSENTIALS_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Builds the value to save. WordPress calls this every time the shared option is saved, also by other
	 * DesignMLP plugins: it changes only this plugin's section, and only when this plugin's form was submitted.
	 *
	 * @param mixed $input Value being saved.
	 * @return mixed
	 */
	public function sanitize( $input ) {
		$section = is_array( $input ) && isset( $input[ DESIGNMLP_ESSENTIALS_OPTION_SECTION ] ) && is_array( $input[ DESIGNMLP_ESSENTIALS_OPTION_SECTION ] )
			? $input[ DESIGNMLP_ESSENTIALS_OPTION_SECTION ]
			: array();

		if ( empty( $section[ DESIGNMLP_ESSENTIALS_OPTION_KEY_SUBMITTED ] ) ) {
			return $input;
		}

		$submitted = ( isset( $section[ DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES ] ) && is_array( $section[ DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES ] ) )
			? $section[ DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES ]
			: array();

		$modules = array();
		foreach ( $this->modules as $module ) {
			$id = $module->get_id();

			// Modules not available on this site have a disabled checkbox, which the browser does not submit:
			// keep their current state instead of switching them off.
			if ( ! $module->is_available() ) {
				$modules[ $id ] = $this->is_enabled( $module );
				continue;
			}

			$modules[ $id ] = ! empty( $submitted[ $id ] );
		}

		// Keep the sections of the other DesignMLP plugins as they are.
		$value = self::get_option_value();
		$value[ DESIGNMLP_ESSENTIALS_OPTION_SECTION ] = array( DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES => $modules );

		return $value;
	}

	/**
	 * Removes this plugin's section from the shared option, and the whole option if no other section is left.
	 * Used by uninstall.php.
	 */
	public static function delete_section(): void {
		$value = get_option( DESIGNMLP_ESSENTIALS_OPTION );
		if ( ! is_array( $value ) ) {
			return;
		}

		unset( $value[ DESIGNMLP_ESSENTIALS_OPTION_SECTION ] );

		if ( empty( $value ) ) {
			delete_option( DESIGNMLP_ESSENTIALS_OPTION );
		} else {
			update_option( DESIGNMLP_ESSENTIALS_OPTION, $value );
		}
	}

	/**
	 * Adds a "Settings" link under the plugin name in the plugins list.
	 *
	 * @param string[] $links Existing links.
	 * @return string[]
	 */
	public function add_settings_link( array $links ): array {
		$url  = add_query_arg( 'page', DESIGNMLP_ESSENTIALS_SLUG, admin_url( 'options-general.php' ) );
		$link = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'designmlp-essentials' ) . '</a>';

		array_unshift( $links, $link );
		return $links;
	}

	/**
	 * Prints the settings page: one checkbox per module.
	 */
	public function render_page(): void {
		if ( ! current_user_can( DESIGNMLP_ESSENTIALS_CAPABILITY ) ) {
			return;
		}

		$field_name = DESIGNMLP_ESSENTIALS_OPTION . '[' . DESIGNMLP_ESSENTIALS_OPTION_SECTION . ']';
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php esc_html_e( 'Enable or disable each module. A module not available on this site stays inactive.', 'designmlp-essentials' ); ?></p>

			<form action="options.php" method="post">
				<?php settings_fields( DESIGNMLP_ESSENTIALS_SETTINGS_GROUP ); ?>
				<?php // Marks the form of this plugin, so sanitize() changes only this plugin's section. ?>
				<input type="hidden" name="<?php echo esc_attr( $field_name . '[' . DESIGNMLP_ESSENTIALS_OPTION_KEY_SUBMITTED . ']' ); ?>" value="1" />

				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $this->modules as $module ) : ?>
						<?php
						$id        = $module->get_id();
						$field_id  = DESIGNMLP_ESSENTIALS_SLUG . '-' . $id;
						$available = $module->is_available();
						?>
						<tr>
							<th scope="row"><?php echo esc_html( $module->get_name() ); ?></th>
							<td>
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<input
										type="checkbox"
										id="<?php echo esc_attr( $field_id ); ?>"
										name="<?php echo esc_attr( $field_name . '[' . DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES . '][' . $id . ']' ); ?>"
										value="1"
										<?php
										// A module not available is shown unchecked because it is not running; its saved
										// state is kept, so it starts again by itself if the site goes back to a supported theme.
										checked( $available && $this->is_enabled( $module ) );
										?>
										<?php disabled( ! $available ); ?>
									/>
									<?php esc_html_e( 'Enabled', 'designmlp-essentials' ); ?>
								</label>
								<p class="description"><?php echo esc_html( $module->get_description() ); ?></p>
								<?php if ( ! $available ) : ?>
									<p class="description"><em><?php esc_html_e( 'Not running at the moment: the module finds nothing on this site that it can fix.', 'designmlp-essentials' ); ?></em></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Saved state of the modules of this plugin, as id => enabled.
	 *
	 * @return array<string, bool>
	 */
	private function get_saved_modules(): array {
		$value = self::get_option_value();

		if ( isset( $value[ DESIGNMLP_ESSENTIALS_OPTION_SECTION ][ DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES ] )
			&& is_array( $value[ DESIGNMLP_ESSENTIALS_OPTION_SECTION ][ DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES ] ) ) {
			return $value[ DESIGNMLP_ESSENTIALS_OPTION_SECTION ][ DESIGNMLP_ESSENTIALS_OPTION_KEY_MODULES ];
		}

		return array();
	}

	/**
	 * Current value of the shared option, always as an array.
	 *
	 * @return array<string, mixed>
	 */
	private static function get_option_value(): array {
		$value = get_option( DESIGNMLP_ESSENTIALS_OPTION, array() );

		return is_array( $value ) ? $value : array();
	}
}
