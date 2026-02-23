<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
        <form id="submitForm" action="https://www.example.com/submit" method="post" > <!--Change the action to the page you want to submit the form to-->
             <br>
             <input type="hidden" name="action" value="post_to_DB">
              <input type="submit" name="submit" value="submit">
            <input id="class0" type="text" name="class[0]" placeholder="Klasse nr. 1" pattern="[1-3]\.[a-z]{1,2}" title="Format: 3.x, 2.a, 3.ux,..."> 
            <input id="gradYear0" type="text" name="gradYear[0]" placeholder="Dimittend år nr. 1" pattern="[0-9]{4}" title="Format: 2019, 1988, 2006...">
            <input id="time0" type="text" name="time[0]" placeholder="Tidspunkt nr. 1" pattern="[0-2]{1}[0-9]{1}:[0-6]{1}[0-9]{1}" title="Format: 12:34, 14:40, 09:36,..."> <br>
        </form> 
        <button onclick="addOption();">Add Class</button> 

        <script> 
            var optionNumber = 1; //The first option to be added is number 1 
            const idList = ["class", "gradYear", "time"]; //List of the first part of the id
            const placeholderList = ["Klasse nr.", "Dimittend år nr.", "Tidspunkt nr."];   //List of the first part of the placeholder

            function addOption() { 
                var theForm = document.getElementById("submitForm"); //Get the form element

                for (let i= 0; i < 3; i++) {

                    var newOption = document.createElement("input"); //Create a new input element
                    newOption.id = idList[i] + optionNumber; 
                    newOption.name = idList[i] + "[" + optionNumber + "]"; 
                    newOption.type = "text"; 
                    newOption.placeholder = placeholderList[i] + " " + (optionNumber + 1);

                    if (i == 3) {    //If the input is the last one, add a line break after it
                        theForm.appendChild(newOption)
                        
                    }
                    else {
                    theForm.appendChild(newOption); 
                    }
                }
                theForm.appendChild(document.createElement("br"));
				optionNumber++;
            }
        </script>

        <!-- Displaying database content -->

        <form id="DataBaseDisplay" action="https://www.kristiansenz.com/wp-admin/admin-post.php/" method="post" > <br>
            <input type="submit" name="delete" value="Delete From DataBase"> <input type="submit" name="addTime" value="Add time to selected: "><input type="number" name="addedTime" placeholder="Minutes" min=-720 max=720>
            <input type="hidden" name="action" value="edit_database">
            <input type="checkbox" name="checkall" value="Check All" onClick="check_all(this.form['checkall']);"> Check all<br>
        </form> 

         <script>
        /** Check all checkboxes with the name starting with "checkbox_" when the "Check all" checkbox is checked, and uncheck them when it is unchecked.
            *  @param object initial_checkbox The "Check all" checkbox that was clicked.
            *  @return void
            */
            function check_all(initial_checkbox) {
                var checkboxes = document.querySelectorAll('input[type=\"checkbox\"]'); //Get all checkboxes on the page
                if (initial_checkbox.checked) { //If the "Check all" checkbox is checked, check all checkboxes with the name starting with "checkbox_"
                    for (let checkbox of checkboxes) {
                        if (checkbox.name.startsWith("checkbox_") && !checkbox.checked) { //If the checkbox is one of the checkboxes to be checked and is not already checked, check it
                            checkbox.checked = true;
                        }  
                    }
                } else {
                    for (let checkbox of checkboxes) {
                        if (checkbox.name.startsWith("checkbox_") && checkbox.checked) {
                            checkbox.checked = false;
                        }  
                    }
                }
            }
        </script>
    </body>
</html>

<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "test";
$conn = NULL;

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

//----------- functions.php -----------

/**
 *  Get data from the database and display it.
 *  @param object $conn The database connection.
 *  @return void
 */
function getFromDB($conn) {
    $sqlQuerry = "SELECT * FROM queue";
    $result = mysqli_query($conn, $sqlQuerry);
    HTMLDisplayString($result);
}



function HTMLDisplayString($result = array()) {
	foreach ($result as $row) {
        $time = substr($row["time"], 0, -3);
        $cleanID = str_replace(":", "_", $row["time"]);
            echo "<script>
                document.getElementById('DataBaseDisplay')
                    .insertAdjacentHTML('beforeend', 
                        '<div id=\"{$cleanID}\" name=\"{$cleanID}\"> \
                            <input type=\"checkbox\" name=\"checkbox_{$cleanID}\"> \
                            <h4 style=\"display:inline\">{$row['class']} </h4> \
                            <h4 style=\"display:inline\">{$row['gradYear']} </h4> \
                            <h4 style=\"display:inline\">{$time} </h4>\
                        </div> <br>'); 
                document.getElementById('DataBaseDisplay')
                    .insertAdjacentHTML('beforeend', 
                        '<hr id=\"{$cleanID}\" style=\"width:80%;text-align:centered;margin-left:10%\">');
                </script>";
    }
}


function clearPOST(){
    if (isset($_POST["clearPost"])) {
        echo "I am clearing";
        $_POST = array();
    }
}

getFromDB($conn);

sendToDB($conn);


//echo print_r($_POST) . "<br>";
checkForAddTimeOrDeleteEntry($conn);
clearPOST();
//echo "<br>";
//print_r($_POST);

//----------- admin-post.php -----------

/**
 * Check which action the form is calling.
 * @param object $conn The database connection.
 * @return void.
 */
function checkForAddTimeOrDeleteEntry($conn) {
    try{
        if (isset($_POST["delete"])) {
            deleteFromDB($conn);

        } else if (isset($_POST["addTime"])) {
            addTimeToSelected($conn);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

/**
 *  Delete the selected classes from the database.
 *  @param object $conn The database connection.
 *  @return void
 */
function deleteFromDB($conn) {
    $checkedBoxes = getArrayOfCheckedBoxes();
    foreach ($checkedBoxes as $time) {
        $sqlQuerry = "DELETE FROM queue WHERE time = '{$time}'";
        mysqli_query($conn, $sqlQuerry);
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
        throw new Exception("{$addedTime} is an Invalid time to add. Please enter a positive integer.");
    }
}

/**
 *  Add time to the selected classes in the database.
 *  @param object $conn The database connection.
 *  @return void
 */
function addTimeToSelected($conn) {
    $addedTime = filterExtraTimeInput($_POST["addedTime"]);
    $hours = intdiv($addedTime, 60);
    $minutes = $addedTime % 60;
    $checkedBoxes = getArrayOfCheckedBoxes();
    if ($addedTime < 0) { //If the added time is negative, reverse the order of the checked boxes to avoid time conflicts in the database
        $checkedBoxes = array_reverse($checkedBoxes);
    }
    foreach ($checkedBoxes as $time) {
        $sqlQuery = "UPDATE queue SET time = ADDTIME(time, '{$hours}:{$minutes}:00') WHERE time = '{$time}'";
        mysqli_query($conn, $sqlQuery);
    }
}


/**
 *  Check if the checkbox ID is correct.
 *  @param string $checkboxID The checkbox ID to check.
 *  @return bool True if the checkbox ID is correct, false otherwise.
 */
function checkIfCorrectCheckBoxID($checkboxID){
    if (!strcmp(substr($checkboxID, 0, 9), "checkbox_")) {
        return true;
    } else  {
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
        if (checkIfCorrectCheckBoxID($key) and $value == "on") {
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
    return str_replace("_", ":", $databaseID);
}

function filterCheckboxID($checkboxID) {
    if (filter_var($checkboxID, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-2][0-9]:[0-6][0-9]$/")))) {
        return $checkboxID;
    } else {
        throw new Exception("{$checkboxID} is an Invalid checkbox ID");
    }
}

//----------- Inserting Classes into Database -----------

/**
 * Insert a class into the database. By using an SQL querry, the class, graduation  year and time are inserted into the database
 * @param object $conn The database connection.
 * @param string $class The class to insert.
 * @param string $gradYear The graduation year to insert.
 * @param string $time The time to insert.  
 * @return void
 */
function insertSQL($conn, $class, $gradYear, $time)
{
	$sqlQuerry = "INSERT INTO queue (class, gradYear, time) VALUES ('$class', $gradYear, '$time')";
	try {
		mysqli_query($conn, $sqlQuerry);
		echo "Class added";
	} catch (mysqli_sql_exception $e) {
		echo 'Database error: ' . mysqli_error($conn);
	}
}

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
 *  Send the POST data to the database.
 *  @param object $conn The database connection.
 *  @param array $POST The POST data.
 *  @return void
 */
function sendToDB($conn) {
    if (isset($_POST["submit_to_DB"])) {
        echo "I am sending to the database";
	    for ($i = 0; $i < count($_POST["class"]); $i++) {
            try {
                $class = filterClass($_POST["class"][$i]);
                $gradYear = filterGradYear($_POST["gradYear"][$i]);
                $time = changeTimeFormat(filterTime($_POST["time"][$i]));
                insertSQL($conn, $class, $gradYear, $time);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
	    }
    }
}
mysqli_close($conn);
/** 
 *   $sqlQuerry = "DELETE FROM queue WHERE time = '10:00:00'";
 *   try {
 *       mysqli_query($conn, $sqlQuerry);
 *       echo "Class deleted";
 *   } catch (mysqli_sql_exception $e) {
 *       echo 'Database error: ' . mysqli_error($conn);
 *   }
 */

?>