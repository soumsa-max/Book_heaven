<?php
session_start();
require_once "config.php";

// ======================
// SEARCH + CATEGORY
// ======================
$search = "";
$category = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if (isset($_GET['category'])) {
    $category = trim($_GET['category']);
}

// ======================
// GET BOOKS
// ======================
if ($search != "") {
    $sql = "SELECT * FROM books 
            WHERE title LIKE :search 
               OR author LIKE :search 
               OR description LIKE :search
            ORDER BY book_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => "%$search%"]);
    $books = $stmt->fetchAll();

} elseif ($category != "") {
    $sql = "SELECT books.* 
            FROM books 
            JOIN categories ON books.category_id = categories.category_id
            WHERE categories.category_name = :category
            ORDER BY books.book_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':category' => $category]);
    $books = $stmt->fetchAll();

} else {
    $sql = "SELECT * FROM books ORDER BY book_id DESC LIMIT 5";
    $stmt = $pdo->query($sql);
    $books = $stmt->fetchAll();
}

// Second Hand books
$secondHand = [];
if ($search == "" && $category == "") {
    $sql = "SELECT books.* 
            FROM books 
            JOIN categories ON books.category_id = categories.category_id
            WHERE categories.category_name = 'Second Hand Books'
            ORDER BY books.book_id DESC
            LIMIT 5";
    $stmt = $pdo->query($sql);
    $secondHand = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Book Heaven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="Fstyle.css">
</head>
<body>

<!-- ========== HEADER ========== -->
<header>
    <div class="logo">
        <h3><i class="fa-solid fa-book"></i> Book Heaven</h3>
    </div>

    <div class="search-box">
        <form action="Home.php" method="GET" style="display:flex; align-items:center;">
            <input type="search" name="search" class="search" 
                   placeholder="search for book"
                   value="<?php echo $search; ?>">
            <button type="submit" style="background:none; border:none; cursor:pointer;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <div class="menu">
        <?php if (isset($_SESSION['user_id'])) { ?>
            <a href="../backend/Book_heaven/user/logout.php">logout</a>
        <?php } else { ?>
            <a href="../backend/Book_heaven/user/login.php">login</a>
            <a href="../backend/Book_heaven/user/register.php">register</a>
        <?php } ?>
        <a href="#"><i class="fa-solid fa-cart-plus"></i> cart</a>
    </div>
</header>

<!-- ========== NAVBAR ========== -->
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

<!-- ========== BANNER ========== -->
<section class="banner">
    <button class="left-arrow" onclick="previousBanner()">&#10094;</button>
    <div class="hero-contain">
        <h1 id="banner-title">Best Selling Books</h1>
        <p id="banner-text">Up to 50% OFF</p>
    </div>
    <button class="right-arrow" onclick="nextBanner()">&#10095;</button>
    <div class="banner-dots">
        <span class="dot active" onclick="showBanner(0)"></span>
        <span class="dot" onclick="showBanner(1)"></span>
        <span class="dot" onclick="showBanner(2)"></span>
    </div>
</section>


<?php if ($search != "") { ?>

    <!-- SEARCH RESULTS -->
    <section class="books">
        <div class="title">
            <h2>Search results for "<?php echo $search; ?>"</h2>
            <a href="Home.php">Clear search</a>
        </div>
        <div class="book-row">
            <?php if (count($books) == 0) { ?>
                <p style="padding:20px; color:#666;">No books found.</p>
            <?php } else { ?>
                <?php foreach ($books as $book) { ?>
                    <a href="book_detail.php?id=<?php echo $book['book_id']; ?>" style="text-decoration:none; color:inherit;">
                        <div class="card">
                            <img src="<?php 
                                if (!empty($book['image'])) {
                                    echo '../backend/Book_heaven/uploads/' . $book['image'];
                                } else {
                                    echo 'https://via.placeholder.com/200x280?text=No+Image';
                                }
                            ?>" alt="Book">
                            <h3><?php echo $book['title']; ?></h3>
                            <p><?php echo $book['author']; ?></p>
                            <p><?php echo substr($book['description'], 0, 50); ?>...</p>
                            <h4>Rs.<?php echo number_format($book['price'], 2); ?></h4>
                        </div>
                    </a>
                <?php } ?>
            <?php } ?>
        </div>
    </section>

<?php } elseif ($category != "") { ?>

    <!-- CATEGORY BOOKS -->
    <section class="books">
        <div class="title">
            <h2><?php echo $category; ?> Books</h2>
            <a href="Home.php">Back to Home</a>
        </div>
        <div class="book-row">
            <?php if (count($books) == 0) { ?>
                <p style="padding:20px; color:#666;">No books found in this category.</p>
            <?php } else { ?>
                <?php foreach ($books as $book) { ?>
                    <a href="book_detail.php?id=<?php echo $book['book_id']; ?>" style="text-decoration:none; color:inherit;">
                        <div class="card">
                            <img src="<?php 
                                if (!empty($book['image'])) {
                                    echo '../backend/Book_heaven/uploads/' . $book['image'];
                                } else {
                                    echo 'https://via.placeholder.com/200x280?text=No+Image';
                                }
                            ?>" alt="Book">
                            <h3><?php echo $book['title']; ?></h3>
                            <p><?php echo $book['author']; ?></p>
                            <p><?php echo substr($book['description'], 0, 50); ?>...</p>
                            <h4>Rs.<?php echo number_format($book['price'], 2); ?></h4>
                        </div>
                    </a>
                <?php } ?>
            <?php } ?>
        </div>
    </section>

<?php } else { ?>

    <!-- NEW RELEASE -->
    <section class="books">
        <div class="title">
            <h2>New release</h2>
            <a href="#">view all &#10095;</a>
        </div>
        <div class="book-row">
            <?php if (count($books) == 0) { ?>
                <p style="padding:20px; color:#666;">No books yet.</p>
            <?php } else { ?>
                <?php foreach ($books as $book) { ?>
                    <a href="book_detail.php?id=<?php echo $book['book_id']; ?>" style="text-decoration:none; color:inherit;">
                        <div class="card">
                            <img src="<?php 
                                if (!empty($book['image'])) {
                                    echo '../backend/Book_heaven/uploads/' . $book['image'];
                                } else {
                                    echo 'https://via.placeholder.com/200x280?text=No+Image';
                                }
                            ?>" alt="Book">
                            <h3><?php echo $book['title']; ?></h3>
                            <p><?php echo $book['author']; ?></p>
                            <p><?php echo substr($book['description'], 0, 50); ?>...</p>
                            <h4>Rs.<?php echo number_format($book['price'], 2); ?></h4>
                        </div>
                    </a>
                <?php } ?>
            <?php } ?>
        </div>
    </section>

    <!-- MOST VIEW -->
    <section class="books">
        <div class="title">
            <h2>Most View</h2>
            <a href="#">view all &#10095;</a>
        </div>
        <div class="book-row">
            <?php foreach ($books as $book) { ?>
                <a href="book_detail.php?id=<?php echo $book['book_id']; ?>" style="text-decoration:none; color:inherit;">
                    <div class="card">
                        <img src="<?php 
                            if (!empty($book['image'])) {
                                echo '../backend/Book_heaven/uploads/' . $book['image'];
                            } else {
                                echo 'https://via.placeholder.com/200x280?text=No+Image';
                            }
                        ?>" alt="Book">
                        <h3><?php echo $book['title']; ?></h3>
                        <p><?php echo $book['author']; ?></p>
                        <p><?php echo substr($book['description'], 0, 50); ?>...</p>
                        <h4>Rs.<?php echo number_format($book['price'], 2); ?></h4>
                    </div>
                </a>
            <?php } ?>
        </div>
    </section>

    <!-- MOST BUY -->
    <section class="books">
        <div class="title">
            <h2>Most Buy</h2>
            <a href="#">view all &#10095;</a>
        </div>
        <div class="book-row">
            <?php foreach ($books as $book) { ?>
                <a href="book_detail.php?id=<?php echo $book['book_id']; ?>" style="text-decoration:none; color:inherit;">
                    <div class="card">
                        <img src="<?php 
                            if (!empty($book['image'])) {
                                echo '../backend/Book_heaven/uploads/' . $book['image'];
                            } else {
                                echo 'https://via.placeholder.com/200x280?text=No+Image';
                            }
                        ?>" alt="Book">
                        <h3><?php echo $book['title']; ?></h3>
                        <p><?php echo $book['author']; ?></p>
                        <p><?php echo substr($book['description'], 0, 50); ?>...</p>
                        <h4>Rs.<?php echo number_format($book['price'], 2); ?></h4>
                    </div>
                </a>
            <?php } ?>
        </div>
    </section>

    <!-- SECOND HAND -->
    <section class="books">
        <div class="title">
            <h2>Second Hand</h2>
            <a href="#">view all &#10095;</a>
        </div>
        <div class="book-row">
            <?php if (count($secondHand) == 0) { ?>
                <p style="padding:20px; color:#666;">No second hand books yet.</p>
            <?php } else { ?>
                <?php foreach ($secondHand as $book) { ?>
                    <a href="book_detail.php?id=<?php echo $book['book_id']; ?>" style="text-decoration:none; color:inherit;">
                        <div class="card">
                            <img src="<?php 
                                if (!empty($book['image'])) {
                                    echo '../backend/Book_heaven/uploads/' . $book['image'];
                                } else {
                                    echo 'https://via.placeholder.com/200x280?text=No+Image';
                                }
                            ?>" alt="Book">
                            <h3><?php echo $book['title']; ?></h3>
                            <p><?php echo $book['author']; ?></p>
                            <p><?php echo substr($book['description'], 0, 50); ?>...</p>
                            <h4>Rs.<?php echo number_format($book['price'], 2); ?></h4>
                        </div>
                    </a>
                <?php } ?>
            <?php } ?>
        </div>
    </section>

<?php } ?>

<!-- ========== FOOTER ========== -->
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

let currentBanner = 0;
let banners = [
    {title: "Best Selling Books", text: "Up to 50% OFF"},
    {title: "New Books Are Here", text: "Discover our latest releases"},
    {title: "Weekend Book Sale", text: "Save up to 50% on selected books"}
];

function showBanner(n) {
    currentBanner = n;
    document.getElementById("banner-title").innerText = banners[n].title;
    document.getElementById("banner-text").innerText = banners[n].text;
    document.querySelectorAll(".dot").forEach(d => d.classList.remove("active"));
    document.querySelectorAll(".dot")[n].classList.add("active");
}

function nextBanner() {
    currentBanner = (currentBanner + 1) % banners.length;
    showBanner(currentBanner);
}

function previousBanner() {
    currentBanner = (currentBanner - 1 + banners.length) % banners.length;
    showBanner(currentBanner);
}

setInterval(nextBanner, 4000);
</script>

</body>
</html>