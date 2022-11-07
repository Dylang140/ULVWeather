<?php
    //Dylan Giliberto
    //October 2022
    //ulvweather.com
    //A project to monitor and log weather data from a DIY weather station

    //--API PHP Script
    
    //postWeatherData.php
    //This file is not listed in the API home page as it is not intended for public use
    //Accepts raw weather data from weather station via an HTTP request and inserts
    //the data in the MySQL database

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
        $temp = $_POST['temp'];
        $humidity = $_POST['humidity'];
        $pressure = $_POST['pressure'];
        $windSpeed = $_POST['windSpeed'];
        $windDirection = $_POST['windDirection'];
        $windDegree = $_POST['windDegree'];
        $rainVolume = $_POST['rainVolume'];
        $cpuTemp = $_POST['cpuTemp'];
        $status = $_POST['status'];
        $testing = $_POST['testing'];
        $rainRateHourly = $_POST['rainRateHourly'];

        $sql = "INSERT INTO weather_data 
            (`time`, temp, humidity, pressure, windSpeed, windDirection, windDegree, rainVolume, rainRateHourly, cpuTemp, `status`, testing) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "SQL Statement Error!";
        }
        else{
            mysqli_stmt_bind_param($stmt, "iddddsddddii", 
                $time, $temp, $humidity, $pressure, $windSpeed, $windDirection, $windDegree, 
                $rainVolume, $rainRateHourly, $cpuTemp, $status, $testing);
            mysqli_stmt_execute($stmt);
            if(!$stmt->error)
                echo "Succesfully Logged Weather Data";
            else
                echo $stmt->error;
        }
    }
    else {
        echo "Error Selecting Database";
    }

    $conn->close();
?>

