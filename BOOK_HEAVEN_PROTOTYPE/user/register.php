<?php

session_start();

require_once "../config.php";

$error = "";
$success = "";


/* =========================
   REGISTRATION
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    /* =========================
       VALIDATION
    ========================= */

    if ($name === "" || $email === "" || $password === "") {

        $error = "Please fill in all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        /* =========================
           CHECK EMAIL
        ========================= */

        $stmt = $pdo->prepare("
            SELECT user_id
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ":email" => $email
        ]);

        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($existing_user) {

            $error = "This email is already registered.";

        } else {

            /* =========================
               HASH CUSTOMER PASSWORD
            ========================= */

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /* =========================
               INSERT CUSTOMER
            ========================= */

            $stmt = $pdo->prepare("
                INSERT INTO users
                (
                    name,
                    email,
                    password,
                    phone,
                    address,
                    role
                )
                VALUES
                (
                    :name,
                    :email,
                    :password,
                    :phone,
                    :address,
                    'customer'
                )
            ");

            $stmt->execute([
                ":name" => $name,
                ":email" => $email,
                ":password" => $hashed_password,
                ":phone" => $phone,
                ":address" => $address
            ]);


            $success = "Registration successful! You can now login.";
        }
    }
}


$page_title = "Register - Book Heaven";

$css_path = "../assets/css/style.css";

require_once "includes/header.php";

?>


<section class="auth-page">

    <div class="auth-card">

        <div class="auth-heading">

            <h1>Create Your Account</h1>

            <p>
                Join Book Heaven today.
            </p>

        </div>


        <!-- ERROR -->

        <?php if ($error !== ""): ?>

            <div class="auth-alert auth-error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <!-- SUCCESS -->

        <?php if ($success !== ""): ?>

            <div class="auth-alert auth-success">

                <?php echo htmlspecialchars($success); ?>

                <br><br>

                <a href="login.php">
                    Login to your account
                </a>

            </div>

        <?php endif; ?>


        <!-- FORM -->

        <form method="POST" class="auth-form">


            <!-- NAME -->

            <div class="form-field">

                <label for="name">
                    Full Name *
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="form-field">

                <label for="email">
                    Email Address *
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                    required
                >

            </div>


            <!-- PHONE -->

            <div class="form-field">

                <label for="phone">
                    Phone Number
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    placeholder="Enter your phone number"
                    value="<?php echo htmlspecialchars($_POST["phone"] ?? ""); ?>"
                >

            </div>


            <!-- ADDRESS -->

            <div class="form-field">

                <label for="address">
                    Address
                </label>

                <input
                    type="text"
                    id="address"
                    name="address"
                    placeholder="Enter your address"
                    value="<?php echo htmlspecialchars($_POST["address"] ?? ""); ?>"
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-field">

                <label for="password">
                    Password *
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    required
                >

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="form-field">

                <label for="confirm_password">
                    Confirm Password *
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    required
                >

            </div>


            <!-- SHOW PASSWORD -->

            <div class="show-password">

                <label>

                    <input
                        type="checkbox"
                        id="showPassword"
                    >

                    Show Password

                </label>

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="auth-submit"
            >
                Create Account
            </button>


        </form>


        <!-- LOGIN -->

        <div class="auth-bottom">

            <p>

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </p>

        </div>

    </div>

</section>


<script>

const showPassword =
    document.getElementById("showPassword");

const password =
    document.getElementById("password");

const confirmPassword =
    document.getElementById("confirm_password");


showPassword.addEventListener("change", function () {

    if (this.checked) {

        password.type = "text";
        confirmPassword.type = "text";

    } else {

        password.type = "password";
        confirmPassword.type = "password";

    }

});

</script>


<?php

require_once "includes/footer.php";

?>