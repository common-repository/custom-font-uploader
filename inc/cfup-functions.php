<?php
/**
 * Exit if accessed directly.
 *
 * @package custom-font-uploader
 * @version 1.0.0
 * @author  wbcomdesigns
 */

add_action( 'wp_ajax_delete_customfont', 'cfu_delete_customfont' );
add_action( 'wp_ajax_nopriv_delete_customfont', 'cfu_delete_customfont' );

if (!function_exists('cfu_delete_customfont')) {
	/**
	 * Function for deleting custom fonts using the upload method.
	 *
	 * @version 1.0.0
	 * @package WbcomDesigns
	 */
	function cfu_delete_customfont()
	{
		// Check and sanitize nonce for security
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!wp_verify_nonce($nonce, 'ajax-nonce')) {
			wp_send_json_error('Invalid nonce');
			return;
		}

		// Check if the current user is a site administrator (super admin) or an administrator
		if (!current_user_can('administrator') && !is_super_admin()) {
			wp_send_json_error('Only administrators can delete fonts');
			return;
		}

		// Get the fonts data from the database
		$fonts_db_data = get_option('font_file_name', true);
		if (!is_array($fonts_db_data)) {
			wp_send_json_error('No fonts data found');
			return;
		}

		// Get the key of the font to be deleted and sanitize it
		$delckey = isset($_POST['del_key']) ? sanitize_text_field(wp_unslash($_POST['del_key'])) : '';
		if (empty($delckey) || !isset($fonts_db_data[$delckey])) {
			wp_send_json_error('Font key not found');
			return;
		}

		// Get the path of the custom font file
		$custom_font_file = CUSTOM_FONT_UPLOADER_UPLOADS_DIR_PATH . $fonts_db_data[$delckey];

		// Check if the font file exists before attempting to delete it
		if (file_exists(realpath($custom_font_file))) {
			unlink(realpath($custom_font_file));
		} else {
			wp_send_json_error('Font file does not exist');
			return;
		}

		// Remove the font from the database
		unset($fonts_db_data[$delckey]);
		update_option('font_file_name', $fonts_db_data);

		// Send success response
		wp_send_json_success('Custom font deleted successfully');
	}
}

add_action( 'wp_ajax_delete_googlefont', 'cfu_delete_googlefont' );
add_action( 'wp_ajax_nopriv_delete_googlefont', 'cfu_delete_googlefont' );

if (!function_exists('cfu_delete_googlefont')) {
	/**
	 * Function for deleting Google Fonts.
	 *
	 * @version 1.0.0
	 * @package WbcomDesigns
	 */
	function cfu_delete_googlefont()
	{
		// Check and sanitize nonce for security
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!wp_verify_nonce($nonce, 'ajax-nonce')) {
			wp_send_json_error('Invalid nonce');
			return;
		}

		// Check if the current user is an administrator or site administrator (super admin)
		if (!current_user_can('administrator') && !is_super_admin()) {
			wp_send_json_error('Only administrators can delete Google Fonts');
			return;
		}

		// Get the Google Fonts data from the database
		$gfonts_db_data = get_option('googlefont_file_name', true);
		if (!is_array($gfonts_db_data)) {
			wp_send_json_error('No Google Fonts data found');
			return;
		}

		// Get the key of the Google Font to be deleted and sanitize it
		$del_gkey = isset($_POST['del_gkey']) ? sanitize_text_field(wp_unslash($_POST['del_gkey'])) : '';
		if ( empty($del_gkey) || !isset($gfonts_db_data[$del_gkey])) {
			wp_send_json_error('Google Font key not found');
			return;
		}

		// Remove the Google Font from the database
		unset($gfonts_db_data[$del_gkey]);
		update_option('googlefont_file_name', $gfonts_db_data);

		// Send success response
		wp_send_json_success('Google Font deleted successfully');
	}
}



if (!function_exists('cfu_get_google_fonts')) {
	/**
	 * Get Google Fonts through Google API and pass it using cURL.
	 *
	 * @version 1.0.0
	 * @package WbcomDesigns
	 * @param   string $api_key API key for Google Fonts.
	 * @return  array|WP_Error  Array of fonts or WP_Error on failure.
	 */
	function cfu_get_google_fonts($api_key)
	{
		// Define the API URL
		$api_url = 'https://www.googleapis.com/webfonts/v1/webfonts';

		// Build the URL with the API key
		$url = add_query_arg(array('key' => $api_key), esc_url_raw($api_url));

		// Make the request to the Google Fonts API
		$response = wp_remote_get( esc_url_raw( $url ) );

		// Check if the request resulted in an error
		if (is_wp_error($response)) {
			return new WP_Error('request_failed', 'Request to Google Fonts API failed');
		}

		// Get the response code and message
		$response_code = wp_remote_retrieve_response_code($response);
		$response_message = wp_remote_retrieve_response_message($response);

		// Handle different response codes
		if (200 !== $response_code) {
			if (!empty($response_message)) {
				return new WP_Error($response_code, $response_message);
			} else {
				return new WP_Error($response_code, 'Unknown error occurred');
			}
		}

		// Everything seems OK, retrieve the fonts
		$body = wp_remote_retrieve_body($response);
		$fonts = json_decode($body, true);

		// Check if the decoding was successful
		if (json_last_error() !== JSON_ERROR_NONE) {
			return new WP_Error('json_decode_error', 'Failed to decode JSON response');
		}

		return $fonts;
	}
}

if ( ! function_exists( 'cfu_elementor_group' ) ) {
	/**
	 * Add CFU Custom font group in elementor font group
	 *
	 * @param string $font_groups Fonts Group.
	 */
	function cfu_elementor_group( $font_groups ) {
		$new_group['cfu-custom-fonts'] = __( 'CFU Custom', 'cfup' );
		$font_groups                   = $new_group + $font_groups;
		return $font_groups;
	}
}
add_filter( 'elementor/fonts/groups', 'cfu_elementor_group', 20 );

if ( ! function_exists( 'cfu_elementor_additional_fonts' ) ) {
	/**
	 * Add CFU Custom font lists in elementor fonts
	 *
	 * @param array $additional_fonts Extra Fonts.
	 */
	function cfu_elementor_additional_fonts( $additional_fonts ) {
		$custom_fonts = get_option( 'font_file_name', true );
		if ( ! is_array( $custom_fonts ) ) {
			$custom_fonts = array();
		}
		if ( ! empty( $custom_fonts ) ) {
			foreach ( $custom_fonts as $key => $value ) {
				$additional_fonts[ $key ] = 'cfu-custom-fonts';
			}
		}
		return $additional_fonts;
	}
}
add_filter( 'elementor/fonts/additional_fonts', 'cfu_elementor_additional_fonts', 20 );

if ( ! function_exists( 'cfu_bb_custom_fonts' ) ) {
	/**
	 * Beaver builder theme customizer, beaver buidler page builder.
	 *
	 * @param  array $bb_fonts BuddyBoss Fonts.
	 */
	function cfu_bb_custom_fonts( $bb_fonts ) {

		$fonts = get_option( 'font_file_name', true );
		if ( ! is_array( $fonts ) ) {
			$fonts = array();
		}

		$custom_fonts = array();
		if ( ! empty( $fonts ) ) {
			foreach ( $fonts as $font_family_name => $fonts_url ) {
				$custom_fonts[ $font_family_name ] = array(
					'fallback' => 'Verdana, Arial, sans-serif',
					'weights'  => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
				);
			}
		}

		return array_merge( $bb_fonts, $custom_fonts );
	}
}
add_filter( 'fl_theme_system_fonts', 'cfu_bb_custom_fonts' );
add_filter( 'fl_builder_font_families_system', 'cfu_bb_custom_fonts' );

if ( ! function_exists( 'cfu_add_customizer_font_list' ) ) {
	/**
	 * Added kirki fonts.
	 *
	 * @param  string $value Fonts value.
	 */
	function cfu_add_customizer_font_list( $value ) {

		$fonts = get_option( 'font_file_name', true );
		if ( ! is_array( $fonts ) ) {
			$fonts = array();
		}

		echo '<optgroup label="' . esc_attr( 'CFU Custom' ) . '">';

		foreach ( $fonts as $font => $links ) {
			echo '<option value="' . esc_attr( $font ) . '" ' . selected( $font, $value, false ) . '>' . esc_attr( $font ) . '</option>';
		}
	}
}
add_action( 'astra_customizer_font_list', 'cfu_add_customizer_font_list' );

if ( ! function_exists( 'cfu_kirki_fonts_all' ) ) {
	/**
	 * Added kirki fonts.
	 *
	 * @param  array $kirki_fonts Kirki fonts.
	 */
	function cfu_kirki_fonts_all( $kirki_fonts ) {
		$fonts = get_option( 'font_file_name', true );
		if ( ! is_array( $fonts ) ) {
			$fonts = array();
		}

		if ( ! empty( $fonts ) ) {
			foreach ( $fonts as $font_family_name => $fonts_url ) {
				$kirki_fonts[ $font_family_name ] = array(
					'label' => $font_family_name,
					'stack' => $font_family_name . ', Verdana, Arial, sans-serif',
				);
			}
		}
		return $kirki_fonts;
	}
}
add_filter( 'kirki_fonts_standard_fonts', 'cfu_kirki_fonts_all' );

add_action( 'admin_init', 'cfu_hide_all_admin_notices_from_setting_page' );
if ( ! function_exists( 'cfu_hide_all_admin_notices_from_setting_page' ) ) {
	/**
	 * Hide all notices from the setting page.
	 *
	 * @return void
	 */
	function cfu_hide_all_admin_notices_from_setting_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'custom-font-uploader-settings', 'wbcom-license-page' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';
		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}
}
