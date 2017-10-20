<?php
/**
 * Settings
 *
 * Register and display admin settings.
 *
 * @package   ng-comment-reply-notification
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register settings page.
 *
 * @since 1.0
 * @return void
 */
function ng_crn_register_admin_page() {

	add_options_page( __( 'Comment Reply Notification', 'ng-comment-reply-notification' ), __( 'Comment Reply Notification', 'ng-comment-reply-notification' ), 'manage_options', 'ng-comment-reply-notification', 'ng_crn_display_settings' );

}

add_action( 'admin_menu', 'ng_crn_register_admin_page' );

/**
 * Register plugin settings.
 *
 * @see   ng_crn_sanitize_settings()
 *
 * @since 1.0
 * @return void
 */
function ng_crn_register_settings() {
	register_setting( 'ng_crn_settings', 'ng_crn_settings', 'ng_crn_sanitize_settings' );
}

add_action( 'admin_init', 'ng_crn_register_settings' );

/**
 * Sanitize plugin settings before they are saved.
 *
 * @param array $data
 *
 * @since 1.0
 * @return array Sanitized settings.
 */
function ng_crn_sanitize_settings( $data ) {

	$sanitized_settings = array(
		'subject' => '',
		'message' => ''
	);

	if ( ! empty( $data['subject'] ) ) {
		$sanitized_settings['subject'] = sanitize_text_field( $data['subject'] );
	}

	if ( ! empty( $data['message'] ) ) {
		$sanitized_settings['message'] = wp_kses( $data['message'], wp_kses_allowed_html() );
	}

	return $sanitized_settings;

}

/**
 * Get the saved value of a setting. If not set, the default value is used.
 *
 * @see   ng_crn_get_default()
 *
 * @param string $key Setting to retrieve the value of.
 *
 * @since 1.0
 * @return string
 */
function ng_crn_get_option( $key ) {

	$settings = get_option( 'ng_crn_settings', array() );
	$default  = ng_crn_get_default( $key );

	if ( ! is_array( $settings ) || ! array_key_exists( $key, $settings ) ) {
		return $default;
	}

	return $settings[ $key ];

}

/**
 * Get the default value for a setting.
 *
 * @param string $key Setting to retrieve the default value of.
 *
 * @since 1.0
 * @return string
 */
function ng_crn_get_default( $key ) {

	switch ( $key ) {

		case 'subject' :
			$default = sprintf( __( 'There\'s a reply to your comment on %s', 'ng-comment-reply-notification' ), wp_strip_all_tags( get_option( 'blogname' ) ) );
			break;

		case 'message' :
			$default = sprintf(
				__( "There's a new reply to your comment over on %s. As a reminder, here's your original comment on the post <a href=\"%%post_url%%\">%%post_title%%</a>:\n\n<blockquote>%%original_comment_content%%</blockquote>\n\nAnd here's the new reply from %%reply_comment_author%%:\n\n<blockquote>%%reply_comment_content%%</blockquote>\n\nYou can respond to the comment here: <a href=\"%%reply_comment_url%%\">%%reply_comment_url%%</a> \n\nLet's keep the conversation going!", 'ng-comment-reply-notification' ),
				wp_strip_all_tags( get_option( 'blogname' ) )
			);
			break;

		default :
			$default = '';
			break;

	}

	return $default;

}

/**
 * Render settings page.
 *
 * @since 1.0
 * @return void
 */
function ng_crn_display_settings() {

	?>
	<div class="wrap">
		<h1><?php _e( 'NG Comment Reply Notification', 'ng-comment-reply-notification' ); ?></h1>

		<form method="POST" action="options.php">
			<?php settings_fields( 'ng_crn_settings' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th>
						<label for="ng_crn_settings_subject"><?php _e( 'Email Subject', 'ng-comment-reply-notification' ); ?></label>
					</th>
					<td>
						<input type="text" id="ng_crn_settings_subject" class="regular-text" name="ng_crn_settings[subject]" value="<?php echo esc_attr( ng_crn_get_option( 'subject' ) ); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th>
						<label for="ng_crn_settings_message"><?php _e( 'Email Message', 'ng-comment-reply-notification' ); ?></label>
					</th>
					<td>
						<textarea id="ng_crn_settings_message" class="large-text code" name="ng_crn_settings[message]" rows="10"><?php echo esc_textarea( ng_crn_get_option( 'message' ) ); ?></textarea>
						<p><?php _e( 'HTML and the following placeholders may be used in the message:' ); ?></p>
						<ul>
							<li>
								<?php printf( __( '%s - Title of the blog post the comment was made on.', 'ng-comment-reply-notification' ), '<code>%post_title%</code>' ); ?>
							</li>
							<li>
								<?php printf( __( '%s - URL to the blog post the comment was made on.', 'ng-comment-reply-notification' ), '<code>%post_url%</code>' ); ?>
							</li>
							<li>
								<?php printf( __( '%s - URL to the original comment.', 'ng-comment-reply-notification' ), '<code>%original_comment_url%</code>' ); ?>
							</li>
							<li>
								<?php printf( __( '%s - URL to the new reply comment.', 'ng-comment-reply-notification' ), '<code>%reply_comment_url%</code>' ); ?>
							</li>
							<li>
								<?php printf( __( '%s - Content of the original comment.', 'ng-comment-reply-notification' ), '<code>%original_comment_content%</code>' ); ?>
							</li>
							<li>
								<?php printf( __( '%s - Content of the new reply comment.', 'ng-comment-reply-notification' ), '<code>%reply_comment_content%</code>' ); ?>
							</li>
							<li>
								<?php printf( __( '%s - Name of the original comment author.', 'ng-comment-reply-notification' ), '<code>%original_comment_author%</code>' ); ?>
							</li>
							<li>
								<?php printf( __( '%s - Name of the new reply comment author.', 'ng-comment-reply-notification' ), '<code>%reply_comment_author%</code>' ); ?>
							</li>
						</ul>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php

}