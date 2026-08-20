<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin - Book Heaven</title>

    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>

<div class="admin-container">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="sidebar-logo">
            <h2>Book Heaven</h2>
            <p>Admin Panel</p>
        </div>

        <nav class="sidebar-menu">

            <a href="index.php"
               class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                Dashboard
            </a>

            <a href="books.php"
               class="<?php echo $current_page == 'books.php' ? 'active' : ''; ?>">
                Books
            </a>

            <a href="categories.php"
               class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                Categories
            </a>

            <a href="orders.php"
               class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                Orders
            </a>

            <a href="users.php"
               class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                Users
            </a>

            <a href="../index.php">
                View Store
            </a>

            <a href="logout.php">
                Logout
            </a>

        </nav>

    </aside>


    <!-- Main Content -->

    <main class="main-content">

        <header class="admin-navbar">

            <div>
                <h3>Admin Panel</h3>
            </div>

            <div class="admin-user">

                <span>
                    Welcome,
                    <?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?>
                </span>

            </div>

        </header>

        <section class="page-content">