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

if ( ! class_exists( 'CFU_Styles_Scripts' ) ) {

	/**
	 * Class to add custom scripts and styles for this plugin
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs
	 */
	class CFU_Styles_Scripts {

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Wbcom Designs
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'cfu_enqueue_public_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'cfu_enqueue_admin_scripts' ) );
			add_action( 'wp_head', array( $this, 'cfu_custom_fonts_enqueue' ) );
		}

		/**
		 * Actions performed to enqueue scripts on public end.
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Wbcom Designs
		 */
		public function cfu_enqueue_public_scripts() {
			$cfu_google_fonts_options = get_option( 'googlefont_file_name', true );
			if ( ! is_array( $cfu_google_fonts_options ) ) {
				$cfu_google_fonts_options = array();
			}

			// Google api url.
			$googleapis_url = 'http://fonts.googleapis.com/css?family=';

			// Check if ssl is activated and switch to https.
			if ( is_ssl() ) {
				$googleapis_url = str_replace( 'http:', 'https:', $googleapis_url );
			}

			// Enquire only the selected fonts.
			if ( isset( $cfu_google_fonts_options ) ) {
				foreach ( $cfu_google_fonts_options as $cfu_google_font_key => $cfu_google_font ) {
					wp_register_style( 'font-style-' . $cfu_google_font_key, $googleapis_url . $cfu_google_font_key );
					wp_enqueue_style( 'font-style-' . $cfu_google_font_key );
				}
			}
		}

		/**
		 * Actions performed to enqueue scripts on admin end.
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Wbcom Designs
		 */
		public function cfu_enqueue_admin_scripts($hook) {			
			$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : '';
			if ( ( ! empty( $tab ) && ! empty( $hook ) ) || 'wb-plugins_page_custom-font-uploader-settings' == $hook || 'google-font-uploader-settings' == $tab  ) {
				wp_enqueue_style( 'cfup-cfup-css', CUSTOM_FONT_UPLOADER_PLUGIN_URL . 'admin/assets/css/cfup.css', array(), CUSTOM_FONT_UPLOADER_VERSION, 'all' );
				wp_enqueue_style( 'cfup-select2css', CUSTOM_FONT_UPLOADER_PLUGIN_URL . 'admin/assets/css/select2.css', array(), CUSTOM_FONT_UPLOADER_VERSION, 'all' );
				wp_enqueue_script( 'cfup-select2js', CUSTOM_FONT_UPLOADER_PLUGIN_URL . 'admin/assets/js/select2.js', array( 'jquery' ), CUSTOM_FONT_UPLOADER_VERSION, false );
				wp_enqueue_script( 'custom-font-uploader-admin', plugins_url( 'admin\assets\js\custom-font-uploader-admin.js', dirname( __FILE__ ) ), array( 'jquery' ), CUSTOM_FONT_UPLOADER_VERSION, false );

				wp_localize_script(
					'custom-font-uploader-admin',
					'cfu_ajax_object',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'ajax-nonce' ),
					)
				);
			}
		}

		/**
		 * Actions performed to enqueue custom added fonts.
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Wbcom Designs
		 */
		public function cfu_custom_fonts_enqueue() {
			$cfu_custom_fonts_options = get_option( 'font_file_name', true );

			if ( ! is_array( $cfu_custom_fonts_options ) ) {
				$cfu_custom_fonts_options = array();
			}

			if ( ! empty( $cfu_custom_fonts_options ) ) {
				$custom_css  = '';
				$custom_css .= '<style type="text/css" id="custom_fonts">';
				foreach ( $cfu_custom_fonts_options as  $custom_fontname => $cfu_custom_font ) {
					$css  = '@font-face {';
					$css .= "\n";
					$css .= '   font-family: ' . $custom_fontname . ';';
					$css .= "\n";
					$css .= '   src: url(' . CUSTOM_FONT_UPLOADER_UPLOADS_DIR_URL . $cfu_custom_font . ');';
					$css .= "\n";
					$css .= '   font-weight: normal;';
					$css .= "\n";
					$css .= '}';

					$custom_css .= $css;
				}
				$custom_css  .= '</style>';
				$allowed_html = array(
					'style' => array(
						'type' => array(),
						'id'   => array(),
					),
				);
				echo wp_kses( $custom_css, $allowed_html );
			}
		}
	}
	new CFU_Styles_Scripts();
}
