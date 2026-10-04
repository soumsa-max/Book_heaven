<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>About Us</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="Fstyle.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="logo">
        <h3><i class="fa-solid fa-book"></i> Book Heaven</h3>
    </div>

    <div class="search-box">
        <form action="Home.php" method="GET" style="display:flex; align-items:center;">
            <input type="search" name="search" class="search" placeholder="search for book">
            <button type="submit" style="background:none; border:none; cursor:pointer;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <div class="menu">
        <?php if (isset($_SESSION['user_id'])) { ?>
            <span style="color: aqua; margin-right: 15px;">
                Hello, <?php echo $_SESSION['user_name'] ?? 'User'; ?>
            </span>
            <a href="../backend/Book_heaven/user/logout.php">logout</a>
        <?php } else { ?>
            <a href="../backend/Book_heaven/user/login.php">login</a>
            <a href="../backend/Book_heaven/user/register.php">register</a>
        <?php } ?>
        <a href="#"><i class="fa-solid fa-cart-plus"></i> cart</a>
    </div>
</header>

<!-- NAVBAR -->
<nav>
    <div class="navbar">
        <ul>
            <li><a href="Home.php">Home</a></li>

            <li class="category-menu">
                <a href="#" onclick="showCategories(event)">
                    Categories <i class="fa-solid fa-chevron-down"></i>
                </a>
                <div class="category-dropdown">
                    <a href="Home.php?category=Fiction">Fiction</a>
                    <a href="Home.php?category=Mystery">Mystery</a>
                    <a href="Home.php?category=Thriller">Thriller</a>
                    <a href="Home.php?category=Romance">Romance</a>
                    <a href="Home.php?category=Fantasy">Fantasy</a>
                    <a href="Home.php?category=Science Fiction">Science Fiction</a>
                    <a href="Home.php?category=Horror">Horror</a>
                    <a href="Home.php?category=Adventure">Adventure</a>
                    <a href="Home.php?category=Biography">Biography</a>
                    <a href="Home.php?category=Autobiography">Autobiography</a>
                    <a href="Home.php?category=History">History</a>
                    <a href="Home.php?category=Philosophy">Philosophy</a>
                    <a href="Home.php?category=Psychology">Psychology</a>
                    <a href="Home.php?category=Self Help">Self Help</a>
                    <a href="Home.php?category=Business">Business</a>
                    <a href="Home.php?category=Finance">Finance</a>
                    <a href="Home.php?category=Education">Education</a>
                    <a href="Home.php?category=Science">Science</a>
                    <a href="Home.php?category=Technology">Technology</a>
                    <a href="Home.php?category=Programming">Programming</a>
                    <a href="Home.php?category=Children's Books">Children's Books</a>
                    <a href="Home.php?category=Young Adult">Young Adult</a>
                    <a href="Home.php?category=Poetry">Poetry</a>
                    <a href="Home.php?category=Comics & Graphic Novels">Comics & Graphic Novels</a>
                    <a href="Home.php?category=Art & Design">Art & Design</a>
                    <a href="Home.php?category=Cooking & Food">Cooking & Food</a>
                    <a href="Home.php?category=Travel">Travel</a>
                    <a href="Home.php?category=Sports">Sports</a>
                    <a href="Home.php?category=Health & Fitness">Health & Fitness</a>
                    <a href="Home.php?category=Religion & Spirituality">Religion & Spirituality</a>
                    <a href="Home.php?category=Law">Law</a>
                    <a href="Home.php?category=Politics">Politics</a>
                    <a href="Home.php?category=Reference">Reference</a>
                    <a href="Home.php?category=Academic">Academic</a>
                    <a href="Home.php?category=Second Hand Books">Second Hand Books</a>
                </div>
            </li>

            <li><a href="offer.php">Offers</a></li>
            <li><a href="About_us.php">About</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </div>
</nav>

<!-- ABOUT HERO -->
<section class="about-hero">
    <div class="about-hero-text">
        <h1>Welcome to <span>Book Heaven</span></h1>
        <p>Your world of stories, knowledge and imagination.</p>
        <p class="small-text">
            Discover books you love and explore new stories waiting to be read.
        </p>
    </div>
    <div class="about-hero-icon">
        <i class="fa-solid fa-book-open"></i>
    </div>
</section>

<!-- ABOUT US -->
<section class="about-section">
    <div class="about-image">
        <i class="fa-solid fa-book-open-reader"></i>
    </div>
    <div class="about-content">
        <h2>About Book Heaven</h2>
        <p>
            Book Heaven is an online bookstore created for people who love reading and discovering new books.
        </p>
        <p>
            We provide a simple and convenient way for readers to explore books, check their details, read reviews, and purchase their favorite books from one place.
        </p>
        <p>
            Whether you enjoy fiction, science, history, self-development, education or other genres, Book Heaven helps you find something worth reading.
        </p>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="why-section">
    <h2>Why Choose Book Heaven?</h2>
    <p class="section-description">
        Everything you need for a better reading experience.
    </p>

    <div class="features">
        <div class="feature-card">
            <i class="fa-solid fa-book"></i>
            <h3>Large Collection</h3>
            <p>Explore books from different genres and discover something new to read.</p>
        </div>

        <div class="feature-card">
            <i class="fa-solid fa-tags"></i>
            <h3>Best Prices</h3>
            <p>Find great books at affordable prices and enjoy special offers.</p>
        </div>

        <div class="feature-card">
            <i class="fa-solid fa-truck-fast"></i>
            <h3>Easy Shopping</h3>
            <p>Browse, choose and order your favorite books with a simple shopping experience.</p>
        </div>

        <div class="feature-card">
            <i class="fa-solid fa-star"></i>
            <h3>Reader Reviews</h3>
            <p>Read reviews from other readers before choosing your next book.</p>
        </div>
    </div>
</section>

<!-- OUR MISSION -->
<section class="mission">
    <div>
        <h2>Our Mission</h2>
        <p>
            Our mission is to make books easier to discover, easier to purchase and more accessible to everyone.
        </p>
        <p>
            We believe that every book has a story to tell and every reader has a story waiting to be discovered.
        </p>
    </div>
    <i class="fa-solid fa-heart"></i>
</section>

<!-- FOOTER -->
<footer>
    <div>
        <h3>Help</h3>
        <p>FAQ</p>
        <p>Support</p>
    </div>
    <div>
        <h3>Services</h3>
        <p>Delivery</p>
        <p>Returns</p>
    </div>
    <div>
        <h3>About Us</h3>
        <p>Company</p>
        <p>Careers</p>
    </div>
    <div>
        <h3>Contact</h3>
        <p>bookstore@email.com</p>
        <p>+977-9800000000</p>
    </div>
</footer>

<script>
function showCategories(event) {
    event.preventDefault();
    document.querySelector(".category-menu").classList.toggle("show");
}
</script>

</body>
</html>