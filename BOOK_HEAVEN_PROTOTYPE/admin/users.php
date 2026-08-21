<?php

require_once "includes/admin_auth.php";

$search = trim($_GET["search"] ?? "");
$role = $_GET["role"] ?? "";


/* =========================
   BUILD QUERY
========================= */

$sql = "
    SELECT
        user_id,
        name,
        email,
        phone,
        address,
        role,
        created_at
    FROM users
";

$conditions = [];
$params = [];


/* Search */

if ($search !== "") {

    $conditions[] = "
        (
            name LIKE :search
            OR email LIKE :search
            OR phone LIKE :search
        )
    ";

    $params[":search"] = "%" . $search . "%";
}


/* Role filter */

if ($role === "admin" || $role === "customer") {

    $conditions[] = "role = :role";

    $params[":role"] = $role;
}


/* Add WHERE */

if (count($conditions) > 0) {

    $sql .= " WHERE " . implode(" AND ", $conditions);
}


/* Newest users first */

$sql .= " ORDER BY created_at DESC";


/* Execute */

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$users = $stmt->fetchAll();


require_once "includes/admin_header.php";

?>


<!-- =========================
     PAGE HEADER
========================= -->

<div class="page-header">

    <div>

        <h1>Users</h1>

        <p>
            View registered customers and administrators.
        </p>

    </div>

</div>


<!-- =========================
     SEARCH
========================= -->

<div class="search-box">

    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search by name, email or phone..."
            value="<?php
                echo htmlspecialchars($search);
            ?>"
        >


        <select name="role">

            <option value="">
                All Users
            </option>


            <option
                value="customer"
                <?php

                echo $role === "customer"
                    ? "selected"
                    : "";

                ?>
            >
                Customers
            </option>


            <option
                value="admin"
                <?php

                echo $role === "admin"
                    ? "selected"
                    : "";

                ?>
            >
                Administrators
            </option>

        </select>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Search
        </button>


        <?php if ($search !== "" || $role !== ""): ?>

            <a
                href="users.php"
                class="btn btn-secondary"
            >
                Clear
            </a>

        <?php endif; ?>

    </form>

</div>


<!-- =========================
     USERS TABLE
========================= -->

<div class="table-container">

    <table class="admin-table">

        <thead>

            <tr>

                <th>ID</th>

                <th>Name</th>

                <th>Email</th>

                <th>Phone</th>

                <th>Address</th>

                <th>Role</th>

                <th>Registered</th>

            </tr>

        </thead>


        <tbody>

        <?php if (count($users) > 0): ?>


            <?php foreach ($users as $user): ?>

                <tr>

                    <!-- ID -->

                    <td>

                        <?php
                        echo $user["user_id"];
                        ?>

                    </td>


                    <!-- Name -->

                    <td>

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $user["name"]
                            );

                            ?>

                        </strong>

                    </td>


                    <!-- Email -->

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $user["email"]
                        );

                        ?>

                    </td>


                    <!-- Phone -->

                    <td>

                        <?php

                        echo !empty($user["phone"])
                            ? htmlspecialchars(
                                $user["phone"]
                            )
                            : "Not provided";

                        ?>

                    </td>


                    <!-- Address -->

                    <td>

                        <?php

                        if (!empty($user["address"])) {

                            $address =
                                htmlspecialchars(
                                    $user["address"]
                                );

                            /*
                             * Limit very long addresses
                             */

                            if (strlen($address) > 50) {

                                echo htmlspecialchars(
                                    substr(
                                        $address,
                                        0,
                                        50
                                    )
                                ) . "...";

                            } else {

                                echo $address;

                            }

                        } else {

                            echo "Not provided";

                        }

                        ?>

                    </td>


                    <!-- Role -->

                    <td>

                        <?php if ($user["role"] === "admin"): ?>

                            <span class="user-role role-admin">
                                Admin
                            </span>

                        <?php else: ?>

                            <span class="user-role role-customer">
                                Customer
                            </span>

                        <?php endif; ?>

                    </td>


                    <!-- Date -->

                    <td>

                        <?php

                        echo date(
                            "d M Y",
                            strtotime(
                                $user["created_at"]
                            )
                        );

                        ?>

                    </td>

                </tr>

            <?php endforeach; ?>


        <?php else: ?>

            <tr>

                <td
                    colspan="7"
                    class="no-data"
                >

                    No users found.

                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>


<?php require_once "includes/admin_footer.php"; ?>