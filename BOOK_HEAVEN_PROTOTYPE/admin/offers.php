<?php

require_once "../config.php";
require_once "includes/admin_auth.php";

$message = "";

/* Add Offer */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_offer"])) {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $discount = trim($_POST["discount"]);
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $status = $_POST["status"];

    if ($title === "" || $discount === "") {
        $message = "Please enter the offer title and discount.";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO offers
            (title, description, discount, start_date, end_date, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $title,
            $description,
            $discount,
            $start_date ?: null,
            $end_date ?: null,
            $status
        ]);

        $message = "Offer added successfully.";
    }
}

/* Delete Offer */
if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"];

    $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: offers.php");
    exit;
}

/* Fetch Offers */
$stmt = $pdo->query("
    SELECT *
    FROM offers
    ORDER BY created_at DESC
");

$offers = $stmt->fetchAll();

require_once "includes/admin_header.php";

?>

<h2>Manage Offers</h2>

<?php if ($message): ?>

    <p>
        <?php echo htmlspecialchars($message); ?>
    </p>

<?php endif; ?>


<!-- Add Offer -->

<div class="admin-form">

    <h3>Add New Offer</h3>

    <form method="POST">

        <label>Offer Title</label>

        <input
            type="text"
            name="title"
            placeholder="Summer Sale"
            required
        >

        <label>Description</label>

        <textarea
            name="description"
            placeholder="Get discount on selected books"
        ></textarea>

        <label>Discount (%)</label>

        <input
            type="number"
            name="discount"
            min="0"
            max="100"
            step="0.01"
            placeholder="20"
            required
        >

        <label>Start Date</label>

        <input
            type="date"
            name="start_date"
        >

        <label>End Date</label>

        <input
            type="date"
            name="end_date"
        >

        <label>Status</label>

        <select name="status">

            <option value="active">
                Active
            </option>

            <option value="inactive">
                Inactive
            </option>

        </select>

        <button type="submit" name="add_offer">
            Add Offer
        </button>

    </form>

</div>


<!-- Existing Offers -->

<div class="table-container">

    <h3>Existing Offers</h3>

    <?php if (!empty($offers)): ?>

        <table class="admin-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Discount</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($offers as $offer): ?>

                    <tr>

                        <td>
                            <?php echo $offer["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($offer["title"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($offer["description"]); ?>
                        </td>

                        <td>
                            <?php echo $offer["discount"]; ?>%
                        </td>

                        <td>
                            <?php echo htmlspecialchars($offer["start_date"] ?? ""); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($offer["end_date"] ?? ""); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($offer["status"]); ?>
                        </td>

                        <td>

                            <a
                                href="offers.php?delete=<?php echo $offer['id']; ?>"
                                onclick="return confirm('Delete this offer?');"
                            >
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php else: ?>

        <p>No offers found.</p>

    <?php endif; ?>

</div>
