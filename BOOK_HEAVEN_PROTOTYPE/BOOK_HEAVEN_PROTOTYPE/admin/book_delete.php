<?php

require_once "includes/admin_auth.php";

/* Get book ID */

$book_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;


/* Validate ID */

if ($book_id <= 0) {
    header("Location: books.php");
    exit();
}


/* Get book information */

$stmt = $pdo->prepare("
    SELECT image
    FROM books
    WHERE book_id = :book_id
");

$stmt->execute([
    ":book_id" => $book_id
]);

$book = $stmt->fetch();


/* Book does not exist */

if (!$book) {
    header("Location: books.php");
    exit();
}


/*
 * Delete book
 *
 * Because book_id may be referenced by order_items,
 * we need to handle that relationship carefully.
 */

try {

    $pdo->beginTransaction();


    /*
     * Check whether this book exists in order_items.
     */

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM order_items
        WHERE book_id = :book_id
    ");

    $stmt->execute([
        ":book_id" => $book_id
    ]);

    $order_item_count = $stmt->fetchColumn();


    /*
     * If the book has already been ordered,
     * don't delete it because order history depends on it.
     */

    if ($order_item_count > 0) {

        $pdo->rollBack();

        header("Location: books.php?error=ordered");
        exit();
    }


    /*
     * Delete from cart first.
     */

    $stmt = $pdo->prepare("
        DELETE FROM cart
        WHERE book_id = :book_id
    ");

    $stmt->execute([
        ":book_id" => $book_id
    ]);


    /*
     * Delete book.
     */

    $stmt = $pdo->prepare("
        DELETE FROM books
        WHERE book_id = :book_id
    ");

    $stmt->execute([
        ":book_id" => $book_id
    ]);


    /*
     * Delete image from uploads folder.
     */

    if (!empty($book["image"])) {

        $image_path = "../uploads/" . $book["image"];

        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }


    $pdo->commit();


    header("Location: books.php?success=deleted");
    exit();


} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: books.php?error=delete_failed");
    exit();
}