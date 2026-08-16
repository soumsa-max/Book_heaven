<?php

require_once "includes/admin_auth.php";

$search = trim($_GET["search"] ?? "");
$status = $_GET["status"] ?? "";


/* =========================
   BUILD QUERY
========================= */

$sql = "
    SELECT
        orders.order_id,
        orders.order_date,
        orders.shipping_address,
        orders.total_amount,
        orders.order_status,
        users.name,
        users.email,
        users.phone
    FROM orders
    INNER JOIN users
        ON orders.customer_id = users.user_id
";

$conditions = [];
$params = [];


/* Search */

if ($search !== "") {

    $conditions[] = "
        (
            orders.order_id = :order_id
            OR users.name LIKE :search
            OR users.email LIKE :search
        )
    ";

    $params[":order_id"] = ctype_digit($search)
        ? (int)$search
        : 0;

    $params[":search"] = "%" . $search . "%";
}


/* Status filter */

$allowed_statuses = [
    "Pending",
    "Processing",
    "Shipped",
    "Delivered",
    "Cancelled"
];

if (in_array($status, $allowed_statuses, true)) {

    $conditions[] = "orders.order_status = :status";

    $params[":status"] = $status;
}


/* Add conditions */

if (count($conditions) > 0) {

    $sql .= " WHERE " . implode(" AND ", $conditions);
}


/* Order by newest */

$sql .= "
    ORDER BY orders.order_date DESC
";


/* Execute */

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$orders = $stmt->fetchAll();


require_once "includes/admin_header.php";

?>


<div class="page-header">

    <div>

        <h1>Customer Orders</h1>

        <p>
            View and manage customer orders.
        </p>

    </div>

</div>


<!-- =========================
     SEARCH AND FILTER
========================= -->

<div class="search-box">

    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search by order ID, customer name or email..."
            value="<?php echo htmlspecialchars($search); ?>"
        >


        <select name="status">

            <option value="">
                All Statuses
            </option>

            <?php foreach ($allowed_statuses as $order_status): ?>

                <option
                    value="<?php echo $order_status; ?>"
                    <?php
                    echo $status === $order_status
                        ? "selected"
                        : "";
                    ?>
                >
                    <?php echo $order_status; ?>
                </option>

            <?php endforeach; ?>

        </select>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Search
        </button>


        <?php if ($search !== "" || $status !== ""): ?>

            <a
                href="orders.php"
                class="btn btn-secondary"
            >
                Clear
            </a>

        <?php endif; ?>

    </form>

</div>


<!-- =========================
     ORDERS TABLE
========================= -->

<div class="table-container">

    <table class="admin-table">

        <thead>

            <tr>

                <th>Order ID</th>

                <th>Customer</th>

                <th>Email</th>

                <th>Date</th>

                <th>Total</th>

                <th>Status</th>

                <th>Action</th>

            </tr>

        </thead>


        <tbody>

        <?php if (count($orders) > 0): ?>

            <?php foreach ($orders as $order): ?>

                <tr>

                    <td>
                        <strong>
                            #<?php echo $order["order_id"]; ?>
                        </strong>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $order["name"]
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $order["email"]
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo date(
                            "d M Y, h:i A",
                            strtotime(
                                $order["order_date"]
                            )
                        );
                        ?>
                    </td>


                    <td>
                        Rs.
                        <?php
                        echo number_format(
                            $order["total_amount"],
                            2
                        );
                        ?>
                    </td>


                    <td>

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

                    </td>


                    <td>

                        <a
                            href="order_details.php?id=<?php
                                echo $order["order_id"];
                            ?>"
                            class="btn btn-primary"
                        >
                            View Details
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>


        <?php else: ?>

            <tr>

                <td
                    colspan="7"
                    class="no-data"
                >
                    No orders found.
                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>


<?php require_once "includes/admin_footer.php"; ?>