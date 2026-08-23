<?php

require_once "../config.php";
require_once "includes/admin_auth.php";

$stmt = $pdo->query("
    SELECT *
    FROM contact_messages
    ORDER BY created_at DESC
");

$messages = $stmt->fetchAll();

require_once "includes/admin_header.php";

?>

<h2>Contact Messages</h2>

<div class="table-container">

    <?php if (!empty($messages)): ?>

        <table class="admin-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($messages as $message): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($message['id']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($message['name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($message['email']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($message['subject']); ?>
                        </td>

                        <td>
                            <?php echo nl2br(
                                htmlspecialchars($message['message'])
                            ); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($message['created_at']); ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php else: ?>

        <p>No contact messages found.</p>

    <?php endif; ?>

</div>
