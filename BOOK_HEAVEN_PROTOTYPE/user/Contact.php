<?php
session_start();
require_once "../config.php";
$search = $_GET['search'] ?? '';

// ======================
// HANDLE FORM SUBMIT
// ======================
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name == "" || $email == "" || $subject == "" || $message == "") {
        $error = "Please fill all fields.";
    } else {
        $sql = "INSERT INTO contact_messages (name, email, subject, message) 
                VALUES (:name, :email, :subject, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);
        $success = "Thank you! Your message has been sent successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact</title>
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
            <input type="search" name="search" class="search" 
                   placeholder="search for book"
                   value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" style="background:none; border:none; cursor:pointer;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <div class="menu">
        <?php if (isset($_SESSION['user_id'])) { ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                <a href="../admin/index.php">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
            <?php } ?>

            <a href="logout.php">logout</a>

        <?php } else { ?>

            <a href="login.php">login</a>
            <a href="register.php">register</a>

        <?php } ?>

        <a href="#">
            <i class="fa-solid fa-cart-plus"></i> cart
        </a>
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

<!-- BANNER -->
<section class="contact-banner">
    <h1>Contact Us</h1>
    <p>We would love to hear from you.</p>
</section>

<!-- CONTACT SECTION -->
<section class="contact-section">

    <!-- LEFT SIDE (static) -->
    <div class="contact-info">
        <h2>Get In Touch</h2>
        <p>Have a question, suggestion or problem? Feel free to contact us.</p>

        <div class="contact-box">
            <i class="fa-solid fa-location-dot"></i>
            <div>
                <h3>Address</h3>
                <p>Kathmandu, Nepal</p>
            </div>
        </div>

        <div class="contact-box">
            <i class="fa-solid fa-phone"></i>
            <div>
                <h3>Phone</h3>
                <p>+977-9800000000</p>
            </div>
        </div>

        <div class="contact-box">
            <i class="fa-solid fa-envelope"></i>
            <div>
                <h3>Email</h3>
                <p>bookstore@email.com</p>
            </div>
        </div>

        <div class="contact-box">
            <i class="fa-solid fa-clock"></i>
            <div>
                <h3>Opening Hours</h3>
                <p>Sunday - Friday</p>
                <p>10:00 AM - 6:00 PM</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE (form → saves to database) -->
    <div class="contact-form">
        <h2>Send Us A Message</h2>

        <?php if ($success != "") { ?>
            <p style="color: green; margin-bottom: 15px; font-weight: bold;">
                <?php echo $success; ?>
            </p>
        <?php } ?>

        <?php if ($error != "") { ?>
            <p style="color: red; margin-bottom: 15px;">
                <?php echo $error; ?>
            </p>
        <?php } ?>

        <form method="POST">
            <label>Name</label>
            <input type="text" name="name" placeholder="Enter your name" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Subject</label>
            <input type="text" name="subject" placeholder="Enter subject" required>

            <label>Message</label>
            <textarea name="message" placeholder="Write your message" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>

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