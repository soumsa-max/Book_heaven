<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Heaven</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="IndexStyle.css">
</head>
<body>

    <!-- ========== HEADER ========== -->
    <header class="header">
        <div class="logo">
            <h3><i class="fa-solid fa-book"></i> Book Heaven</h3>
        </div>

        <i class="fa-solid fa-bars" id="menu-icon"></i>

        <nav class="navbar" id="navbar">
            <a href="#home" class="active">Home</a>
            <a href="Home.php">Books</a>
            <a href="offer.php">Offers</a>
            <a href="About_us.php">About</a>
            <a href="Contact.php">Contact</a>
            <a href="../backend/Book_heaven/user/login.php " class="btn-login" > login</a>
        </nav>
    </header>

    <!-- ========== HOME SECTION ========== -->
    <section class="home" id="home">
        <div class="home-content">
            <h1>Welcome to <span>Book Heaven</span></h1>
            <p>Discover thousands of books. New releases, best sellers and second-hand books all in one place.</p>
            
            <div class="home-buttons">
                <a href="Home.php" class="btn">Explore Books</a>
                <a href="offer.php" class="btn btn-outline">View Offers</a>
            </div>
        </div>
    </section>

    <!-- ========== FEATURES ========== -->
    <section class="features">
        <div class="feature-box">
            <i class="fa-solid fa-truck-fast"></i>
            <h3>Fast Delivery</h3>
            <p>Get your books delivered quickly across Nepal.</p>
        </div>
        <div class="feature-box">
            <i class="fa-solid fa-book-open"></i>
            <h3>Wide Collection</h3>
            <p>Fiction, Programming, Second Hand and many more.</p>
        </div>
        <div class="feature-box">
            <i class="fa-solid fa-tags"></i>
            <h3>Best Offers</h3>
            <p>Enjoy big discounts and special deals every week.</p>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
        <p>&copy; 2026 Book Heaven. All rights reserved.</p>
    </footer>

    <script>
        // Mobile menu toggle
        let menuIcon = document.getElementById('menu-icon');
        let navbar = document.getElementById('navbar');

        menuIcon.onclick = () => {
            navbar.classList.toggle('active');
            menuIcon.classList.toggle('fa-xmark');
        }

        // Close menu when clicking a link
        document.querySelectorAll('.navbar a').forEach(link => {
            link.onclick = () => {
                navbar.classList.remove('active');
                menuIcon.classList.remove('fa-xmark');
            }
        });
    </script>

</body>
</html>