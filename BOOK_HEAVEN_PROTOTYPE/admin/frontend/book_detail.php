<?php
session_start();
require_once "config.php";

// Get book id from URL
$book_id = 0;
if (isset($_GET['id'])) {
    $book_id = (int)$_GET['id'];
}

// Get the book details
$sql = "SELECT * FROM books WHERE book_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $book_id]);
$book = $stmt->fetch();

// If book not found
if (!$book) {
    echo "<h2>Book not found</h2>";
    echo "<a href='Home.php'>Go back to Home</a>";
    exit;
}

// Handle Review form
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    $name = trim($_POST['name']);
    $review = trim($_POST['review']);

    if ($name != "" && $review != "") {
        $sql = "INSERT INTO reviews (book_id, name, review) VALUES (:book_id, :name, :review)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':book_id' => $book_id,
            ':name' => $name,
            ':review' => $review
        ]);
        $message = "Thank you! Your review has been added.";
    } else {
        $message = "Please fill both name and review.";
    }
}

// Get all reviews for this book
$sql = "SELECT * FROM reviews WHERE book_id = :id ORDER BY review_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $book_id]);
$reviews = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $book['title']; ?> - Book Heaven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="Fstyle.css">
    <style>
        .detail-container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            padding: 30px;
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }
        .detail-image {
            width: 280px;
            flex-shrink: 0;
        }
        .detail-image img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .detail-info {
            flex: 1;
            min-width: 280px;
        }
        .detail-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .detail-info .author {
            color: #555;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .detail-info .price {
            color: #0d6efd;
            font-size: 26px;
            font-weight: bold;
            margin: 15px 0;
        }
        .detail-info .description {
            color: #444;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .btn-order {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 14px 30px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-order:hover {
            background: #0b5ed7;
        }
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 14px 25px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .review-section {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto 60px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .review-section h2 {
            margin-bottom: 20px;
        }
        .review-form input,
        .review-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }
        .review-form textarea {
            height: 100px;
            resize: vertical;
        }
        .review-form button {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }
        .review-box {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .review-box:last-child {
            border-bottom: none;
        }
        .review-box .name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .review-box .text {
            color: #444;
            line-height: 1.5;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- HEADER (same as home) -->
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
        <a href="login.php">login</a>
        <a href="register.php">register</a>
        <a href="#"><i class="fa-solid fa-cart-plus"></i> cart</a>
    </div>
</header>

<!-- NAVBAR -->
<nav>
    <div class="navbar">
        <ul>
            <li><a href="Home.php">Home</a></li>
            <li><a href="Home.php">Categories</a></li>
            <li><a href="offer.php">Offers</a></li>
            <li><a href="About_us.php">About</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
    </div>
</nav>

<!-- BOOK DETAIL -->
<div class="detail-container">
    <div class="detail-image">
        <img src="<?php 
            if (!empty($book['image'])) {
                echo '../backend/Book_heaven/uploads/' . $book['image'];
            } else {
                echo 'https://via.placeholder.com/280x400?text=No+Image';
            }
        ?>" alt="Book Cover">
    </div>

    <div class="detail-info">
        <h1><?php echo $book['title']; ?></h1>
        <p class="author">by <?php echo $book['author']; ?></p>
        <p class="price">Rs. <?php echo number_format($book['price'], 2); ?></p>
        <p class="description"><?php echo $book['description']; ?></p>

        <p><strong>Stock:</strong> <?php echo $book['stock']; ?> available</p>

        <br>
        <button class="btn-order" onclick="alert('Order feature coming soon!')">
            <i class="fa-solid fa-cart-plus"></i> Order Now
        </button>
        <a href="Home.php" class="btn-back">Back to Home</a>
    </div>
</div>

<!-- REVIEW SECTION -->
<div class="review-section">
    <h2>Customer Reviews</h2>

    <?php if ($message != "") { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <!-- Review Form -->
    <form method="POST" class="review-form">
        <input type="text" name="name" placeholder="Your Name" required>
        <textarea name="review" placeholder="Write your review here..." required></textarea>
        <button type="submit" name="submit_review">Submit Review</button>
    </form>

    <hr style="margin: 30px 0;">

    <!-- Show Reviews -->
    <?php if (count($reviews) == 0) { ?>
        <p style="color:#666;">No reviews yet. Be the first to review!</p>
    <?php } else { ?>
        <?php foreach ($reviews as $r) { ?>
            <div class="review-box">
                <div class="name"><?php echo $r['name']; ?></div>
                <div class="text"><?php echo $r['review']; ?></div>
            </div>
        <?php } ?>
    <?php } ?>
</div>

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

</body>
</html>