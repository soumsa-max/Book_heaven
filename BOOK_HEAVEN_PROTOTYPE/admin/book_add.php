<?php

session_start();

require_once "../config.php";
require_once "includes/admin_auth.php";

$error = "";


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

        $image_name = null;


        /* =========================
           IMAGE UPLOAD
        ========================= */

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
                        "image/png"  => "png",
                        "image/webp" => "webp"
                    ];


                    /* Check actual MIME type */

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


                        /* Upload directory */

                        $upload_directory = "../uploads/";


                        if (!is_dir($upload_directory)) {

                            mkdir(
                                $upload_directory,
                                0777,
                                true
                            );
                        }


                        /* Generate unique filename */

                        $image_name =
                            uniqid("book_", true)
                            . "."
                            . $extension;


                        $destination =
                            $upload_directory
                            . $image_name;


                        if (
                            !move_uploaded_file(
                                $tmp_name,
                                $destination
                            )
                        ) {

                            $error =
                                "Failed to save the uploaded image.";

                            $image_name = null;
                        }
                    }
                }
            }
        }


        /* =========================
           INSERT BOOK
        ========================= */

        if ($error === "") {

            $stmt = $pdo->prepare("
                INSERT INTO books
                (
                    title,
                    author,
                    category_id,
                    price,
                    stock,
                    image,
                    description
                )
                VALUES
                (
                    :title,
                    :author,
                    :category_id,
                    :price,
                    :stock,
                    :image,
                    :description
                )
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
                    $description
            ]);


            header("Location: books.php?success=added");

            exit();
        }
    }
}


/* =========================
   PAGE
========================= */

$page_title = "Add New Book";

require_once "includes/admin_header.php";

?>


<div class="page-header">

    <div>

        <h1>Add New Book</h1>

        <p>
            Add a new book to the Book Heaven catalog.
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

        <?php echo htmlspecialchars($error); ?>

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
                    echo htmlspecialchars(
                        $_POST["title"] ?? ""
                    );
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
                    echo htmlspecialchars(
                        $_POST["author"] ?? ""
                    );
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
                            ($_POST["category_id"] ?? "")
                            ==
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


            <?php if (count($categories) === 0): ?>

                <small class="form-help">

                    No categories available.
                    Please create a category first.

                </small>

            <?php endif; ?>

        </div>


        <!-- Price and Stock -->

        <div class="form-row">


            <!-- Price -->

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
                            $_POST["price"] ?? ""
                        );
                    ?>"
                    required
                >

            </div>


            <!-- Stock -->

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
                            $_POST["stock"] ?? "0"
                        );
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
                placeholder="Enter book description..."
            ><?php

                echo htmlspecialchars(
                    $_POST["description"] ?? ""
                );

            ?></textarea>

        </div>


        <!-- Image -->

        <div class="form-group">

            <label for="image">

                Book Cover

            </label>


            <input
                type="file"
                id="image"
                name="image"
                accept=".jpg,.jpeg,.png,.webp"
            >


            <small class="form-help">

                JPG, PNG or WEBP.
                Maximum size: 2 MB.

            </small>

        </div>


        <!-- Buttons -->

        <div class="form-actions">


            <button
                type="submit"
                class="btn btn-primary"
            >

                Add Book

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