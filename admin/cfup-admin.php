<?php
/**
 * Exit if accessed directly.
 *
 * @package custom-font-uploader
 * @version 1.0.0
 * @author  wbcomdesigns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CFU_AdminPage' ) ) {

	/**
	 * Add admin page for displaying buddypress fitness settings.
	 *
	 * @package custom-font-uploader
	 * @version 1.0.0
	 * @author  wbcomdesigns
	 */
	class CFU_AdminPage {

		/**
		 * The Slug of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_slug    The Slug of this plugin.
		 */
		private $plugin_slug = 'custom-font-uploader-settings',

		/**
		 * Plugin setting tabs.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      array    $plugin_slug    Plugin setting tabs.
		 */
		$plugin_settings_tabs = array();

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'cfu_add_menu_page' ) );
			add_action( 'admin_init', array( $this, 'cfu_register_general_settings' ) );
			add_action( 'admin_init', array( $this, 'cfu_register_custom_font_settings' ) );
			add_action( 'admin_init', array( $this, 'cfu_register_google_font_settings' ) );
			add_action( 'admin_init', array( $this, 'cfu_register_support_settings' ) );
		}

		/**
		 * Actions performed to create a custom menu on loading admin_menu.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_add_menu_page() {
			// add_menu_page(__('Custom Font Uploader', 'cfup'), __('Font Uploader', 'cfup'), 'manage_options', $this->plugin_slug, array( $this, 'cfu_admin_settings_page' ), 'dashicons-editor-textcolor', 4);
			if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
				add_menu_page( esc_html__( 'WB Plugins', 'cfup' ), esc_html__( 'WB Plugins', 'cfup' ), 'manage_options', 'wbcomplugins', array( $this, 'cfu_admin_settings_page' ), 'dashicons-lightbulb', 59 );
				add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'cfup' ), esc_html__( 'General', 'cfup' ), 'manage_options', 'wbcomplugins' );
			}
			add_submenu_page( 'wbcomplugins', esc_html__( 'Custom Font Uploader Settings Page', 'cfup' ), esc_html__( 'Font Uploader', 'cfup' ), 'manage_options', 'custom-font-uploader-settings', array( $this, 'cfu_admin_settings_page' ) );
		}

		/**
		 * Actions performed to create a custom setting page.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_admin_settings_page() {
			$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'cfup-welcome';
			?>
			<div class="wrap">			
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
					<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/?utm_source=pluginoffernotice&utm_medium=community_banner" target="_blank">
						<img src="<?php echo esc_url( CUSTOM_FONT_UPLOADER_PLUGIN_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
					</a>
				</div>
			</div>
			<div class="wbcom-wrap wbcom-plugin-wrapper">
			<div class="bffs-header">
				<div class="wbcom_admin_header-wrapper">
					<div id="wb_admin_plugin_name">
						<?php esc_html_e( 'Custom Font Uploader', 'cfup' ); ?>
						<?php /* translators: %s: */ ?>
						<span><?php printf( esc_html__( 'Version %s', 'cfup' ), esc_html__( CUSTOM_FONT_UPLOADER_VERSION ) ); ?></span>
					</div>
					<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				</div>
			</div>
					<div class="wbcom-admin-settings-page">
							<?php $this->cfu_plugin_settings_tabs(); ?>
						<div class="wbcom-tab-content">
							<div class="wbcom-cfup-notice"><?php esc_html_e( 'This plugin lets you upload your own font files and apply them to any element of your website.', 'cfup' ); ?></div>
							<form action="" method="POST" id="<?php echo esc_attr( $tab ); ?>-settings-form" enctype="multipart/form-data">
							<?php do_settings_sections( $tab ); ?>
							</form>
						</div>
					</div>
					</div>
			</div>
			<?php
		}

		/**
		 * Actions performed to create a custom setting tab.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_plugin_settings_tabs() {
			$current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'cfup-welcome';
			echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<li class="' . esc_attr( $tab_key ) . '"><a class="nav-tab ' . esc_attr( $active ) . '" href="?page=' . esc_attr( $this->plugin_slug ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption ) . '</a></li>';
			}
			echo '</div></ul></div>';
		}

		/**
		 * Actions performed to create a general setting tab.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_register_general_settings() {
			$this->plugin_settings_tabs['cfup-welcome'] = esc_html__( 'Welcome', 'cfup' );
			add_settings_section( 'cfu_welcome_settings', ' ', array( &$this, 'cfu_welcome_page_content' ), 'cfup-welcome' );

			$this->plugin_settings_tabs['cfup-general-settings'] = __( 'General', 'cfup' );
			register_setting( 'cfup-general-settings', 'cfup-general-settings' );
			add_settings_section( 'section_general', ' ', array( &$this, 'cfu_general_settings_section' ), 'cfup-general-settings' );
		}

		/**
		 * Added welcome tab content.
		 */
		public function cfu_welcome_page_content() {
			if ( file_exists( dirname( __FILE__ ) . '/cfup-welcome-page.php' ) ) {
				include_once dirname( __FILE__ ) . '/cfup-welcome-page.php';
			}
		}

		/**
		 * Actions performed to create a general setting content.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_general_settings_section() {
			if ( file_exists( dirname( __FILE__ ) . '/cfup-general-settings.php' ) ) {
				include_once dirname( __FILE__ ) . '/cfup-general-settings.php';
			}
		}

		/**
		 * Actions performed to create a custom font setting tab.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_register_custom_font_settings() {
			$this->plugin_settings_tabs['custom-font-uploader-settings'] = __( 'Upload Fonts', 'cfup' );
			register_setting( 'custom-font-uploader-settings', 'custom-font-uploader-settings' );
			add_settings_section( 'section_custom_font', ' ', array( &$this, 'cfu_custom_fonts_section' ), 'custom-font-uploader-settings' );
		}

		/**
		 * Actions performed to create a custom font setting content.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_custom_fonts_section() {
			if ( file_exists( dirname( __FILE__ ) . '/cfup-customfont-settings.php' ) ) {
				include_once dirname( __FILE__ ) . '/cfup-customfont-settings.php';
			}
		}

		/**
		 * Actions performed to create a google font setting tab.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_register_google_font_settings() {
			$this->plugin_settings_tabs['google-font-uploader-settings'] = __( 'Google Fonts', 'cfup' );
			register_setting( 'google-font-uploader-settings', 'google-font-uploader-settings' );
			add_settings_section( 'section_google_font', ' ', array( &$this, 'cfu_google_fonts_section' ), 'google-font-uploader-settings' );
		}

		/**
		 * Actions performed to create a google font setting content.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_google_fonts_section() {
			if ( file_exists( dirname( __FILE__ ) . '/cfup-googlefont-settings.php' ) ) {
				include_once dirname( __FILE__ ) . '/cfup-googlefont-settings.php';
			}
		}

		/**
		 * Actions performed to create a support setting tab.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_register_support_settings() {
			$this->plugin_settings_tabs['cfup-support'] = __( 'Support', 'cfup' );
			register_setting( 'cfup-support', 'cfup-support' );
			add_settings_section( 'section_support', ' ', array( &$this, 'cfu_section_support' ), 'cfup-support' );
		}

		/**
		 * Actions performed to create a support section.
		 *
		 * @version 1.0.0
		 * @author  wbcomdesigns
		 */
		public function cfu_section_support() {
			if ( file_exists( dirname( __FILE__ ) . '/cfup-support.php' ) ) {
				include_once dirname( __FILE__ ) . '/cfup-support.php';
			}
		}
	}
	new CFU_AdminPage();
}
