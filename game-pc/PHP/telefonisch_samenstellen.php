<?php
session_start();
include '../../private_gamepc/connection.php';

// Toon PHP-fouten
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$build_name = filter_input(INPUT_POST, 'build_name', FILTER_SANITIZE_STRING);
$component_ids = $_POST['components'] ?? [];

if (!$user_id || !$build_name || empty($component_ids)) {
    die("Fout: Gebrek aan gegevens.");
}

$total_price = 0;
$placeholders = rtrim(str_repeat('?,', count($component_ids)), ',');

try {
    $stmt = $pdo->prepare("SELECT SUM(price) as total FROM components WHERE components_id IN ($placeholders)");
    $stmt->execute(array_values($component_ids));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_price = $result['total'] ?? 0;
} catch (PDOException $e) {
    die("Fout bij prijsberekening: " . $e->getMessage());
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("INSERT INTO builds (user_id, name, total_price) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $build_name, $total_price]);
    $build_id = $pdo->lastInsertId();

    $stmt_component = $pdo->prepare("INSERT INTO build_components (build_id, component_id) VALUES (?, ?)");
    foreach ($component_ids as $component_id) {
        if (!empty($component_id)) {
            $stmt_component->execute([$build_id, $component_id]);
        }
    }

    $pdo->commit();
    $_SESSION['success'] = 'Build successfully created.';
    header('Location: ../index.php?page=orders'); // Zorg ervoor dat de header na de sessie-instelling komt
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Databasefout: " . $e->getMessage());
}