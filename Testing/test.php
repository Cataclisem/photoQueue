<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
   <body>
        <form id="myForm"  method="post" > <!--Change the action to the page you want to submit the form to. action="https://www.example.com/submit" <input type="hidden" name="action" value="post_to_DB">-->
             <br> 
            <input id="class0" type="text" name="class[0]" placeholder="Klasse nr. 1" pattern="[1-3]\.[a-z]{1,2}" title="Format: 3.x, 2.a, 3.ux,..."> 
            <input id="gradYear0" type="text" name="gradYear[0]" placeholder="Dimittend år nr. 1" pattern="[0-9]{4}" title="Format: 2019, 1988, 2006...">
            <input id="time0" type="text" name="time[0]" placeholder="Tidspunkt nr. 1" pattern="[0-2]{1}[0-9]{1}:[0-6]{1}[0-9]{1}" title="Format: 12:34, 14:40, 09:36,..."> <br>
            <input type="submit" name="submit">
        </form> 
        <button onclick="addOption();">Add Class</button> 

        <script> 
            var optionNumber = 1; //The first option to be added is number 1 
            const idList = ["class", "gradYear", "time"]; //List of the first part of the id
            const placeholderList = ["Klasse nr.", "Dimittend år nr.", "Tidspunkt nr."];   //List of the first part of the placeholder

            function addOption() { 
                var theForm = document.getElementById("myForm"); //Get the form element

                for (let i= 0; i < 3; i++) {

                    var newOption = document.createElement("input"); //Create a new input element
                    newOption.id = idList[i] + optionNumber; 
                    newOption.name = idList[i] + "[" + optionNumber + "]"; 
                    newOption.type = "text"; 
                    newOption.placeholder = placeholderList[i] + " " + (optionNumber + 1);

                    if (i == 3) {    //If the input is the last one, add a line break after it
                        theForm.appendChild(newOption)
                        theForm.appendChild(document.createElement("br"));
                    }
                    else {
                    theForm.appendChild(newOption); 
                    }
                }
				optionNumber++;
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
function sendToDB($conn, $POST) {
	for ($i = 0; $i < count($POST["class"]); $i++) {
        try {
            $class = filterClass($POST["class"][$i]);
            $gradYear = filterGradYear($POST["gradYear"][$i]);
            $time = changeTimeFormat(filterTime($POST["time"][$i]));
            insertSQL($conn, $class, $gradYear, $time);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
	}
}

/**
 *  Get data from the database and display it.
 *  @param object $conn The database connection.
 *  @return void
 */
function getFromDB($conn) {
    $sqlQuerry = "SELECT * FROM queue";
    $result = mysqli_query($conn, $sqlQuerry);
    foreach ($result as $row) {
        $time = substr($row["time"], 0, -3);
		echo "<h4 style='display:inline'>{$row['class']} </h4> <h4 style='display:inline'>{$row['gradYear']} </h4> <h4 style='display:inline'>{$time} </h4> <br>";
		echo "<hr style='width:80%;text-align:centered;margin-left:10%'>";
    }
}

getFromDB($conn);

sendToDB($conn, $_POST);

mysqli_close($conn);
?>