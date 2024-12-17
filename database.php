<?php

$host = 'localhost';
$db = 'u779691448_EggWatchDB';
$user = 'u779691448_EggWatch';
$pass = 'Namias99'; 

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Check if the 'role' column exists
$result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'role'");
if ($result->num_rows == 0) {
    $mysqli->query("ALTER TABLE users ADD COLUMN role ENUM('admin', 'client') DEFAULT 'client'");
}

// Check if the 'is_verified' column exists
$result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'is_verified'");
if ($result->num_rows == 0) {
    $mysqli->query("ALTER TABLE users ADD COLUMN is_verified BOOLEAN DEFAULT FALSE");
}

// Check if the 'verification_code' column exists
$result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'verification_code'");
if ($result->num_rows == 0) {
    $mysqli->query("ALTER TABLE users ADD COLUMN verification_code VARCHAR(32)");
}

return $mysqli;
?>