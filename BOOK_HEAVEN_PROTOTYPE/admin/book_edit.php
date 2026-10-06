<?php

session_start();

require_once "../config.php";
require_once "includes/admin_auth.php";

$error = "";


/* =========================
   GET BOOK ID
========================= */

$book_id = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;


if ($book_id <= 0) {

    header("Location: books.php");
    exit();

}


/* =========================
   GET EXISTING BOOK
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM books
    WHERE book_id = :book_id
");

$stmt->execute([
    ":book_id" => $book_id
]);

$book = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$book) {

    header("Location: books.php");
    exit();

}


/* =========================
   GET CATEGORIES
========================= */

$stmt = $pdo->query("
    SELECT category_id, category_name
    FROM categories
    ORDER BY category_name ASC
");

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   HANDLE FORM SUBMISSION
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $category_id = $_POST["category_id"] ?? "";
    $price = $_POST["price"] ?? "";
    $stock = $_POST["stock"] ?? "";
    $description = trim($_POST["description"] ?? "");


    /* =========================
       VALIDATION
    ========================= */

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
        filter_var($stock, FILTER_VALIDATE_INT) === false &&
        $stock !== "0"
    ) {

        $error = "Please enter a valid stock quantity.";

    } elseif ((int)$stock < 0) {

        $error = "Stock cannot be negative.";

    } else {

        /* Keep current image unless a new one is uploaded */

        $image_name = $book["image"];


        /* =========================
           HANDLE NEW IMAGE
        ========================= */

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if (
                $_FILES["image"]["error"] !== UPLOAD_ERR_OK
            ) {

                $error =
                    "There was a problem uploading the image.";

            } else {

                $file = $_FILES["image"];

                $file_size = $file["size"];
                $tmp_name = $file["tmp_name"];


                /* Maximum 2 MB */

                if ($file_size > 2 * 1024 * 1024) {

                    $error =
                        "Image size must be less than 2 MB.";

                } else {

                    $allowed_types = [

                        "image/jpeg" => "jpg",

                        "image/png" => "png",

                        "image/webp" => "webp"

                    ];


                    /* Check actual file type */

                    $file_info =
                        finfo_open(FILEINFO_MIME_TYPE);

                    $mime_type =
                        finfo_file(
                            $file_info,
                            $tmp_name
                        );

                    finfo_close($file_info);


                    if (
                        !isset(
                            $allowed_types[$mime_type]
                        )
                    ) {

                        $error =
                            "Only JPG, PNG and WEBP images are allowed.";

                    } else {

                        $extension =
                            $allowed_types[$mime_type];


                        /* Upload directory */

                        $upload_directory =
                            "../uploads/";


                        if (
                            !is_dir(
                                $upload_directory
                            )
                        ) {

                            mkdir(
                                $upload_directory,
                                0777,
                                true
                            );
                        }


                        /* Generate unique filename */

                        $new_image_name =
                            uniqid(
                                "book_",
                                true
                            )
                            . "."
                            . $extension;


                        $destination =
                            $upload_directory
                            . $new_image_name;


                        /* Move new image */

                        if (
                            move_uploaded_file(
                                $tmp_name,
                                $destination
                            )
                        ) {


                            /* =========================
                               DELETE OLD IMAGE
                            ========================= */

                            if (
                                !empty(
                                    $book["image"]
                                )
                            ) {

                                $old_image_path =
                                    $upload_directory
                                    . $book["image"];


                                if (
                                    file_exists(
                                        $old_image_path
                                    )
                                ) {

                                    unlink(
                                        $old_image_path
                                    );
                                }
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


        /* =========================
           UPDATE DATABASE
        ========================= */

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

                ":title" =>
                    $title,

                ":author" =>
                    $author,

                ":category_id" =>
                    $category_id,

                ":price" =>
                    $price,

                ":stock" =>
                    (int)$stock,

                ":image" =>
                    $image_name,

                ":description" =>
                    $description,

                ":book_id" =>
                    $book_id

            ]);


            header(
                "Location: books.php?success=updated"
            );

            exit();

        }
    }
}


/* =========================
   FORM VALUES
========================= */

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


/* =========================
   ADMIN HEADER
========================= */

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


        <!-- =========================
             TITLE
        ========================= -->

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
                    echo htmlspecialchars(
                        $form_title
                    );
                ?>"
                required
            >

        </div>


        <!-- =========================
             AUTHOR
        ========================= -->

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
                    echo htmlspecialchars(
                        $form_author
                    );
                ?>"
                required
            >

        </div>


        <!-- =========================
             CATEGORY
        ========================= -->

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


                <?php foreach (
                    $categories as $category
                ): ?>

                    <option
                        value="<?php
                            echo $category[
                                "category_id"
                            ];
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
                            $category[
                                "category_name"
                            ]
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <?php if (
                count($categories) === 0
            ): ?>

                <small class="form-help">

                    No categories available.
                    Please create a category first.

                </small>

            <?php endif; ?>

        </div>


        <!-- =========================
             PRICE AND STOCK
        ========================= -->

        <div class="form-row">


            <!-- PRICE -->

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
                        echo htmlspecialchars(
                            $form_price
                        );
                    ?>"
                    required
                >

            </div>


            <!-- STOCK -->

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
                        echo htmlspecialchars(
                            $form_stock
                        );
                    ?>"
                    required
                >

            </div>


        </div>


        <!-- =========================
             DESCRIPTION
        ========================= -->

        <div class="form-group">

            <label for="description">

                Description

            </label>


            <textarea
                id="description"
                name="description"
                rows="6"
                placeholder="Enter book description..."
            ><?php

                echo htmlspecialchars(
                    $form_description
                );

            ?></textarea>

        </div>


        <!-- =========================
             CURRENT IMAGE
        ========================= -->

        <div class="form-group">

            <label>

                Current Cover

            </label>


            <?php if (
                !empty($book["image"])
            ): ?>

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


        <!-- =========================
             NEW IMAGE
        ========================= -->

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
                JPG, PNG or WEBP.
                Maximum 2 MB.

            </small>

        </div>


        <!-- =========================
             BUTTONS
        ========================= -->

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


<?php

require_once "includes/admin_footer.php";

?>