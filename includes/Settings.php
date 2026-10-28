<?php
/**
 * Settings page (Settings > DesignMLP Essentials) and saved state of the modules.
 *
 * @package DesignMLP\Essentials
 */

namespace DesignMLP\Essentials;

defined( 'ABSPATH' ) || exit;

final class Settings {

	/**
	 * Name of the option saved in the database, e.g. array( 'modules' => array( 'viewport-zoom' => true ) ).
	 */
	public const OPTION = 'designmlp_settings';

	/**
	 * Slug of the settings page (admin URL: options-general.php?page=designmlp-essentials).
	 */
	private const PAGE = 'designmlp-essentials';

	/**
	 * Settings group used by the Settings API to save the form.
	 */
	private const GROUP = 'designmlp_settings_group';

	/**
	 * Capability needed to see and save the settings: administrators.
	 */
	private const CAPABILITY = 'manage_options';

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
	 * Adds the page under Settings.
	 */
	public function add_page(): void {
		add_options_page(
			__( 'DesignMLP Essentials', 'designmlp-essentials' ),
			__( 'DesignMLP Essentials', 'designmlp-essentials' ),
			self::CAPABILITY,
			self::PAGE,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Registers the option with the Settings API, which saves the form and checks permissions and nonce.
	 */
	public function register_setting(): void {
		register_setting(
			self::GROUP,
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Builds the value to save from the submitted form: only known modules, only true/false.
	 *
	 * @param mixed $input Submitted values.
	 * @return array<string, array<string, bool>>
	 */
	public function sanitize( $input ): array {
		$submitted = ( is_array( $input ) && isset( $input['modules'] ) && is_array( $input['modules'] ) ) ? $input['modules'] : array();

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

		return array( 'modules' => $modules );
	}

	/**
	 * Adds a "Settings" link under the plugin name in the plugins list.
	 *
	 * @param string[] $links Existing links.
	 * @return string[]
	 */
	public function add_settings_link( array $links ): array {
		$url  = admin_url( 'options-general.php?page=' . self::PAGE );
		$link = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'designmlp-essentials' ) . '</a>';

		array_unshift( $links, $link );
		return $links;
	}

	/**
	 * Prints the settings page: one checkbox per module.
	 */
	public function render_page(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php esc_html_e( 'Enable or disable each module. A module not available on this site stays inactive.', 'designmlp-essentials' ); ?></p>

			<form action="options.php" method="post">
				<?php settings_fields( self::GROUP ); ?>

				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $this->modules as $module ) : ?>
						<?php
						$id        = $module->get_id();
						$field_id  = 'designmlp-module-' . $id;
						$available = $module->is_available();
						?>
						<tr>
							<th scope="row"><?php echo esc_html( $module->get_name() ); ?></th>
							<td>
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<input
										type="checkbox"
										id="<?php echo esc_attr( $field_id ); ?>"
										name="<?php echo esc_attr( self::OPTION . '[modules][' . $id . ']' ); ?>"
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
	 * Saved state of the modules, as id => enabled.
	 *
	 * @return array<string, bool>
	 */
	private function get_saved_modules(): array {
		$saved = get_option( self::OPTION, array() );

		if ( is_array( $saved ) && isset( $saved['modules'] ) && is_array( $saved['modules'] ) ) {
			return $saved['modules'];
		}

		return array();
	}
}
