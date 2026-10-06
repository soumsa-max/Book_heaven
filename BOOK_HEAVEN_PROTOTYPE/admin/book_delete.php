<?php

session_start();

require_once "../config.php";
require_once "includes/admin_auth.php";


/* =========================
   GET BOOK ID
========================= */

$book_id = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;


/* =========================
   VALIDATE ID
========================= */

if ($book_id <= 0) {

    header("Location: books.php");
    exit();

}


/* =========================
   GET BOOK INFORMATION
========================= */

$stmt = $pdo->prepare("
    SELECT image
    FROM books
    WHERE book_id = :book_id
");

$stmt->execute([
    ":book_id" => $book_id
]);

$book = $stmt->fetch(PDO::FETCH_ASSOC);


/* =========================
   BOOK DOES NOT EXIST
========================= */

if (!$book) {

    header("Location: books.php");
    exit();

}


/* =========================
   DELETE BOOK
========================= */

try {

    $pdo->beginTransaction();


    /* =========================
       DELETE FROM CART FIRST
    ========================= */

    $stmt = $pdo->prepare("
        DELETE FROM cart
        WHERE book_id = :book_id
    ");

    $stmt->execute([
        ":book_id" => $book_id
    ]);


    /* =========================
       DELETE BOOK
    ========================= */

    $stmt = $pdo->prepare("
        DELETE FROM books
        WHERE book_id = :book_id
    ");

    $stmt->execute([
        ":book_id" => $book_id
    ]);


    /* =========================
       DELETE COVER IMAGE
    ========================= */

    if (!empty($book["image"])) {

        $image_path = "../uploads/" . $book["image"];

        if (file_exists($image_path)) {

            unlink($image_path);

        }

    }


    /* =========================
       COMMIT
    ========================= */

    $pdo->commit();


    header("Location: books.php?success=deleted");
    exit();


} catch (Exception $e) {

    /* =========================
       ROLLBACK
    ========================= */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    header("Location: books.php?error=delete_failed");
    exit();

}