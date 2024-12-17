
<?php

$mysqli = require __DIR__ . "/database.php";

$verification_code = $_GET['code'];

$sql = "UPDATE users SET is_verified = TRUE WHERE verification_code = ?";
$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("s", $verification_code);

if ($stmt->execute()) {
    echo "Email verified successfully!";
    header("Location: login.php");
} else {
    die($mysqli->error . " " . $mysqli->errno);
}
?>