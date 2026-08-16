<?php

require_once "includes/admin_auth.php";

/* Search */
$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

/* Fetch books */
if ($search !== "") {

    $stmt = $pdo->prepare("
        SELECT 
            books.*,
            categories.category_name
        FROM books
        LEFT JOIN categories
            ON books.category_id = categories.category_id
        WHERE books.title LIKE :search
           OR books.author LIKE :search
           OR categories.category_name LIKE :search
        ORDER BY books.book_id DESC
    ");

    $stmt->execute([
        ':search' => "%$search%"
    ]);

} else {

    $stmt = $pdo->query("
        SELECT 
            books.*,
            categories.category_name
        FROM books
        LEFT JOIN categories
            ON books.category_id = categories.category_id
        ORDER BY books.book_id DESC
    ");
}

$books = $stmt->fetchAll();

require_once "includes/admin_header.php";

?>

<?php if (isset($_GET["success"])): ?>

    <?php if ($_GET["success"] === "deleted"): ?>

        <div class="alert alert-success">
            Book deleted successfully.
        </div>

    <?php elseif ($_GET["success"] === "added"): ?>

        <div class="alert alert-success">
            Book added successfully.
        </div>

    <?php elseif ($_GET["success"] === "updated"): ?>

        <div class="alert alert-success">
            Book updated successfully.
        </div>

    <?php endif; ?>

<?php endif; ?>


<?php if (isset($_GET["error"])): ?>

    <?php if ($_GET["error"] === "ordered"): ?>

        <div class="alert alert-error">
            This book cannot be deleted because it has already been ordered.
        </div>

    <?php elseif ($_GET["error"] === "delete_failed"): ?>

        <div class="alert alert-error">
            Failed to delete the book.
        </div>

    <?php endif; ?>

<?php endif; ?>


<div class="page-header">

    <div>
        <h1>Manage Books</h1>
        <p>View, search, edit and delete books.</p>
    </div>

    <a href="book_add.php" class="btn btn-primary">
        + Add New Book
    </a>

</div>


<!-- Search -->

<div class="search-box">

    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search by title, author or category..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit" class="btn btn-primary">
            Search
        </button>

        <?php if ($search !== ""): ?>

            <a href="books.php" class="btn btn-secondary">
                Clear
            </a>

        <?php endif; ?>

    </form>

</div>


<!-- Books Table -->

<div class="table-container">

    <table class="admin-table">

        <thead>

            <tr>

                <th>ID</th>

                <th>Cover</th>

                <th>Title</th>

                <th>Author</th>

                <th>Category</th>

                <th>Price</th>

                <th>Stock</th>

                <th>Actions</th>

            </tr>

        </thead>


        <tbody>

        <?php if (count($books) > 0): ?>

            <?php foreach ($books as $book): ?>

                <tr>

                    <td>
                        <?php echo $book['book_id']; ?>
                    </td>


                    <!-- Cover -->

                    <td>

                        <?php if (!empty($book['image'])): ?>

                            <img
                                src="../uploads/<?php echo htmlspecialchars($book['image']); ?>"
                                alt="Book Cover"
                                class="book-cover"
                            >

                        <?php else: ?>

                            <div class="no-image">
                                No Image
                            </div>

                        <?php endif; ?>

                    </td>


                    <!-- Title -->

                    <td>
                        <?php echo htmlspecialchars($book['title']); ?>
                    </td>


                    <!-- Author -->

                    <td>
                        <?php echo htmlspecialchars($book['author']); ?>
                    </td>


                    <!-- Category -->

                    <td>

                        <?php

                        echo !empty($book['category_name'])
                            ? htmlspecialchars($book['category_name'])
                            : "Uncategorized";

                        ?>

                    </td>


                    <!-- Price -->

                    <td>
                        Rs.
                        <?php echo number_format($book['price'], 2); ?>
                    </td>


                    <!-- Stock -->

                    <td>

                        <?php if ($book['stock'] > 0): ?>

                            <span class="stock-available">
                                <?php echo $book['stock']; ?>
                            </span>

                        <?php else: ?>

                            <span class="stock-out">
                                Out of Stock
                            </span>

                        <?php endif; ?>

                    </td>


                    <!-- Actions -->

                    <td>

                        <div class="action-buttons">

                            <a
                                href="book_edit.php?id=<?php echo $book['book_id']; ?>"
                                class="btn btn-edit"
                            >
                                Edit
                            </a>


                            <a
                                href="book_delete.php?id=<?php echo $book['book_id']; ?>"
                                class="btn btn-delete"
                                onclick="return confirm('Are you sure you want to delete this book?');"
                            >
                                Delete
                            </a>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>


        <?php else: ?>

            <tr>

                <td colspan="8" class="no-data">
                    No books found.
                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>


<?php require_once "includes/admin_footer.php"; ?>