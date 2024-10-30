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
$nonce = isset( $_POST['cfu_admin_setting_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['cfu_admin_setting_nonce'] ) ) : '';
if ( wp_verify_nonce( $nonce, 'cfu_admin_nonce' ) ) {

	// Save General Settings.
	if ( isset( $_POST['cfup-general-settings-submit'] ) ) {
		$cfu_general_settings = array();
		// API key.
		if ( isset( $_POST['cfup-apikey'] ) ) {
			$cfu_general_settings['apikey_settings'] = sanitize_text_field( wp_unslash( $_POST['cfup-apikey'] ) );
		}
		update_option( 'cfu_general_settings', $cfu_general_settings );
		$success_msg = "<div class='notice updated is-dismissible' id='message'>";

		$success_msg .= 'Settings Saved.';

		$success_msg .= '<span>' . esc_html__( 'Settings Saved.', 'cfup' ) . '</p>';

		$success_msg .= '</div>';
		echo wp_kses_post( $success_msg );
	}
}
// Retrieve Settings.
$settings = get_option( 'cfu_general_settings', true );
$apikey   = '';
if ( isset( $settings['apikey_settings'] ) ) {
	$apikey = $settings['apikey_settings'];
}
$wp_loader = includes_url( 'images/wpspin.gif' );

$verify_btn_display = 'display: none;';
if ( '' != $apikey ) {
	$verify_btn_display = '';
}
?>
<div class="wrap">
	<div class="wbcom-admin-title-section">
		<h3><?php esc_html_e( 'General Settings', 'cfup' ); ?><h3>
	</div>
	<div class="cfup-general-settings-container wbcom-admin-option-wrap">
		<div class="form-table">
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label for="api-key"><?php esc_html_e( 'Please add Google Font API Key', 'cfup' ); ?></label>
						<span><a href="javascript:void(0);" onClick="window.open('https://developers.google.com/fonts/docs/developer_api','pagename','resizable,height=600,width=700'); return false;">
		<?php esc_html_e( 'Don\'t have it? Get it here!', 'cfup' ); ?>
						</a>, <a href="javascript:void(0);" onClick="window.open('https://drive.google.com/file/d/0B3sZmaJPdRCwRDBVUlJLNUpfQmc/view?usp=sharing','pagename','resizable,height=600,width=700'); return false;">
		<?php esc_html_e( 'Video Tutorial -> Fetch the API Key', 'cfup' ); ?>
						</a></span>
					</div>
					<div class="wbcom-settings-section-options">
						<input required id="cfup-apikey" name="cfup-apikey" type="text" class="regular-text" placeholder="<?php esc_html_e( 'Google API Key', 'cfup' ); ?>" value="<?php echo esc_attr( $apikey ); ?>">
						<!-- <input style="<?php // echo esc_attr( $verify_btn_display ); ?>" type="button" class="button button-secondary" value="<?php // esc_html_e( 'Verify', 'cfup' ); ?>" id="cfup-verify-apikey"> -->
						<img src="<?php echo esc_attr( $wp_loader ); ?>" class="cfup-admin-loader">

						<p class="description"><?php esc_html_e( 'This is the API Key from Google which will help you fetch fonts from Google.', 'cfup' ); ?></p>
					</div>
				</div>
		</div>
		<div class="submit">
			<?php wp_nonce_field( 'cfu_admin_nonce', 'cfu_admin_setting_nonce' ); ?>
			<input type="submit" name="cfup-general-settings-submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'cfup' ); ?>">
		</div>
	</div>
</div>
