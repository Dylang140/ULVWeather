<?php
    //Dylan Giliberto
    //October 2022
    //ulvweather.com
    //A project to monitor and log weather data from a DIY weather station

    //--API PHP Script
    
    //lastHourWeatherData.php
    //Returns all recordings in the last hour (raw data)
    //Note: This returns the last hours worth of recordings, eben if those recordings were taken more
    //than an hour ago. If the weather station is left off, this script will continue to return data
    
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
        $uploadRate = 60;
        $rowsToQuery = 3600 / $uploadRate;

        $sql = "SELECT * FROM weather_data ORDER BY time DESC LIMIT ?";

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "SQL Statement Error!";
        }
        else{
            mysqli_stmt_bind_param($stmt, "i", $rowsToQuery);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while($row = $result->fetch_assoc()) {
                $myArray[] = $row;
            }
            echo json_encode($myArray);
        }
    }
    else {
        echo "Error Selecting Database";
    }

    $conn->close();
?>