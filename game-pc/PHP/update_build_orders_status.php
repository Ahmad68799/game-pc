<?php
session_start();
include '../../private_gamepc/connection.php';

// Alleen admins mogen dit
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header('Location: ../index.php?page=login');
    exit;
}

$build_id = $_POST['build_id'] ?? null;
$new_status = $_POST['new_status'] ?? null;

if ($build_id && $new_status) {
    // Toegestane statussen
    $allowed_statuses = ['processing', 'shipped', 'completed'];
    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['alert'] = "Invalid status value.";
        header("Location: ../index.php?page=saved_builds");
        exit;
    }

    try {
        // Update de status van de build
        $stmt = $pdo->prepare("UPDATE builds SET status = :status WHERE builds_id = :build_id");
        $stmt->execute([
            ':status' => $new_status,
            ':build_id' => $build_id
        ]);

        $_SESSION['success'] = "Build status updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['alert'] = "Error updating build status: " . $e->getMessage();
    }
} else {
    $_SESSION['alert'] = "Missing build ID or status.";
}

header("Location: ../index.php?page=orders");
exit;