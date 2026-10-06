
<?php

session_start();

require_once "../config.php";

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

if ($search !== '' && $category !== '') {

    $stmt = $pdo->prepare("
        SELECT b.*, c.category_name
        FROM books b
        LEFT JOIN categories c
        ON b.category_id = c.category_id
        WHERE (
            b.title LIKE :search
            OR b.author LIKE :search
            OR c.category_name LIKE :search
        )
        AND c.category_name = :category
        ORDER BY b.book_id DESC
    ");

    $stmt->execute([
        ':search' => '%' . $search . '%',
        ':category' => $category
    ]);

} elseif ($search !== '') {

    $stmt = $pdo->prepare("
        SELECT b.*, c.category_name
        FROM books b
        LEFT JOIN categories c
        ON b.category_id = c.category_id
        WHERE b.title LIKE :search
           OR b.author LIKE :search
           OR c.category_name LIKE :search
        ORDER BY b.book_id DESC
    ");

    $stmt->execute([
        ':search' => '%' . $search . '%'
    ]);

} elseif ($category !== '') {

    $stmt = $pdo->prepare("
        SELECT b.*, c.category_name
        FROM books b
        LEFT JOIN categories c
        ON b.category_id = c.category_id
        WHERE c.category_name = :category
        ORDER BY b.book_id DESC
    ");

    $stmt->execute([
        ':category' => $category
    ]);

} else {

    $stmt = $pdo->query("
        SELECT b.*, c.category_name
        FROM books b
        LEFT JOIN categories c
        ON b.category_id = c.category_id
        ORDER BY b.book_id DESC
    ");

}

$books = $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Books - Book Heaven</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="Fstyle.css">

    <style>

        /* BOOKS */

        .books {

            padding: 50px 7%;

            background: #fff;

        }

        .books .title {

            text-align: center;

            margin-bottom: 35px;

        }

        .books .title h2 {

            font-size: 30px;

            margin-bottom: 8px;

        }

        .books .title p {

            color: #666;

        }

        .book-row {

            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 25px;

        }

        .book-card {

            background: #fff;

            border-radius: 10px;

            padding: 15px;

            box-shadow: 0 2px 10px rgba(0,0,0,0.10);

            transition: 0.3s;

        }

        .book-card:hover {

            transform: translateY(-5px);

        }

        .book-card img {

            width: 100%;

            height: 280px;

            object-fit: cover;

            border-radius: 7px;

            display: block;

        }

        .no-image {

            width: 100%;

            height: 280px;

            background: #eee;

            border-radius: 7px;

            display: flex;

            justify-content: center;

            align-items: center;

            color: #777;

        }

        .book-card h3 {

            margin-top: 15px;

            font-size: 18px;

        }

        .book-author {

            margin-top: 7px;

            color: #666;

        }

        .book-category {

            margin-top: 7px;

            color: #777;

            font-size: 14px;

        }

        .book-description {

            margin-top: 10px;

            color: #666;

            font-size: 14px;

            line-height: 1.4;

        }

        .book-price {

            margin-top: 12px;

            font-size: 18px;

        }

        .book-link {

            text-decoration: none;

            color: inherit;

        }

        .search-section {

            padding: 30px 7% 0;

            background: #fff;

        }

        .search-section form {

            display: flex;

            justify-content: center;

            max-width: 600px;

            margin: auto;

        }

        .search-section input {

            flex: 1;

            padding: 13px 16px;

            border: 1px solid #ccc;

            border-radius: 6px 0 0 6px;

            outline: none;

        }

        .search-section button {

            padding: 13px 20px;

            border: none;

            background: #333;

            color: #fff;

            border-radius: 0 6px 6px 0;

            cursor: pointer;

        }

        .empty-books {

            text-align: center;

            padding: 50px;

            color: #666;

        }

        @media (max-width: 1000px) {

            .book-row {

                grid-template-columns: repeat(3, 1fr);

            }

        }

        @media (max-width: 750px) {

            .book-row {

                grid-template-columns: repeat(2, 1fr);

            }

        }

        @media (max-width: 500px) {

            .book-row {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>

<body>


<!-- HEADER -->

<header>

    <div class="logo">

        <h3>
            <i class="fa-solid fa-book"></i>
            Book Heaven
        </h3>

    </div>


    <div class="search-box">

        <form action="books.php"
              method="GET"
              style="display:flex; align-items:center;">

            <input
                type="search"
                name="search"
                class="search"
                placeholder="search for book"
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button
                type="submit"
                style="background:none; border:none; cursor:pointer;">

                <i class="fa-solid fa-magnifying-glass"></i>

            </button>

        </form>

    </div>


    <div class="menu">

        <?php if (isset($_SESSION['user_id'])) { ?>

            <?php if (
                isset($_SESSION['role']) &&
                $_SESSION['role'] === 'admin'
            ) { ?>

                <a href="../admin/index.php">

                    <i class="fa-solid fa-gauge"></i>
                    Dashboard

                </a>

            <?php } ?>

            <a href="logout.php">
                logout
            </a>

        <?php } else { ?>

            <a href="login.php">
                login
            </a>

            <a href="register.php">
                register
            </a>

        <?php } ?>


        <a href="#">

            <i class="fa-solid fa-cart-plus"></i>
            cart

        </a>

    </div>

</header>


<!-- NAVBAR -->

<nav>

    <div class="navbar">

        <ul>

            <li>

                <a href="Home.php">
                    Home
                </a>

            </li>


            <li class="category-menu">

                <a href="#" onclick="showCategories(event)">

                    Categories
                    <i class="fa-solid fa-chevron-down"></i>

                </a>


                <div class="category-dropdown">

                    <a href="books.php?category=Fiction">
                        Fiction
                    </a>

                    <a href="books.php?category=Science">
                        Science
                    </a>

                    <a href="books.php?category=Programming">
                        Programming
                    </a>

                    <a href="books.php?category=Business">
                        Business
                    </a>

                    <a href="books.php?category=Biography">
                        Biography
                    </a>

                    <a href="books.php?category=Self Help">
                        Self Help
                    </a>

                </div>

            </li>


            <li>

                <a href="offer.php">
                    Offers
                </a>

            </li>


            <li>

                <a href="About_us.php">
                    About
                </a>

            </li>


            <li>

                <a href="Contact.php">
                    Contact
                </a>

            </li>

        </ul>

    </div>

</nav>


<!-- SEARCH -->

<section class="search-section">

    <form action="books.php" method="GET">

        <input
            type="search"
            name="search"
            placeholder="Search books, authors or categories..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">

            <i class="fa-solid fa-magnifying-glass"></i>
            Search

        </button>

    </form>

</section>


<!-- BOOKS -->

<section class="books">

    <div class="title">

        <?php if ($category !== '') { ?>

            <h2>
                <?php echo htmlspecialchars($category); ?> Books
            </h2>

            <p>
                Explore books from the <?php echo htmlspecialchars($category); ?> category.
            </p>

        <?php } else { ?>

            <h2>
                All Books
            </h2>

            <p>
                Discover your next favorite book from Book Heaven.
            </p>

        <?php } ?>

    </div>


    <?php if (count($books) > 0) { ?>

        <div class="book-row">

            <?php foreach ($books as $book) { ?>

                <a
                    href="book_detail.php?id=<?php echo $book['book_id']; ?>"
                    class="book-link"
                >

                    <div class="book-card">


                        <?php if (!empty($book['image'])) { ?>

                            <img
                                src="../uploads/<?php echo htmlspecialchars($book['image']); ?>"
                                alt="<?php echo htmlspecialchars($book['title']); ?>"
                            >

                        <?php } else { ?>

                            <div class="no-image">

                                <i class="fa-solid fa-book"></i>
                                &nbsp; No Image

                            </div>

                        <?php } ?>


                        <h3>

                            <?php echo htmlspecialchars($book['title']); ?>

                        </h3>


                        <p class="book-author">

                            <i class="fa-solid fa-user"></i>

                            <?php echo htmlspecialchars($book['author']); ?>

                        </p>


                        <?php if (!empty($book['category_name'])) { ?>

                            <p class="book-category">

                                <i class="fa-solid fa-tag"></i>

                                <?php echo htmlspecialchars($book['category_name']); ?>

                            </p>

                        <?php } ?>


                        <?php if (!empty($book['description'])) { ?>

                            <p class="book-description">

                                <?php

                                echo htmlspecialchars(
                                    substr($book['description'], 0, 80)
                                );

                                ?>...

                            </p>

                        <?php } ?>


                        <h4 class="book-price">

                            Rs.
                            <?php echo number_format($book['price'], 2); ?>

                        </h4>


                    </div>

                </a>

            <?php } ?>

        </div>


    <?php } else { ?>

        <div class="empty-books">

            <i
                class="fa-solid fa-book-open"
                style="font-size:45px; margin-bottom:15px;"
            ></i>

            <h2>
                No Books Found
            </h2>

            <?php if ($search !== '') { ?>

                <p>

                    No books matched
                    "<?php echo htmlspecialchars($search); ?>"

                </p>

            <?php } elseif ($category !== '') { ?>

                <p>
                    There are no books in this category.
                </p>

            <?php } else { ?>

                <p>
                    There are currently no books available.
                </p>

            <?php } ?>

        </div>

    <?php } ?>

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

    document
        .querySelector(".category-menu")
        .classList.toggle("show");

}

</script>


</body>

</html>

