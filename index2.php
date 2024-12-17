<?php
// Database connection parameters
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest temperature and humidity readings
$sql = "SELECT temperature, humidity FROM sensor_readings ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $temperature = $row["temperature"];
    $humidity = $row["humidity"];
} else {
    $temperature = "N/A";
    $humidity = "N/A";
}

$conn->close();

// Function to determine status
function getStatus($value, $min, $max) {
    if ($value < $min) return "Below range";
    if ($value > $max) return "Above range";
    return "Optimal range";
}

$tempStatus = getStatus($temperature, 36.5, 37.5);
$humidStatus = getStatus($humidity, 50, 60);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EggWatch: Temperature and Humidity Monitor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .container {
            display: flex;
            gap: 20px;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            width: 200px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .card-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .card-icon {
            width: 24px;
            height: 24px;
            fill: #666;
        }
        .card-content {
            font-size: 24px;
            font-weight: bold;
            color: #000;
        }
        .card-status {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card" id="temperature-card">
            <div class="card-header">
                <span class="card-title">Temperature</span>
                <svg class="card-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2a7 7 0 0 0-7 7v6a7 7 0 0 0 14 0V9a7 7 0 0 0-7-7zm3 13a3 3 0 1 1-6 0V9a3 3 0 1 1 6 0v6z"/>
                </svg>
            </div>
            <div class="card-content" id="temperature-value"><?php echo $temperature; ?>°C</div>
            <div class="card-status" id="temperature-status"><?php echo $tempStatus; ?></div>
        </div>
        <div class="card" id="humidity-card">
            <div class="card-header">
                <span class="card-title">Humidity</span>
                <svg class="card-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
                </svg>
            </div>
            <div class="card-content" id="humidity-value"><?php echo $humidity; ?>%</div>
            <div class="card-status" id="humidity-status"><?php echo $humidStatus; ?></div>
        </div>
    </div>

    <script>
        // Refresh the page every 5 seconds to get new data
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
</body>
</html>