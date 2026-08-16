<?php

require_once "includes/admin_auth.php";

$stmt = $pdo->query("SELECT COUNT(*) FROM books");
$total_books = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
$total_categories = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$total_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'Cancelled'");
$total_sales = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'");
$pending_orders = $stmt->fetchColumn();

require_once "includes/admin_header.php";

?>

<h1>Dashboard</h1>

<p class="dashboard-subtitle">
    Welcome to the Book Heaven Admin Panel.
</p>


<!-- Dashboard Cards -->

<div class="dashboard-cards">

    <div class="dashboard-card">
        <h3>Total Books</h3>
        <p><?php echo $total_books; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Categories</h3>
        <p><?php echo $total_categories; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Customers</h3>
        <p><?php echo $total_users; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Total Orders</h3>
        <p><?php echo $total_orders; ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Total Sales</h3>
        <p>Rs. <?php echo number_format($total_sales, 2); ?></p>
    </div>

    <div class="dashboard-card">
        <h3>Pending Orders</h3>
        <p><?php echo $pending_orders; ?></p>
    </div>

</div>


<!-- Recent Orders -->

<div class="dashboard-section">

    <h2>Recent Orders</h2>

    <?php

    $stmt = $pdo->query("
        SELECT 
            orders.order_id,
            orders.order_date,
            orders.total_amount,
            orders.order_status,
            users.name
        FROM orders
        INNER JOIN users 
            ON orders.customer_id = users.user_id
        ORDER BY orders.order_date DESC
        LIMIT 5
    ");

    $recent_orders = $stmt->fetchAll();

    ?>

    <?php if (count($recent_orders) > 0): ?>

        <div class="table-container">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($recent_orders as $order): ?>

                        <tr>

                            <td>
                                #<?php echo $order['order_id']; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($order['name']); ?>
                            </td>

                            <td>
                                <?php echo date(
                                    "d M Y",
                                    strtotime($order['order_date'])
                                ); ?>
                            </td>

                            <td>
                                Rs.
                                <?php echo number_format(
                                    $order['total_amount'],
                                    2
                                ); ?>
                            </td>

                            <td>

                                <span class="status status-<?php echo strtolower($order['order_status']); ?>">
                                    <?php echo htmlspecialchars($order['order_status']); ?>
                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <p class="no-data">
            No orders have been placed yet.
        </p>

    <?php endif; ?>

</div>


<?php require_once "includes/admin_footer.php"; ?>