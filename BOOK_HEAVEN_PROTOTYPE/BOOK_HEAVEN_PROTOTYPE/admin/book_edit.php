<?php

require_once "includes/admin_auth.php";

$error = "";

/* Get book ID */

$book_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($book_id <= 0) {
    header("Location: books.php");
    exit();
}


/* Get existing book */

$stmt = $pdo->prepare("
    SELECT *
    FROM books
    WHERE book_id = :book_id
");

$stmt->execute([
    ":book_id" => $book_id
]);

$book = $stmt->fetch();


if (!$book) {
    header("Location: books.php");
    exit();
}


/* Get categories */

$stmt = $pdo->query("
    SELECT category_id, category_name
    FROM categories
    ORDER BY category_name ASC
");

$categories = $stmt->fetchAll();


/* Handle form submission */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $category_id = $_POST["category_id"] ?? "";
    $price = $_POST["price"] ?? "";
    $stock = $_POST["stock"] ?? "";
    $description = trim($_POST["description"] ?? "");


    /* Validate */

    if (
        $title === "" ||
        $author === "" ||
        $category_id === "" ||
        $price === "" ||
        $stock === ""
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!is_numeric($price) || $price < 0) {

        $error = "Please enter a valid price.";

    } elseif (
        !filter_var($stock, FILTER_VALIDATE_INT)
        && $stock !== "0"
    ) {

        $error = "Please enter a valid stock quantity.";

    } elseif ((int)$stock < 0) {

        $error = "Stock cannot be negative.";

    } else {

        $image_name = $book["image"];


        /* Handle new image */

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

                $error = "There was a problem uploading the image.";

            } else {

                $file = $_FILES["image"];

                $file_size = $file["size"];
                $tmp_name = $file["tmp_name"];


                /* Maximum 2 MB */

                if ($file_size > 2 * 1024 * 1024) {

                    $error = "Image size must be less than 2 MB.";

                } else {

                    $allowed_types = [
                        "image/jpeg" => "jpg",
                        "image/png" => "png",
                        "image/webp" => "webp"
                    ];


                    $file_info = finfo_open(FILEINFO_MIME_TYPE);

                    $mime_type = finfo_file(
                        $file_info,
                        $tmp_name
                    );

                    finfo_close($file_info);


                    if (!isset($allowed_types[$mime_type])) {

                        $error =
                            "Only JPG, PNG and WEBP images are allowed.";

                    } else {

                        $extension =
                            $allowed_types[$mime_type];

                        $new_image_name =
                            uniqid("book_", true) .
                            "." .
                            $extension;

                        $upload_directory = "../uploads/";


                        if (!is_dir($upload_directory)) {

                            mkdir(
                                $upload_directory,
                                0777,
                                true
                            );
                        }


                        $destination =
                            $upload_directory .
                            $new_image_name;


                        if (
                            move_uploaded_file(
                                $tmp_name,
                                $destination
                            )
                        ) {

                            /*
                             * Delete old image
                             */

                            if (
                                !empty($book["image"]) &&
                                file_exists(
                                    $upload_directory .
                                    $book["image"]
                                )
                            ) {

                                unlink(
                                    $upload_directory .
                                    $book["image"]
                                );
                            }


                            $image_name =
                                $new_image_name;

                        } else {

                            $error =
                                "Failed to save the uploaded image.";
                        }
                    }
                }
            }
        }


        /* Update database */

        if ($error === "") {

            $stmt = $pdo->prepare("
                UPDATE books
                SET
                    title = :title,
                    author = :author,
                    category_id = :category_id,
                    price = :price,
                    stock = :stock,
                    image = :image,
                    description = :description
                WHERE book_id = :book_id
            ");


            $stmt->execute([

                ":title" => $title,

                ":author" => $author,

                ":category_id" => $category_id,

                ":price" => $price,

                ":stock" => (int)$stock,

                ":image" => $image_name,

                ":description" => $description,

                ":book_id" => $book_id
            ]);


            header("Location: books.php?success=updated");

            exit();
        }
    }
}


/*
 * If validation failed, use submitted values.
 * Otherwise use database values.
 */

$form_title =
    $_POST["title"] ??
    $book["title"];

$form_author =
    $_POST["author"] ??
    $book["author"];

$form_category =
    $_POST["category_id"] ??
    $book["category_id"];

$form_price =
    $_POST["price"] ??
    $book["price"];

$form_stock =
    $_POST["stock"] ??
    $book["stock"];

$form_description =
    $_POST["description"] ??
    $book["description"];


require_once "includes/admin_header.php";

?>


<div class="page-header">

    <div>

        <h1>Edit Book</h1>

        <p>
            Update book information and stock.
        </p>

    </div>


    <a
        href="books.php"
        class="btn btn-secondary"
    >
        Back to Books
    </a>

</div>


<?php if ($error !== ""): ?>

    <div class="alert alert-error">

        <?php
        echo htmlspecialchars($error);
        ?>

    </div>

<?php endif; ?>


<div class="form-container">

    <form
        method="POST"
        enctype="multipart/form-data"
        class="admin-form"
    >


        <!-- Title -->

        <div class="form-group">

            <label for="title">

                Book Title
                <span>*</span>

            </label>


            <input
                type="text"
                id="title"
                name="title"
                value="<?php
                    echo htmlspecialchars($form_title);
                ?>"
                required
            >

        </div>


        <!-- Author -->

        <div class="form-group">

            <label for="author">

                Author
                <span>*</span>

            </label>


            <input
                type="text"
                id="author"
                name="author"
                value="<?php
                    echo htmlspecialchars($form_author);
                ?>"
                required
            >

        </div>


        <!-- Category -->

        <div class="form-group">

            <label for="category_id">

                Category
                <span>*</span>

            </label>


            <select
                id="category_id"
                name="category_id"
                required
            >

                <option value="">
                    Select Category
                </option>


                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?php
                            echo $category["category_id"];
                        ?>"
                        <?php

                        echo (
                            $form_category ==
                            $category["category_id"]
                        )
                            ? "selected"
                            : "";

                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $category["category_name"]
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- Price and Stock -->

        <div class="form-row">


            <div class="form-group">

                <label for="price">

                    Price (Rs.)
                    <span>*</span>

                </label>


                <input
                    type="number"
                    id="price"
                    name="price"
                    step="0.01"
                    min="0"
                    value="<?php
                        echo htmlspecialchars($form_price);
                    ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="stock">

                    Stock
                    <span>*</span>

                </label>


                <input
                    type="number"
                    id="stock"
                    name="stock"
                    min="0"
                    value="<?php
                        echo htmlspecialchars($form_stock);
                    ?>"
                    required
                >

            </div>


        </div>


        <!-- Description -->

        <div class="form-group">

            <label for="description">

                Description

            </label>


            <textarea
                id="description"
                name="description"
                rows="6"
            ><?php

                echo htmlspecialchars(
                    $form_description
                );

            ?></textarea>

        </div>


        <!-- Current Image -->

        <div class="form-group">

            <label>
                Current Cover
            </label>


            <?php if (!empty($book["image"])): ?>

                <div>

                    <img
                        src="../uploads/<?php
                            echo htmlspecialchars(
                                $book["image"]
                            );
                        ?>"
                        alt="Current Book Cover"
                        class="edit-book-image"
                    >

                </div>

            <?php else: ?>

                <p class="form-help">
                    No cover image uploaded.
                </p>

            <?php endif; ?>

        </div>


        <!-- New Image -->

        <div class="form-group">

            <label for="image">

                Replace Cover

            </label>


            <input
                type="file"
                id="image"
                name="image"
                accept=".jpg,.jpeg,.png,.webp"
            >


            <small class="form-help">

                Leave empty to keep the current cover.
                JPG, PNG or WEBP. Maximum 2 MB.

            </small>

        </div>


        <!-- Buttons -->

        <div class="form-actions">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Update Book
            </button>


            <a
                href="books.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </div>


    </form>

</div>


<?php require_once "includes/admin_footer.php"; ?>