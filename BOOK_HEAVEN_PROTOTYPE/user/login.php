<?php

session_start();

require_once "../config.php";

$error = "";


/* =========================
   LOGIN
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    if ($email === "" || $password === "") {

        $error = "Please enter your email and password.";

    } else {

        $stmt = $pdo->prepare("
            SELECT user_id, name, email, password, role
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ":email" => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$user) {

            $error = "Invalid email or password.";

        } else {

            $login_success = false;


            /* =========================
               ADMIN
            ========================= */

            if ($user["role"] === "admin") {

                // Admin password is plain text
                if ($password === $user["password"]) {

                    $login_success = true;
                }

            }


            /* =========================
               CUSTOMER
            ========================= */

            elseif ($user["role"] === "customer") {

                // Customer password is hashed
                if (password_verify(
                    $password,
                    $user["password"]
                )) {

                    $login_success = true;
                }

            }


            if ($login_success) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];


                /* =========================
                   REDIRECT
                ========================= */

                if ($user["role"] === "admin") {

                    header("Location: ../admin/index.php");
                    exit();

                }


                if ($user["role"] === "customer") {

                    header("Location: index.php");
                    exit();

                }

            } else {

                $error = "Invalid email or password.";

            }

        }

    }

}


$page_title = "Login - Book Heaven";

$css_path = "../assets/css/style.css";

require_once "includes/header.php";

?>


<section class="auth-page">

    <div class="auth-card">

        <div class="auth-heading">

            <h1>Welcome Back</h1>

            <p>
                Login to your Book Heaven account.
            </p>

        </div>


        <?php if ($error !== ""): ?>

            <div class="auth-alert auth-error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form method="POST" class="auth-form">

            <div class="form-field">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <div class="form-field">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <div class="show-password">

                <label>

                    <input
                        type="checkbox"
                        id="showPassword"
                    >

                    Show Password

                </label>

            </div>


            <button
                type="submit"
                class="auth-submit"
            >
                Login
            </button>

        </form>


        <div class="auth-bottom">

            <p>
                Don't have an account?

                <a href="register.php">
                    Register
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

showPassword.addEventListener("change", function () {

    password.type = this.checked
        ? "text"
        : "password";

});

</script>


<?php

require_once "includes/footer.php";

?>