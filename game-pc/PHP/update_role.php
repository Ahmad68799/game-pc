<?php
session_start();
include '../../private_gamepc/connection.php';

if (!isset($_SESSION['userid']) || $_SESSION['role_id'] != 2) {
    header('Location: ../index.php?page=home');
    exit;
}

if (isset($_POST['user_id'], $_POST['new_role_id'])) {
    $user_id = (int) $_POST['user_id'];
    $new_role_id = (int) $_POST['new_role_id'];

    $stmt = $pdo->prepare("UPDATE users SET role_id = :role_id WHERE user_id = :user_id");
    $stmt->bindParam(':role_id', $new_role_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['success'] = 'User role updated successfully.';
}

header('Location: ../index.php?page=users_overview');
exit;
