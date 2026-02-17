<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
    <body>

        <form id="myForm" action= "test.php" method = "post">
            <input type="submit"> <br>
            <label for="IDYearclass0">ID:</label> <label for="class0">Class:</label> <label for="gradYear0">Year:</label> <label for="time0">Time:</label> <br>
            <input id="IDYearclass0" type="text" name="IDclassYear[0]" placeholder="ID 1" pattern="[0-9]{4}[a-z]{1}" title="Format: 2019x"> 
            <input id="class0" type="text" name="class[0]" placeholder="Class 1" pattern="[1-3]\.[a-z]{1}" title="Format: 3.x"> 
            <input id="gradYear0"type="text" name="gradYear[0]" placeholder="Year 1" pattern="[0-9]{4}" title="Format: 0123">
            <input id="time0" type="text" name="time[0]" placeholder="Time 1" pattern="[0-9]{2}:[0-9]{2}" title="Format: 12:34"> <br>
        </form> 
        <button onclick="addOption();">Add Class</button> 

        <script> 
            var optionNumber = 1; //The first option to be added is number 1 
            const idList = ["IDclassYear", "class", "gradYear", "time"]; //List of the first part of the id
            const placeholderList = ["ID", "Class", "Year", "Time"];   //List of the first part of the placeholder

            function addOption() { 
                var theForm = document.getElementById("myForm"); //Get the form element

                for (let i= 0; i < 4; i++) {

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

function insertSQL($conn, $IDyearClass, $class, $gradYear, $time)
{
	$sqlQuerry = "INSERT INTO queue (IDyearClass, class, gradYear, time) VALUES ('$IDyearClass', '$class', $gradYear, '$time')";
	try {
		mysqli_query($conn, $sqlQuerry);
		echo "Class added";
	} catch (mysqli_sql_exception) {
		echo 'Something Wrong';
	}
}

function changeTimeFormat($time) {
	$time = "{$time}:00";
	return $time;
}

function sendToDB($conn, $POST) {
	for ($i = 0; $i < count($POST["IDclassYear"]); $i++) {
		$IDyearClass = $POST["IDclassYear"][$i];
		$class = $POST["class"][$i];
		$gradYear = $POST["gradYear"][$i];
		$time = changeTimeFormat($POST["time"][$i]);
		insertSQL($conn, $IDyearClass, $class, $gradYear, $time);
	}
}

sendToDB($conn, $_POST);

for ($i = 0; $i < count($_POST["IDclassYear"]); $i++) {
	foreach ($_POST as $key => $value) {
		echo "key: {$key} value: {$value[$i]} <br>";
	}
}

//insertSQL($conn, "2018y", "3.y", 2021, "18:30:00");


mysqli_close($conn);
?>