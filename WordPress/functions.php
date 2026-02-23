<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

//------------------------------- Before is Elementor stuff --------

// ------------------------ Removing to Database Functions ------------------------

// add_action( 'admin_post_edit_database', 'checkForAddTimeOrDeleteEntry' );
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
		wp_safe_redirect('https://kristiansenz.com/queueadmin/'); //Change this to the page you want to redirect to after the form is submitted
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
    if (filter_var($checkboxID, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-2][0-9]:[0-6][0-9]$/")))) {
        return $checkboxID;
    } else {
        throw new Exception("{$checkboxID} is an Invalid checkbox ID");
    }
}

// ------------------------ Adding to Database Functions ------------------------

//add_action( 'admin_post_post_to_DB', 'testfunc3' );
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
	wp_safe_redirect('https://kristiansenz.com/queueadmin/'); //Change this to the page you want to redirect to after the form is submitted
	exit();
}

// ----- Some exception handeling for the functions above -----

function exception_return()
{
	echo "<br> <a href='https://kristiansenz.com/queueadmin/' style='font-size:xx-large'>Return to Queue Admin</a> <br>";
}

// ------------------------ Displaying Database Functions ------------------------

function printDB(){
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
                document.getElementById('DataBaseDisplay')
                    .insertAdjacentHTML('beforeend', 
                        '<div id=\"{$cleanID}\" name=\"{$cleanID}\"> \
                            <input type=\"checkbox\" name=\"checkbox_{$cleanID}\"> \
                            <h4 style=\"display:inline\">{$row->class} </h4> \
                            <h4 style=\"display:inline\">{$row->gradYear} </h4> \
                            <h4 style=\"display:inline\">{$time} </h4>\
                        </div> <br>'); 
                document.getElementById('DataBaseDisplay')
                    .insertAdjacentHTML('beforeend', 
                        '<hr id=\"{$cleanID}\" style=\"width:80%;text-align:centered;margin-left:10%\">');
                </script>";
    }
}

//Action takes place during WP hook (Someplace in the loading of the site the 'wp' "hook" is activated and we do the action)
add_action('shutdown', 'page_check');
//Check if page is queueadmin else do nothing
function page_check() {
	if (is_page('queueadmin')) {
		printDB();
	}
}