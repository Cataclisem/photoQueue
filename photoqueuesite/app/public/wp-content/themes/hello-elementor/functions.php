<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.4.6' );
define( 'EHP_THEME_SLUG', 'hello-elementor' );

define( 'HELLO_THEME_PATH', get_template_directory() );
define( 'HELLO_THEME_URL', get_template_directory_uri() );
define( 'HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/' );
define( 'HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/' );
define( 'HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/' );
define( 'HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/' );
define( 'HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/' );
define( 'HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/' );
define( 'HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/' );
define( 'HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'assets/css/editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				HELLO_THEME_STYLE_URL . 'reset.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				HELLO_THEME_STYLE_URL . 'theme.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				HELLO_THEME_STYLE_URL . 'header-footer.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

require HELLO_THEME_PATH . '/theme.php';

HelloTheme\Theme::instance();

//------------------------------- Before is Elementor stuff --------

// ------------------------ Removing to Database Functions ------------------------

add_action( 'admin_post_edit_database', 'checkForAddTimeOrDeleteEntry' );
add_action('admin_post_nopriv_edit_database', 'checkForAddTimeOrDeleteEntry');

/**
 * Check which action the form is calling.
 * @param void.
 * @return void.
 */
function checkForAddTimeOrDeleteEntry() {
	try {
		if (isset($_POST["delete"])) {
			deleteFromDB();
		} else if (isset($_POST["addTime"])) {
			addTimeToSelected();
		}
		wp_safe_redirect('http://photoqueuesite.local/queueadmin/'); //Change this to the page you want to redirect to after the form is submitted
		exit();
	} catch (Exception $e) {
		print_r($e);
		echo $e->getMessage();
		exception_return();
		exit();
	}
}

/**
 *  Delete the selected classes from the database.
 *  @param object $conn The database connection.
 *  @return void
 */
function deleteFromDB() {
	global $wpdb;
	$checkedBoxes = getArrayOfCheckedBoxes();
	foreach ($checkedBoxes as $time) {
		$sqlQuery = "DELETE FROM Queue WHERE time = '{$time}'";;
		if (!filter_var($time, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^-?[0-2][0-9]:[0-5][0-9]:00$/")))) {
			throw new Exception("{$time} is an Invalid time. Something went wrong when trying to delete the class with time {$time} from the database. Check if the time is correct and try again.");
		}
		if (!$wpdb->query($sqlQuery)) {
			throw new Exception(print_r($wpdb->last_error));
		}
	}
}

/**
 *  Add time to the selected classes in the database.
 *  @param object $conn The database connection.
 *  @return void
 */
function addTimeToSelected() {
	global $wpdb;
	$addedTime = filterExtraTimeInput($_POST["addedTime"]);
	$checkedBoxes = getArrayOfCheckedBoxes();
	$hours = abs(intdiv($addedTime, 60));
	$minutes = abs($addedTime % 60);
	$leading = "-";
	if ($addedTime > 0) { //If the added time is positive, reverse the order of the checked boxes to avoid time conflicts in the database
		$checkedBoxes = array_reverse($checkedBoxes);
		$leading = "";
	}
	foreach ($checkedBoxes as $time) {
		$sqlQuery = "UPDATE Queue SET time = ADDTIME(time, '{$leading}{$hours}:{$minutes}:00') WHERE time = '{$time}'";
		if (!$wpdb->query($sqlQuery)) {
			throw new Exception(print_r($wpdb->last_error));
		}
	}
}

/**
 *  Filter time input.
 *  @param string $addedTime The time to filter.
 *  @return int The filtered time in minutes.
 */
function filterExtraTimeInput($addedTime) {
	if (filter_var($addedTime, FILTER_VALIDATE_INT, array("options" => array("min_range" => -720, "max_range" => 720)))) {
		return $addedTime;
	} else {
		throw new Exception("{$addedTime} is an Invalid time to add. Please enter a integer.");
	}
}

/**
 *  Check if the checkbox ID is correct.
 *  @param string $checkboxID The checkbox ID to check.
 *  @return bool True if the checkbox ID is correct, false otherwise.
 */
function checkIfCorrectCheckBoxID($checkboxID) {
	if (!strcmp(substr($checkboxID, 0, 9), "checkbox_")) {
		return true;
	} else {
		return false;
	}
}


/**
 *  Get an array of the checked boxes.
 *  @return array The array of checked boxes.
 */
function getArrayOfCheckedBoxes() {
	$checkedBoxes = array();
	foreach ($_POST as $key => $value) {
		if (checkIfCorrectCheckBoxID($key) and $value == "on") { //If the checkbox is checked, the value will be "on"
			array_push($checkedBoxes, getDatabaseIDFromCheckBoxID($key));
		}
	}
	return $checkedBoxes;
}

/**
 *  Get the database ID from the checkbox ID.
 *  @param string $checkboxID The checkbox ID.
 *  @return string The database ID.
 */
function getDatabaseIDFromCheckBoxID($checkboxID) {
	$databaseID = substr($checkboxID, 9);
	return filterCheckboxID(str_replace("_", ":", $databaseID));
}

/**
 * Sanitize the checkbox ID to ensure it is in the correct format.
 * @param string $checkboxID The checkbox ID to sanitize.
 * @return string The sanitized checkbox ID.
 * @throws Exception If the checkbox ID is not in the correct format.
 */
function filterCheckboxID($checkboxID) {
    if (filter_var($checkboxID, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-2][0-9]:[0-6][0-9]:00$/")))) {
        return $checkboxID;
    } else {
        throw new Exception("{$checkboxID} is an Invalid checkbox ID");
    }
}

// ------------------------ Adding to Database Functions ------------------------

add_action( 'admin_post_post_to_DB', 'addToDB' );
add_action('admin_post_nopriv_post_to_DB', 'addToDB');

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
	if (!filter_var($time, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[0-2][0-9]:[0-5][0-9]$/")))) {
		throw new Exception("{$time} is not an integer. Please enter time as number of minutes to be added.");
	} else {
		return $time;
	}
}

/**
 *  Filter the graduation year to ensure it is valid. By validating that it is an integer and has 4 digits.
 *  @param string $gradYear The graduation year to filter.
 *  @return string The filtered graduation year.
 */
function filterGradYear($gradYear) {
	if (!(filter_var($gradYear, FILTER_VALIDATE_INT) and count(str_split($gradYear)) == 4)) {
		throw new Exception("{$gradYear} is an Invalid grad year");
	} else {
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
function addToDB()
{
	global $wpdb; //This is the global variable that allows us to interact with the database in WordPress
	for ($i = 0; $i < count($_POST["class"]); $i++) { //This loop goes through each class that was submitted in the form and adds it to the database
		try {
			$class = filterClass($_POST["class"][$i]);
			$gradYear = filterGradYear($_POST["gradYear"][$i]);
			$time = changeTimeFormat(filterTime($_POST["time"][$i]));
			$data = array("class" => $class, "gradYear" => $gradYear, "time" => $time);
			if (!$wpdb->insert("Queue", $data)) {
				throw new Exception(print_r($wpdb->last_error) . "<br> Something went wrong when intserting the class {$gradYear} {$class} with time {$time} data. Check if the data is correct or is a duplication and try again.");
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			exception_return();
		}
	}
	wp_safe_redirect('http://photoqueuesite.local/queueadmin/'); //Change this to the page you want to redirect to after the form is submitted
	exit();
}

// ----- Some exception handeling for the functions above -----

function exception_return()
{
	echo "<br> <a href='http://photoqueuesite.local/queueadmin/' style='font-size:xx-large'>Return to Queue Admin</a> <br>";
}

// ------------------------ Displaying Database Functions ------------------------

function adminPrintDB(){
	// -> works like . in java , so "$wpdb->get_results" would be "wpdb.get_results" in java
	global $wpdb;
	$result = $wpdb->get_results ( 'SELECT * FROM Queue' );
	//echo "<h3 style='display:inline'>Årgang</h3> <h3 style='display:inline'>Klasse</h3> <h3 style='display:inline' >Tidspunkt</h3> <br>";
    HTMLDisplayString($result);
}

function HTMLDisplayString($result = array()) {
	foreach ($result as $row) {
        $time = substr($row->time, 0, -3);
        $cleanID = str_replace(":", "_", $row->time);
            echo "<script>
                document.getElementById('adminDisplayTable')
                    .insertAdjacentHTML('beforeend',
                            '<td> <input type=\"checkbox\" name=\"checkbox_{$cleanID}\"> {$row->class} </td> \
                            <td>{$row->gradYear} </td> \
                            <td>{$time} </td>');
                </script>";
    }
}

function printDB() {
	global $wpdb;
	$result = $wpdb->get_results ( 'SELECT * FROM Queue' );
	foreach ($result as $row) {
        $time = substr($row->time, 0, -3);
		if (!strcmp($row->class, "Årgang")) {
			$gradYear = $row->gradYear;
		} else {
			$gradYear = "";
		}
       	echo "<script>
			document.getElementById('displayTable')
                    .insertAdjacentHTML('beforeend', 
                        '<tr> \
                            <td>{$row->class} {$gradYear} </td>\
							<td>{$row->gradYear}</td>\
							<td>{$time}</td>\
                        </tr>'); 
                </script>";
	}
}

//Action takes place during WP hook (Someplace in the loading of the site the 'wp' "hook" is activated and we do the action)
add_action('shutdown', 'page_check');
//Check if page is queueadmin else do nothing
function page_check() {
	if (is_page('queueadmin')) {
		adminPrintDB();
	} elseif (is_page('photo-queue')) {
		printDB();
	}
}
