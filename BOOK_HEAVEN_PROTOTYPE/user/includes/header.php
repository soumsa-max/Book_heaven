<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in =
    isset($_SESSION["user_id"]) &&
    isset($_SESSION["role"]);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo $page_title ?? "Book Heaven"; ?>
    </title>

    <link
        rel="stylesheet"
        href="<?php echo $css_path ?? "../assets/css/style.css"; ?>"
    >

</head>

<body>


<header class="site-header">

    <div class="header-container">


        <!-- LOGO -->

        <a
            href="index.php"
            class="logo"
        >
            Book Heaven
        </a>


        <!-- NAVIGATION -->

        <nav class="main-nav">

            <a href="index.php">
                Home
            </a>

            <a href="Home.php">
                Books
            </a>

            <?php if ($is_logged_in): ?>


                <a href="logout.php">
                    Logout
                </a>

            <?php else: ?>

                <a href="login.php">
                    Login
                </a>

                <a
                    href="register.php"
                    class="nav-register"
                >
                    Register
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>


<main>