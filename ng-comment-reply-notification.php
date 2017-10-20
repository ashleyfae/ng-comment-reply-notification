<?php
/**
 * Plugin Name: NG Comment Reply Notification
 * Plugin URI: https://www.nosegraze.com
 * Description: Sends an email to commenters when you reply to their comment.
 * Version: 1.0
 * Author: Ashley Gibson
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 *
 * @package   ng-comment-reply-notification
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 *
 * Forked/rewritten from Comment Reply Notification by denishua.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NG_Comment_Reply_Notification' ) ) :

	class NG_Comment_Reply_Notification {

		/**
		 * NG_Comment_Reply_Notification object.
		 *
		 * @var NG_Comment_Reply_Notification Instance of the NG_Comment_Reply_Notification class.
		 * @access private
		 * @since  1.0
		 */
		private static $instance;

		/**
		 * NG_Comment_Reply_Notification instance.
		 *
		 * Insures that only one instance of NG_Comment_Reply_Notification exists at any one time.
		 *
		 * @uses   NG_Comment_Reply_Notification::setup_constants() Set up the plugin constants.
		 * @uses   NG_Comment_Reply_Notification::includes() Include any required files.
		 * @uses   NG_Comment_Reply_Notification::load_textdomain() Load the language files.
		 *
		 * @access public
		 * @since  1.0
		 * @return NG_Comment_Reply_Notification Instance of NG_Comment_Reply_Notification class
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! self::$instance instanceof NG_Comment_Reply_Notification ) {
				self::$instance = new NG_Comment_Reply_Notification;
				self::$instance->setup_constants();

				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

				self::$instance->includes();
			}

			return self::$instance;

		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since  1.0
		 * @return void
		 */
		private function setup_constants() {

			if ( ! defined( 'NG_CRN_VERSION' ) ) {
				define( 'NG_CRN_VERSION', '1.0' );
			}
			if ( ! defined( 'NG_CRN_DIR' ) ) {
				define( 'NG_CRN_DIR', plugin_dir_path( __FILE__ ) );
			}
			if ( ! defined( 'NG_CRN_URL' ) ) {
				define( 'NG_CRN_URL', plugin_dir_url( __FILE__ ) );
			}
			if ( ! defined( 'NG_CRN_FILE' ) ) {
				define( 'NG_CRN_FILE', __FILE__ );
			}

		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since  1.0
		 * @return void
		 */
		private function includes() {

			require_once NG_CRN_DIR . 'includes/notifications.php';
			require_once NG_CRN_DIR . 'includes/settings.php';

		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since  1.0
		 * @return void
		 */
		public function load_textdomain() {

			$lang_dir = dirname( plugin_basename( NG_CRN_FILE ) ) . '/languages/';
			$lang_dir = apply_filters( 'ng-comment-reply-notification/languages-directory', $lang_dir );
			load_plugin_textdomain( 'ng-comment-reply-notification', false, $lang_dir );

		}

	}

endif;

/**
 * Returns the main instance of NG_Comment_Reply_Notification.
 *
 * @since 1.0
 * @return NG_Comment_Reply_Notification
 */
function ng_comment_reply_notification() {
	return NG_Comment_Reply_Notification::instance();
}

ng_comment_reply_notification();