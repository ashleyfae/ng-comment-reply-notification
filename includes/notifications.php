<?php
/**
 * Notifications
 *
 * Sends email notifications when a comment gets a reply.
 *
 * @package   ng-comment-reply-notification
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends the email notification for a comment.
 *
 * @param int|WP_Comment $comment        Comment object or ID that was just submitted. This is the new reply.
 * @param int|WP_Comment $comment_parent Parent comment object or ID. We are emailing the author of this comment.
 *
 * @since 1.0
 * @return bool
 */
function ng_crn_send_notification( $comment, $comment_parent ) {

	if ( ! is_object( $comment ) ) {
		$comment = get_comment( $comment );
	}

	if ( ! is_object( $comment_parent ) ) {
		$comment_parent = get_comment( $comment_parent );
	}

	// Unexpected results - bail.
	if ( ! is_a( $comment, 'WP_Comment' ) || ! is_a( $comment_parent, 'WP_Comment' ) ) {
		return false;
	}

	$recipient        = $comment_parent->comment_author_email;
	$original_subject = ng_crn_get_option( 'subject' );
	$original_message = ng_crn_get_option( 'message' );

	// Bail if subject or message is empty.
	if ( empty( $original_subject ) || empty( $original_message ) ) {
		return false;
	}

	/**
	 * Process comment to replace placeholders with the real deal.
	 */
	$find = array(
		'%post_title%',
		'%post_url%',
		'%original_comment_url%',
		'%reply_comment_url%',
		'%original_comment_content%',
		'%reply_comment_content%',
		'%original_comment_author%',
		'%reply_comment_author%',
	);

	$replace = array(
		esc_html( get_the_title( $comment_parent->comment_post_ID ) ),
		esc_url( get_permalink( $comment_parent->comment_post_ID ) ),
		esc_url( get_comment_link( $comment_parent ) ),
		esc_url( get_comment_link( $comment ) ),
		$comment_parent->comment_content,
		$comment->comment_content,
		esc_html( $comment_parent->comment_author ),
		esc_html( $comment->comment_author )
	);

	$compiled_subject = str_replace( $find, $replace, $original_subject );
	$compiled_message = str_replace( $find, $replace, $original_message );

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	return wp_mail( $recipient, $compiled_subject, $compiled_message, apply_filters( 'ng-comment-reply-notification/email-headers', $headers, $comment, $comment_parent ) );

}

/**
 * Maybe send notification when a comment is inserted.
 *
 * @uses  ng_crn_send_notification()
 *
 * @param int        $comment_id ID of the comment that was just inserted.
 * @param WP_Comment $comment    Comment object.
 *
 * @since 1.0
 * @return void
 */
function ng_crn_send_email_on_insert( $comment_id, $comment ) {

	// This comment is not approved or it's not a reply to a parent comment -- bail.
	if ( 1 != $comment->comment_approved || $comment->comment_parent < 1 ) {
		return;
	}

	$comment_parent = get_comment( $comment->comment_parent );

	// If someone is replying to themselves, don't send an email.
	if ( $comment_parent->comment_author_email == $comment->comment_author_email ) {
		return;
	}

	ng_crn_send_notification( $comment, $comment_parent );

}

add_action( 'wp_insert_comment', 'ng_crn_send_email_on_insert', 99, 2 );

/**
 * Maybe send notification when a comment's status is changed to "approved".
 *
 * @uses  ng_crn_send_email_on_insert()
 *
 * @param int    $comment_id     ID of the comment that just had its status changed.
 * @param string $comment_status New comment status.
 *
 * @since 1.0
 * @return void
 */
function ng_crn_send_email_on_status_change( $comment_id, $comment_status ) {

	$comment = get_comment( $comment_id );

	if ( 'approve' == $comment_status ) {
		ng_crn_send_email_on_insert( $comment_id, $comment );
	}

}

add_action( 'wp_set_comment_status', 'ng_crn_send_email_on_status_change', 99, 2 );