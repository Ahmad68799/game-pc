<?php
session_start();
include '../../private_gamepc/connection.php';

// Only allow workers (role_id == 3)
if (!isset($_SESSION['userid']) || $_SESSION['role_id'] != 3) {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $newStatus = $_POST['new_status'] ?? '';

    $allowedStatuses = ['processing', 'shipped', 'completed'];

    if (!in_array($newStatus, $allowedStatuses)) {
        die("Invalid status.");
    }

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE orders_id = ?");
    $stmt->execute([$newStatus, $orderId]);

    $_SESSION['success'] = "Status updated!";
}

header("Location: ../index.php?page=orders"); // Pas dit aan naar je eigen pagina
exit;
