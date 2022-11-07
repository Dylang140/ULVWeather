<?php
    //Dylan Giliberto
    //October 2022
    //ulvweather.com
    //A project to monitor and log weather data from a DIY weather station

    //--API PHP Script
    
    //currentWeatherData.php
    //Returns processed weather data to be displayed.
    //This script is meant to be called each time a page showing the current weather is loaded/refreshed
    //as some data is averaged over a period, while other data will provide the most recent recording
    //Temperature, humidty, and pressure will be avergaed over a period of 5 minutes
    //Rain Volume will be summed for the last hour
    //windSpeed will show most recent wind speed recording
    
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

            $rainSum = 0;
            while($row = $result->fetch_assoc()) {
                $myArray[] = $row;
                $rainSum += $row['rainVolume'];
            }
            $len = count($myArray);
            $currentWeather = $myArray[0];
            $currentWeather['rainSum'] = $rainSum;
            foreach ($currentWeather as $key => &$value){
                if($key != 'time' && $key != 'ID' && $key != 'testing' && $key != 'status' && $key != 'windDirection')
                    $value = number_format($value, 2, '.', '');
            }
            echo json_encode($currentWeather);
        }
    }
    else {
        echo "Error Selecting Database";
    }

    $conn->close();
?>