<?php

require_once "includes/admin_auth.php";

$error = "";
$success = "";


/* =========================
   GET ORDER ID
========================= */

$order_id = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;


if ($order_id <= 0) {

    header("Location: orders.php");
    exit();

}


/* =========================
   UPDATE ORDER STATUS
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $new_status = $_POST["order_status"] ?? "";


    $allowed_statuses = [
        "Pending",
        "Processing",
        "Shipped",
        "Delivered",
        "Cancelled"
    ];


    if (!in_array($new_status, $allowed_statuses, true)) {

        $error = "Invalid order status.";

    } else {

        $stmt = $pdo->prepare("
            UPDATE orders
            SET order_status = :order_status
            WHERE order_id = :order_id
        ");

        $stmt->execute([
            ":order_status" => $new_status,
            ":order_id" => $order_id
        ]);


        $success = "Order status updated successfully.";
    }
}


/* =========================
   GET ORDER INFORMATION
========================= */

$stmt = $pdo->prepare("
    SELECT
        orders.*,
        users.name,
        users.email,
        users.phone
    FROM orders

    INNER JOIN users
        ON orders.customer_id = users.user_id

    WHERE orders.order_id = :order_id
");

$stmt->execute([
    ":order_id" => $order_id
]);

$order = $stmt->fetch();


/* Order doesn't exist */

if (!$order) {

    header("Location: orders.php");
    exit();

}


/* =========================
   GET ORDER ITEMS
========================= */

$stmt = $pdo->prepare("
    SELECT
        order_items.order_item_id,
        order_items.quantity,
        order_items.price,

        books.book_id,
        books.title,
        books.author,
        books.image

    FROM order_items

    INNER JOIN books
        ON order_items.book_id = books.book_id

    WHERE order_items.order_id = :order_id

    ORDER BY order_items.order_item_id ASC
");

$stmt->execute([
    ":order_id" => $order_id
]);

$order_items = $stmt->fetchAll();


require_once "includes/admin_header.php";

?>


<!-- =========================
     PAGE HEADER
========================= -->

<div class="page-header">

    <div>

        <h1>
            Order #<?php echo $order["order_id"]; ?>
        </h1>

        <p>
            View order details and update order status.
        </p>

    </div>


    <a
        href="orders.php"
        class="btn btn-secondary"
    >
        Back to Orders
    </a>

</div>


<!-- =========================
     MESSAGES
========================= -->

<?php if ($error !== ""): ?>

    <div class="alert alert-error">

        <?php
        echo htmlspecialchars($error);
        ?>

    </div>

<?php endif; ?>


<?php if ($success !== ""): ?>

    <div class="alert alert-success">

        <?php
        echo htmlspecialchars($success);
        ?>

    </div>

<?php endif; ?>


<div class="order-details-grid">


    <!-- =========================
         CUSTOMER INFORMATION
    ========================= -->

    <div class="order-card">

        <h2>Customer Information</h2>


        <div class="order-info">

            <p>

                <strong>Name:</strong>

                <?php
                echo htmlspecialchars(
                    $order["name"]
                );
                ?>

            </p>


            <p>

                <strong>Email:</strong>

                <?php
                echo htmlspecialchars(
                    $order["email"]
                );
                ?>

            </p>


            <p>

                <strong>Phone:</strong>

                <?php

                echo !empty($order["phone"])
                    ? htmlspecialchars(
                        $order["phone"]
                    )
                    : "Not provided";

                ?>

            </p>

        </div>

    </div>


    <!-- =========================
         SHIPPING INFORMATION
    ========================= -->

    <div class="order-card">

        <h2>Shipping Information</h2>


        <p>

            <strong>Address:</strong>

        </p>


        <p class="shipping-address">

            <?php

            echo nl2br(
                htmlspecialchars(
                    $order["shipping_address"]
                )
            );

            ?>

        </p>

    </div>


</div>


<!-- =========================
     ORDER INFORMATION
========================= -->

<div class="order-card order-summary-card">

    <h2>Order Information</h2>


    <div class="order-info-grid">

        <div>

            <strong>Order ID</strong>

            <p>
                #<?php
                echo $order["order_id"];
                ?>
            </p>

        </div>


        <div>

            <strong>Order Date</strong>

            <p>

                <?php

                echo date(
                    "d M Y, h:i A",
                    strtotime(
                        $order["order_date"]
                    )
                );

                ?>

            </p>

        </div>


        <div>

            <strong>Total Amount</strong>

            <p>

                Rs.

                <?php

                echo number_format(
                    $order["total_amount"],
                    2
                );

                ?>

            </p>

        </div>


        <div>

            <strong>Current Status</strong>

            <p>

                <span
                    class="status status-<?php
                        echo strtolower(
                            $order["order_status"]
                        );
                    ?>"
                >

                    <?php

                    echo htmlspecialchars(
                        $order["order_status"]
                    );

                    ?>

                </span>

            </p>

        </div>

    </div>

</div>


<!-- =========================
     ORDER ITEMS
========================= -->

<div class="order-card">

    <h2>Books in This Order</h2>


    <div class="table-container">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>Book</th>

                    <th>Author</th>

                    <th>Price</th>

                    <th>Quantity</th>

                    <th>Subtotal</th>

                </tr>

            </thead>


            <tbody>


            <?php if (count($order_items) > 0): ?>


                <?php foreach ($order_items as $item): ?>

                    <tr>


                        <!-- Book -->

                        <td>

                            <div class="order-book">

                                <?php if (!empty($item["image"])): ?>

                                    <img
                                        src="../uploads/<?php
                                            echo htmlspecialchars(
                                                $item["image"]
                                            );
                                        ?>"
                                        alt="Book Cover"
                                        class="order-book-image"
                                    >

                                <?php endif; ?>


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $item["title"]
                                    );

                                    ?>

                                </strong>

                            </div>

                        </td>


                        <!-- Author -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $item["author"]
                            );

                            ?>

                        </td>


                        <!-- Price -->

                        <td>

                            Rs.

                            <?php

                            echo number_format(
                                $item["price"],
                                2
                            );

                            ?>

                        </td>


                        <!-- Quantity -->

                        <td>

                            <?php
                            echo $item["quantity"];
                            ?>

                        </td>


                        <!-- Subtotal -->

                        <td>

                            Rs.

                            <?php

                            $subtotal =
                                $item["price"] *
                                $item["quantity"];

                            echo number_format(
                                $subtotal,
                                2
                            );

                            ?>

                        </td>


                    </tr>

                <?php endforeach; ?>


            <?php else: ?>

                <tr>

                    <td
                        colspan="5"
                        class="no-data"
                    >

                        No items found for this order.

                    </td>

                </tr>

            <?php endif; ?>


            </tbody>

        </table>

    </div>


    <!-- Total -->

    <div class="order-total">

        <strong>
            Total:
        </strong>

        <strong>

            Rs.

            <?php

            echo number_format(
                $order["total_amount"],
                2
            );

            ?>

        </strong>

    </div>

</div>


<!-- =========================
     UPDATE STATUS
========================= -->

<div class="order-card">

    <h2>Update Order Status</h2>


    <form method="POST">

        <div class="status-form">

            <select
                name="order_status"
                required
            >

                <?php

                $statuses = [
                    "Pending",
                    "Processing",
                    "Shipped",
                    "Delivered",
                    "Cancelled"
                ];

                ?>


                <?php foreach ($statuses as $status): ?>

                    <option
                        value="<?php echo $status; ?>"
                        <?php

                        echo $order["order_status"] === $status
                            ? "selected"
                            : "";

                        ?>
                    >

                        <?php
                        echo $status;
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <button
                type="submit"
                class="btn btn-primary"
            >

                Update Status

            </button>

        </div>

    </form>

</div>


<?php require_once "includes/admin_footer.php"; ?>