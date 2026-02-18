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


/**
 *  Change the time format to HH:MM:SS.
 *  @param string $time The time to change.
 *  @return string The changed time.
 */
function changeTimeFormat($time) {
	$time = "{$time}:00";
	return $time;
}


/**
 *  Filter the time to ensure it is valid. By using "FILTER_VALIDATE_REGEXP" to check that it is in the format of HH:MM and that the hours are between 00 and 23 and the minutes are between 00 and 59.
 *  @param string $time The time to filter.
 *  @return string The filtered time.
 */
function filterTime($time) {
    $time = filter_var($time, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-2][0-9]:[0-5][0-9]$/")));
    return $time;
}


/**
 *  Filter the graduation year to ensure it is valid. By validating that it is an integer and has 4 digits.
 *  @param string $gradYear The graduation year to filter.
 *  @return string The filtered graduation year.
 */
function filterGradYear($gradYear) {
    if (!(filter_var($gradYear, FILTER_VALIDATE_INT) and count(str_split($gradYear)) == 4)) {
        throw new Exception("{$gradYear} is an Invalid grad year");
    }else {
    return $gradYear;
    }
}


/**
 *  Filter the class name to ensure it is valid.
 *  @param string $class The class name to filter.
 *  @return string The filtered class name.
 */
function filterClass($class) {
    if (!filter_var($class, FILTER_SANITIZE_SPECIAL_CHARS)) {
        throw new Exception("{$class} is an Invalid class");
    } else {
    return $class;
    }
}

/**
 *  Add the submitted classes to the database. Through an add_action hook, this function is called when the form is submitted. <br>
 *  It goes through each class that was submitted in the form and adds it to the database. After the form is submitted, it will redirect to page of your choosing.
 *  @return void
 */
function addToDB(){
	global $wpdb; //This is the global variable that allows us to interact with the database in WordPress
	for ($i = 0; $i < count($_POST["class"]); $i++) { //This loop goes through each class that was submitted in the form and adds it to the database
		try {
        	$class = filterClass($_POST["class"][$i]);
        	$gradYear = filterGradYear($_POST["gradYear"][$i]);
        	$time = changeTimeFormat(filterTime($_POST["time"][$i]));
			$data = array("class"=>$class, "gradYear"=>$gradYear, "time"=>$time);
			if (!$wpdb->insert("Queue", $data)) {
				print_r($wpdb->last_error);
				echo "<br> Something went wrong when intserting the class {$gradYear} {$class} with time {$time} data. Check if the data is correct or is a duplication and try again.";
			}
			wp_safe_redirect(home_url()); //Change this to the page you want to redirect to after the form is submitted
			exit();

		} catch (Exception $e) {
		echo $e->getMessage();
		}
	}
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
		/**
		 * Fires on a non-authenticated admin post request where no action is supplied.
		 *
		 * @since 2.6.0
		 */
		do_action( 'admin_post_nopriv' );
	} else {
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
		/**
		 * Fires on an authenticated admin post request where no action is supplied.
		 *
		 * @since 2.6.0
		 */
		do_action( 'admin_post' );
	} else {
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

