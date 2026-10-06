<?php

require_once "includes/admin_auth.php";

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| ADD CATEGORY
|--------------------------------------------------------------------------
*/

if (isset($_POST["add_category"])) {

    $category_name = trim($_POST["category_name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($category_name === "") {

        $error = "Category name is required.";

    } else {

        /* Check duplicate category */

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM categories
            WHERE category_name = :category_name
        ");

        $stmt->execute([
            ":category_name" => $category_name
        ]);

        $exists = $stmt->fetchColumn();

        if ($exists > 0) {

            $error = "This category already exists.";

        } else {

            $stmt = $pdo->prepare("
                INSERT INTO categories
                (
                    category_name,
                    description
                )
                VALUES
                (
                    :category_name,
                    :description
                )
            ");

            $stmt->execute([
                ":category_name" => $category_name,
                ":description" => $description
            ]);

            $success = "Category added successfully.";
        }
    }
}


/*
|--------------------------------------------------------------------------
| UPDATE CATEGORY
|--------------------------------------------------------------------------
*/

if (isset($_POST["update_category"])) {

    $category_id = (int)($_POST["category_id"] ?? 0);
    $category_name = trim($_POST["category_name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($category_id <= 0) {

        $error = "Invalid category.";

    } elseif ($category_name === "") {

        $error = "Category name is required.";

    } else {

        /* Check duplicate category */

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM categories
            WHERE category_name = :category_name
            AND category_id != :category_id
        ");

        $stmt->execute([
            ":category_name" => $category_name,
            ":category_id" => $category_id
        ]);

        $exists = $stmt->fetchColumn();

        if ($exists > 0) {

            $error = "Another category with this name already exists.";

        } else {

            $stmt = $pdo->prepare("
                UPDATE categories
                SET
                    category_name = :category_name,
                    description = :description
                WHERE category_id = :category_id
            ");

            $stmt->execute([
                ":category_name" => $category_name,
                ":description" => $description,
                ":category_id" => $category_id
            ]);

            $success = "Category updated successfully.";
        }
    }
}


/*
|--------------------------------------------------------------------------
| DELETE CATEGORY
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $category_id = (int)$_GET["delete"];

    if ($category_id > 0) {

        /*
         * Because books.category_id uses
         * ON DELETE SET NULL, deleting the category
         * will not delete the books.
         */

        $stmt = $pdo->prepare("
            DELETE FROM categories
            WHERE category_id = :category_id
        ");

        $stmt->execute([
            ":category_id" => $category_id
        ]);

        $success = "Category deleted successfully.";
    }
}


/*
|--------------------------------------------------------------------------
| GET CATEGORY FOR EDIT
|--------------------------------------------------------------------------
*/

$edit_category = null;

if (isset($_GET["edit"])) {

    $category_id = (int)$_GET["edit"];

    if ($category_id > 0) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM categories
            WHERE category_id = :category_id
        ");

        $stmt->execute([
            ":category_id" => $category_id
        ]);

        $edit_category = $stmt->fetch();
    }
}


/*
|--------------------------------------------------------------------------
| FETCH ALL CATEGORIES
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        categories.category_id,
        categories.category_name,
        categories.description,
        COUNT(books.book_id) AS book_count
    FROM categories
    LEFT JOIN books
        ON categories.category_id = books.category_id
    GROUP BY
        categories.category_id,
        categories.category_name,
        categories.description
    ORDER BY categories.category_name ASC
");

$categories = $stmt->fetchAll();


require_once "includes/admin_header.php";

?>


<div class="page-header">

    <div>

        <h1>Manage Categories</h1>

        <p>
            Add, edit and delete book categories.
        </p>

    </div>

</div>


<!-- Messages -->

<?php if ($error !== ""): ?>

    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>

<?php endif; ?>


<?php if ($success !== ""): ?>

    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
    </div>

<?php endif; ?>


<div class="category-layout">


    <!-- ADD / EDIT FORM -->

    <div class="category-form">

        <?php if ($edit_category): ?>

            <h2>Edit Category</h2>

        <?php else: ?>

            <h2>Add Category</h2>

        <?php endif; ?>


        <form method="POST">


            <?php if ($edit_category): ?>

                <input
                    type="hidden"
                    name="category_id"
                    value="<?php
                        echo $edit_category["category_id"];
                    ?>"
                >

            <?php endif; ?>


            <div class="form-group">

                <label for="category_name">

                    Category Name
                    <span>*</span>

                </label>


                <input
                    type="text"
                    id="category_name"
                    name="category_name"
                    value="<?php

                        echo $edit_category
                            ? htmlspecialchars(
                                $edit_category["category_name"]
                            )
                            : "";

                    ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="description">

                    Description

                </label>


                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Enter category description..."
                ><?php

                    echo $edit_category
                        ? htmlspecialchars(
                            $edit_category["description"]
                        )
                        : "";

                ?></textarea>

            </div>


            <?php if ($edit_category): ?>

                <button
                    type="submit"
                    name="update_category"
                    class="btn btn-primary"
                >
                    Update Category
                </button>


                <a
                    href="categories.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            <?php else: ?>

                <button
                    type="submit"
                    name="add_category"
                    class="btn btn-primary"
                >
                    Add Category
                </button>

            <?php endif; ?>


        </form>

    </div>


    <!-- CATEGORY LIST -->

    <div class="category-list">

        <h2>Categories</h2>


        <div class="table-container">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Category</th>

                        <th>Description</th>

                        <th>Books</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (count($categories) > 0): ?>

                    <?php foreach ($categories as $category): ?>

                        <tr>

                            <td>
                                <?php
                                echo $category["category_id"];
                                ?>
                            </td>


                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $category["category_name"]
                                    );
                                    ?>
                                </strong>
                            </td>


                            <td>

                                <?php

                                if (
                                    !empty(
                                        $category["description"]
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $category["description"]
                                    );

                                } else {

                                    echo "No description";

                                }

                                ?>

                            </td>


                            <td>
                                <?php
                                echo $category["book_count"];
                                ?>
                            </td>


                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="categories.php?edit=<?php
                                            echo $category["category_id"];
                                        ?>"
                                        class="btn btn-edit"
                                    >
                                        Edit
                                    </a>


                                    <?php if ($category["book_count"] == 0): ?>

                                        <a
                                            href="categories.php?delete=<?php
                                                echo $category["category_id"];
                                            ?>"
                                            class="btn btn-delete"
                                            onclick="return confirm(
                                                'Are you sure you want to delete this category?'
                                            );"
                                        >
                                            Delete
                                        </a>

                                    <?php else: ?>

                                        <span
                                            class="btn btn-disabled"
                                            title="This category contains books"
                                        >
                                            Delete
                                        </span>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>


                <?php else: ?>

                    <tr>

                        <td
                            colspan="5"
                            class="no-data"
                        >
                            No categories found.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<?php require_once "includes/admin_footer.php"; ?>