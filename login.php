<?php
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $mysqli->stmt_init();

    if (!$stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param("s", $_POST["email"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($_POST["password"], $user["password_hash"])) {
            session_start();
            session_regenerate_id();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"];
            
            if ($user["is_verified"]) {
                if ($user["role"] === 'admin') {
                    header("Location: admin-dashboard.php");
                } else {
                    header("Location: index.php");
                }
            } else {
                header("Location: unverified.php");
            }
            exit;
        }
    }
    
    $is_invalid = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EggWatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #1877f2;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        button {
            background-color: #1877f2;
            color: #ffffff;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #166fe5;
        }
        .error {
            color: #c62828;
            text-align: center;
            margin-bottom: 1rem;
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1rem 0;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        .divider span {
            padding: 0 0.5rem;
            color: #666;
        }
        .google-login {
            display: block;
            text-align: center;
            background-color: #4285f4;
            color: #ffffff;
            padding: 0.75rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .google-login:hover {
            background-color: #357ae8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login to EggWatch</h1>
        <?php if ($is_invalid): ?>
            <p class="error">Invalid email or password</p>
        <?php endif; ?>
        <form method="post" id="login-form">
            <input type="email" id="email" name="email" placeholder="Email Address" required value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="divider">
            <span>or</span>
        </div>
        <a href="oauth-login.php" class="google-login">Login with Google</a>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            var error = false;

            if (!email) {
                alert('Please enter your email address');
                error = true;
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                alert('Please enter a valid email address');
                error = true;
            }

            if (!password) {
                alert('Please enter your password');
                error = true;
            }

            if (error) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>