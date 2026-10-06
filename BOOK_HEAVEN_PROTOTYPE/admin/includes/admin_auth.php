<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . "/config.php";

// =========================
// CHECK LOGIN
// =========================

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../user/login.php");
    exit();
}

// =========================
// CHECK ADMIN ROLE
// =========================

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../user/index.php");
    exit();
}

?>