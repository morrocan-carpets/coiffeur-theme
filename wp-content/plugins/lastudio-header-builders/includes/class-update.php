<?php
/**
 * LA-Studio Core Update class.
 *
 * @package LA-Studio
 */

// don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

if ( ! class_exists( 'LAHB_Update' ) ) :

	/**
	 * Creates the connection between Github to install & update the LA-Studio Core plugin.
	 *
	 * @class LAHB_Update
	 * @version 1.0.0
	 * @since 1.0.0
	 */
	class LAHB_Update {

		/**
		 * Action nonce.
		 *
		 * @type string
		 */
		const AJAX_ACTION = 'lahb_plugin_update_dismiss_notice';

		/**
		 * The single class instance.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var object
		 */
		private static $_instance = null;

		/**
		 * The API URL.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var string
		 */
		private static $api_url = 'https://la-studioweb.com/file-resouces/shared/plugins/lastudio-header-builders/update-check.json';

		private static $_plugin_slug = 'lastudio-header-builders';

		private static $_plugin_url = 'https://la-studioweb.com';

		private static $_plugin_file = 'lastudio-header-builders/lastudio-header-builder.php';

		private static $_plugin_file_php = 'lastudio-header-builder.php';

		private static $_plugin_name = 'LA-Studio Header Builder';

		private static $_plugin_opt_key = 'lahb_plugin_state';

		/**
		 * The LAHB_Update Instance
		 *
		 * Ensures only one instance of this class exists in memory at any one time.
		 *
		 * @see LAHB_Update()
		 * @uses LAHB_Update::init_actions() Setup hooks and actions.
		 *
		 * @since 1.0.0
		 * @static
		 * @return object The one true LAHB_Update.
		 * @codeCoverageIgnore
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::$_instance->init_actions();
			}
			return self::$_instance;
		}

		/**
		 * A dummy constructor to prevent this class from being loaded more than once.
		 *
		 * @see Envato_Market_Github::instance()
		 *
		 * @since 1.0.0
		 * @access private
		 * @codeCoverageIgnore
		 */
		private function __construct() {
			/* We do nothing here! */
		}

		/**
		 * You cannot clone this class.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'lastudio' ), '1.0.0' );
		}

		/**
		 * You cannot unserialize instances of this class.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'lastudio' ), '1.0.0' );
		}

		/**
		 * Setup the actions and filters.
		 *
		 * @uses add_action() To add actions.
		 * @uses add_filter() To add filters.
		 *
		 * @since 1.0.0
		 */
		public function init_actions() {

			// Bail outside of the WP Admin panel.
			if ( ! is_admin() ) {
				return;
			}

			add_filter( 'http_request_args', array( $this, 'update_check' ), 5, 2 );
			add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins' ) );
			add_filter( 'pre_set_transient_update_plugins', array( $this, 'update_plugins' ) );
			add_filter( 'site_transient_update_plugins', array( $this, 'update_state' ) );
			add_filter( 'transient_update_plugins', array( $this, 'update_state' ) );
			add_action( 'admin_notices', array( $this, 'notice' ) );
			add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'dismiss_notice' ) );
		}

		/**
		 * Check Github for an update.
		 *
		 * @since 1.0.0
		 *
		 * @return false|object
		 */
		public function api_check() {
			$raw_response = wp_remote_get( self::$api_url );
			if ( is_wp_error( $raw_response ) ) {
				return false;
			}

			if ( ! empty( $raw_response['body'] ) ) {
				$raw_body = json_decode( $raw_response['body'], true );
				if ( $raw_body ) {
					return (object) $raw_body;
				}
			}

			return false;
		}

		/**
		 * Disables requests to the wp.org repository for LA-Studio Core.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $request An array of HTTP request arguments.
		 * @param string $url The request URL.
		 * @return array
		 */
		public function update_check( $request, $url ) {

			// Plugin update request.
			if ( false !== strpos( $url, '//api.wordpress.org/plugins/update-check/1.1/' ) ) {

				// Decode JSON so we can manipulate the array.
				$data = json_decode( $request['body']['plugins'] );

				// Remove the Envato Market.
				unset( $data->plugins->{self::$_plugin_file} );

				// Encode back into JSON and update the response.
				$request['body']['plugins'] = wp_json_encode( $data );
			}

			return $request;
		}

		/**
		 * API check.
		 *
		 * @since 1.0.0
		 *
		 * @param bool   $api Always false.
		 * @param string $action The API action being performed.
		 * @param object $args Plugin arguments.
		 * @return mixed $api The plugin info or false.
		 */
		public function plugins_api( $api, $action, $args ) {
			if ( isset( $args->slug ) && self::$_plugin_slug === $args->slug ) {
				$api_check = $this->api_check();
				if ( is_object( $api_check ) ) {
					$api = $api_check;
				}
			}
			return $api;
		}

		/**
		 * Update check.
		 *
		 * @since 1.0.0
		 *
		 * @param object $transient The pre-saved value of the `update_plugins` site transient.
		 * @return object
		 */
		public function update_plugins( $transient ) {
			$state = $this->state();
			if ( 'activated' === $state ) {
				$api_check = $this->api_check();
				if ( is_object( $api_check ) && version_compare( LaStudio_Header_Builder::VERSION, $api_check->version, '<' ) ) {
					$transient->response[self::$_plugin_file] = (object) array(
						'slug'        => self::$_plugin_slug,
						'plugin'      => self::$_plugin_file,
						'new_version' => $api_check->version,
						'url'         => self::$_plugin_url,
						'package'     => $api_check->download_link,
						'tested'      => $api_check->tested ? $api_check->tested : get_bloginfo( 'version' ),
						'icons'       => $api_check->icons ? $api_check->icons : []
					);
				}
			}
			return $transient;
		}

		/**
		 * Set the plugin state.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function state() {
			$option         = self::$_plugin_opt_key;
			$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			// We also have to check network activated plugins. Otherwise this plugin won't update on multisite.
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );
			if ( ! is_array( $active_plugins ) ) {
				$active_plugins = array();
			}
			if ( ! is_array( $active_sitewide_plugins ) ) {
				$active_sitewide_plugins = array();
			}
			$active_plugins = array_merge( $active_plugins, array_keys( $active_sitewide_plugins ) );
			if ( in_array( self::$_plugin_file, $active_plugins ) ) {
				$state = 'activated';
				update_option( $option, $state );
			} else {
				$state = 'install';
				update_option( $option, $state );
				foreach ( array_keys( get_plugins() ) as $plugin ) {
					if ( strpos( $plugin, self::$_plugin_file_php ) !== false ) {
						$state = 'deactivated';
						update_option( $option, $state );
					}
				}
			}
			return $state;
		}

		/**
		 * Force the plugin state to be updated.
		 *
		 * @since 1.0.0
		 *
		 * @param object $transient The saved value of the `update_plugins` site transient.
		 * @return object
		 */
		public function update_state( $transient ) {
			$state = $this->state();
			return $transient;
		}

		/**
		 * Admin notices.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function notice() {
			$screen = get_current_screen();
			$slug   = self::$_plugin_slug;
			$state  = get_option( self::$_plugin_opt_key );
			$notice = get_option( self::AJAX_ACTION );

			if ( empty( $state ) ) {
				$state = $this->state();
			}

			if (
				'activated' === $state ||
				'update-core' === $screen->id ||
				'update' === $screen->id ||
				'plugins' === $screen->id && isset( $_GET['action'] ) && 'delete-selected' === $_GET['action'] ||
				'dismissed' === $notice
				) {
				return;
			}

			if ( 'deactivated' === $state ) {
				$activate_url = add_query_arg(
					array(
						'action'   => 'activate',
						'plugin'   => urlencode( self::$_plugin_file ),
						'_wpnonce' => urlencode( wp_create_nonce( "activate-plugin_" . self::$_plugin_file ) ),
					),
					self_admin_url( 'plugins.php' )
				);

				$message = sprintf(
					esc_html__( '%1$sActivate the %1$3 plugin%2$s', 'lastudio' ),
					'<a href="' . esc_url( $activate_url ) . '">',
					'</a>',
                    self::$_plugin_name
				);
			} elseif ( 'install' === $state ) {
				$install_url = add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => $slug,
					),
					self_admin_url( 'update.php' )
				);

				$message = sprintf(
					esc_html__( '%1$sInstall the %3$s plugin%2$s', 'lastudio' ),
					'<a href="' . esc_url( wp_nonce_url( $install_url, 'install-plugin_' . $slug ) ) . '">',
					'</a>',
                    self::$_plugin_name
				);
			}

			if ( isset( $message ) ) {
				?>
				<div class="updated <?php echo self::$_plugin_slug ?>-plugin-notice notice is-dismissible">
					<p><?php echo wp_kses_post( $message ); ?></p>
				</div>
				<script>
				jQuery( document ).ready( function( $ ) {
					$( document ).on( 'click', '.<?php echo self::$_plugin_slug ?>-plugin-notice .notice-dismiss', function() {
						$.ajax( {
							url: ajaxurl,
							data: {
								action: '<?php echo self::AJAX_ACTION; ?>',
								nonce: '<?php echo wp_create_nonce( self::AJAX_ACTION ); ?>'
							}
						} );
					} );
				} );
				</script>
				<?php
			}
		}

		/**
		 * Dismiss admin notice.
		 *
		 * @since 1.0.0
		 */
		public function dismiss_notice() {
			check_ajax_referer( self::AJAX_ACTION, 'nonce' );

			update_option( self::AJAX_ACTION, 'dismissed' );
			wp_send_json_success();
		}
	}

endif;