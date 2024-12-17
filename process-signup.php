<?php

// If you installed PHPMailer using Composer, include the Composer autoload file
require __DIR__ . '/vendor/autoload.php';

// If you manually downloaded PHPMailer, use the correct path to the PHPMailer files
// require __DIR__ . '/path/to/PHPMailer/src/Exception.php';
// require __DIR__ . '/path/to/PHPMailer/src/PHPMailer.php';
// require __DIR__ . '/path/to/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mysqli = require __DIR__ . "/database.php";


if (empty($_POST["name"])) {
    die("Name is required!");
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid Email is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["confirm-password"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$verification_code = bin2hex(random_bytes(16));

$sql = "INSERT INTO users (fullname, email, password_hash, role, verification_code)
        VALUES (?, ?, ?, 'client', ?)";

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("ssss",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash,
                  $verification_code);

try {
    $stmt->execute();
    send_verification_email($_POST["email"], $verification_code);
    header("Location: signup-success.html");
    exit;
} catch (mysqli_sql_exception) {
    if ($mysqli->errno === 1062) {
        die("Email already taken");
    } else {
        die("Database error: ");
    }
}

function send_verification_email($email, $verification_code) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Use Gmail's SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mailernimike6@gmail.com'; // Your Gmail address
        $mail->Password   = 'aoad ikwg wqcj rxxb'; // Your app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('mailernimike6@gmail.com', 'Mailer');
        $mail->addAddress($email); // Sends the email to the user's email address

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body    = "Please click the link below to verify your email address: <a href='https://lightblue-hamster-252631.hostingersite.com/verify-email.php?code=$verification_code'>Verify Email</a>"; // Update to localhost

        $mail->send();
    } catch (Exception $e) {
        die("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

?>