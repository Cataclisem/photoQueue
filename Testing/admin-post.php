<?php
/**
 * WordPress Generic Request (POST/GET) Handler
 *
 * Intended for form submission handling in themes and plugins.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** We are located in WordPress Administration Screens */
if ( ! defined( 'WP_ADMIN' ) ) {
	define( 'WP_ADMIN', true );
}

/** Load WordPress Bootstrap */
require_once dirname( __DIR__ ) . '/wp-load.php';

/** Allow for cross-domain requests (from the front end). */
send_origin_headers();

require_once ABSPATH . 'wp-admin/includes/admin.php';

nocache_headers();

/** This action is documented in wp-admin/admin.php */
do_action( 'admin_init' );

/**
 *  Above is code i don't know what do, but its fine
 * 
 */

function testfunc3() {
	echo "testfunc3 <br>";
}

function testfunc4() {
	echo "testfunc4 <br>";
	echo "POST:";
	print_r($_POST);
	echo "<br> REQUEST: ";
	print_r($_REQUEST);
}

function changeTimeFormat($time) {
	$time = "{$time}:00";
	return $time;
}

function addToDB(){
	global $wpdb;
	for ($i = 0; $i < count($_POST["IDclassYear"]); $i++) {
		$IDyearClass = $_POST["IDclassYear"][$i];
		$class = $_POST["class"][$i];
		$gradYear = $_POST["gradYear"][$i];
		$time = changeTimeFormat($_POST["time"][$i]);
		$data = array("IDYearClass"=>$IDyearClass, "class"=>$class, "gradYear"=>$gradYear, "time"=>$time);
		
		if (!$wpdb->insert("Queue", $data)) {
			print_r($wpdb->last_error);
			echo "<br> Something went wrong when intserting the {$IDyearClass} data. Check if the data is correct or is a duplication and try again.";
			exit();
		}
	}
	wp_safe_redirect("https://example.com/redirect"); //Change this to the page you want to redirect to after the form is submitted
}


//add_action( 'admin_post_post_to_DB', 'testfunc3' );
add_action( 'admin_post_nopriv_post_to_DB', 'addToDB' );


/** 
 * Below is code i don't know what do, but its fine
 * Everything under checks paramenters for request stuff 
 * */

$action = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';

// Reject invalid parameters.
if ( ! is_scalar( $action ) ) {
	wp_die( '', 400 );
}

if ( ! is_user_logged_in() ) {
	if ( empty( $action ) ) {
		echo "action: admin_post_nopriv <br>";
		/**
		 * Fires on a non-authenticated admin post request where no action is supplied.
		 *
		 * @since 2.6.0
		 */
		do_action( 'admin_post_nopriv' );
	} else {
		//echo "action: admin_post_nopriv_{$action} <br>";
		// If no action is registered, return a Bad Request response.
		if ( ! has_action( "admin_post_nopriv_{$action}" ) ) {
			wp_die( '', 400 );
		}

		/**
		 * Fires on a non-authenticated admin post request for the given action.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the given
		 * request action.
		 *
		 * @since 2.6.0
		 */
		do_action( "admin_post_nopriv_{$action}" );
	}
} else {
	if ( empty( $action ) ) {
		echo "action: admin_post <br>";
		/**
		 * Fires on an authenticated admin post request where no action is supplied.
		 *
		 * @since 2.6.0
		 */
		do_action( 'admin_post' );
	} else {
		echo "action: admin_post_{$action} <br>";
		// If no action is registered, return a Bad Request response.
		if ( ! has_action( "admin_post_{$action}" ) ) {
			wp_die( '', 400 );
		}

		/**
		 * Fires on an authenticated admin post request for the given action.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the given
		 * request action.
		 *
		 * @since 2.6.0
		 */
		do_action( "admin_post_{$action}" );
	}
}

