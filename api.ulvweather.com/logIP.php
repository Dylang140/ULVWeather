<?php
    //Dylan Giliberto
    //October 2022
    //ulvweather.com
    //A project to monitor and log weather data from a DIY weather station

    //--API PHP Script
    
    //logIP.php
    //This file is not listed in the API home page as it is not intended for public use
    //Logs the Raspberry PIs local IP address each time the main weatherTracker program starts
    //so that I can remotely access it for maintenence or changes

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
        $ip = $_POST['rpi_ip'];

        $sql = "INSERT INTO `raspberry_pi_ip` (`rpi_ip`) VALUES (?)";

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "SQL Statement Error!";
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $ip);
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

