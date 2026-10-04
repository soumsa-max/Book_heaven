<?php
session_start();
require_once "config.php";

// Get only active offers from database
$sql = "SELECT * FROM offers WHERE status = 'active' ORDER BY id DESC";
$stmt = $pdo->query($sql);
$offers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Offers - Book Heaven</title>
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
        <a href="../backend/Book_heaven/user/login.php">login</a>
        <a href="../backend/Book_heaven/user/register.php">register</a>
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
                    <a href="Home.php?category=Romance">Romance</a>
                    <a href="Home.php?category=Fantasy">Fantasy</a>
                    <a href="Home.php?category=Programming">Programming</a>
                    <a href="Home.php?category=Second Hand Books">Second Hand Books</a>
                </div>
            </li>
            <li><a href="offer.php">Offers</a></li>
            <li><a href="About_us.php">About</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </div>
</nav>

<!-- BANNER -->
<section class="offer-banner">
    <div>
        <h1>Special Offers</h1>
        <p>Get your favorite books at amazing prices!</p>
        <p>Limited time discounts available now.</p>
    </div>
    <i class="fa-solid fa-tags"></i>
</section>

<!-- OFFERS FROM DATABASE -->
<section class="offers">
    <div class="title">
        <h2>Today's Best Deals</h2>
        <p>Don't miss these offers!</p>
    </div>

    <div class="offer-row">

        <?php if (count($offers) == 0) { ?>
            <p style="padding: 30px; color: #666; width: 100%;">
                No active offers right now.
            </p>
        <?php } else { ?>

            <?php foreach ($offers as $offer) { ?>
                <div class="offer-card">
                    <div class="discount">
                        <?php echo $offer['discount']; ?>% OFF
                    </div>

                    <h3><?php echo $offer['title']; ?></h3>
                    <p><?php echo $offer['description']; ?></p>

                    <div class="price">
                        <span class="new-price">
                            <?php echo $offer['discount']; ?>% Discount
                        </span>
                    </div>

                    <?php if (!empty($offer['start_date']) || !empty($offer['end_date'])) { ?>
                        <p style="font-size: 13px; color: #777; margin-top: 8px;">
                            <?php 
                                if (!empty($offer['start_date'])) echo "From: " . $offer['start_date'] . " ";
                                if (!empty($offer['end_date'])) echo "To: " . $offer['end_date'];
                            ?>
                        </p>
                    <?php } ?>

                    <a href="Home.php">
                        <button>
                            <i class="fa-solid fa-book"></i> Shop Now
                        </button>
                    </a>
                </div>
            <?php } ?>

        <?php } ?>

    </div>
</section>

<!-- BIG OFFER -->
<section class="big-offer">
    <div>
        <h2>Weekend Book Sale!</h2>
        <p>Save up to 50% on selected books.</p>
        <a href="Home.php"><button>Shop Now</button></a>
    </div>
    <i class="fa-solid fa-book-open"></i>
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