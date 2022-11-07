<?php
    //Dylan Giliberto
    //October 2022
    //ulvweather.com
    //A project to monitor and log weather data from a DIY weather station

    //--API PHP Script

    //queryWeatherData.php
    //Returns most recent weather recording (raw data), unless start time and end time
    //are specified in UNIX timestamp format. In that case, all data recordings
    //between those intervals are returned.

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
    header('Access-Control-Allow-Headers: *');

    $servername = "localhost";
    $username = "dylang140";
    $password = "Mogli.123.456";
    $database = "WeatherData";
    // Create connection
    $conn = new mysqli($servername, $username, $password);

    if($conn->connect_error){
        echo "Could not Connect to Database" . $conn->connect_error;
    }
    else if($conn->query("USE " . $database) === TRUE){
        $end = $_POST["end"];
        $start = $_POST["start"];

        if(!is_null($start) && !is_null($end)){
            $sql = "SELECT * FROM weather_data WHERE time < ? AND time > ?";

            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sql)){
                echo "SQL Statement Error!";
            }
            else{
                mysqli_stmt_bind_param($stmt, "ii", $end, $start);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while($row = $result->fetch_assoc()) {
                    $myArray[] = $row;
                }
                echo json_encode($myArray);
            }
        }
        else{
            $sql = "SELECT * FROM weather_data ORDER BY time DESC LIMIT 1";

            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sql)){
                echo "SQL Statement Error!";
            }
            else{
                mysqli_stmt_bind_param($stmt, "ii", $end, $start);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while($row = $result->fetch_assoc()) {
                    $myArray[] = $row;
                }
                echo json_encode($myArray);
            }
        }
    }
    else {
        echo "Error Selecting Database";
    }

    $conn->close();
?>

