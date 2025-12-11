<?php
session_start();
include '../../private_gamepc/connection.php';

if (!isset($_SESSION['userid']) || $_SESSION['role_id'] != 2) {
    header('Location: ../index.php?page=home');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // voorkom dat admin zichzelf verwijdert
    if ($user_id === $_SESSION['userid']) {
        $_SESSION['notification'] = "You cannot delete your own account.";
        header('Location: ../index.php?page=manage_users');
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :id");
    if ($stmt->execute(['id' => $user_id])) {
        $_SESSION['success'] = "User successfully deleted.";
    } else {
        $_SESSION['alert'] = "Failed to delete user.";
    }

    header('Location: ../index.php?page=users_overview');
    exit;
}
?>
