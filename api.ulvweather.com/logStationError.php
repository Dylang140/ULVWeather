<?php
    //Dylan Giliberto
    //October 2022
    //ulvweather.com
    //A project to monitor and log weather data from a DIY weather station

    //--API PHP Script
    
    //logStationError.php
    //This file is not listed in the API home page as it is not intended for public use
    //Logs errors encountered by the weather station
    //This inserts into a new table rather than logging errors along with weather data,
    //to save space as the error column would (hopefully) usually be empty.
    //Errors are logged rather than the program on the weather station shutting down, since it
    //will installed somewhere and need to be accessed remotely

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
        $time = $_POST['time'];
        $errorToLog = $_POST['errorToLog'];

        $sql = "INSERT INTO `weather_station_error_log` (`time`, `error`) VALUES (?, ?)";

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "SQL Statement Error!";
        }
        else{
            mysqli_stmt_bind_param($stmt, "is", $time, $errorToLog);
            mysqli_stmt_execute($stmt);
            if(!$stmt->error)
                echo "Succesfully Logged Error";
            else
                echo $stmt->error;
        }
    }
    else {
        echo "Error Selecting Database";
    }

    $conn->close();
?>

