<?php

session_start();

require_once "../config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare(
        "SELECT * FROM users
         WHERE email = :email
         AND role = 'admin'
         LIMIT 1"
    );

    $stmt->execute([
        ':email' => $email
    ]);

    $admin = $stmt->fetch();

    if ($admin) {

        /*
         * Temporary development authentication.
         * We will change this to password_hash/password_verify
         * once the basic system is working.
         */
        if ($password === $admin['password']) {

            $_SESSION['user_id'] = $admin['user_id'];
            $_SESSION['name'] = $admin['name'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['role'] = $admin['role'];

            header("Location: index.php");
            exit();

        } else {
            $error = "Incorrect password.";
        }

    } else {
        $error = "Admin account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login - Book Heaven</title>

    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<div class="login-container">

    <div class="login-box">

        <h1>Book Heaven</h1>
        <h2>Admin Login</h2>

        <?php if ($error != ""): ?>
            <p class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter admin email"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >
            </div>

            <button type="submit">
                Login
            </button>

        </form>

    </div>

</div>

</body>

</html>