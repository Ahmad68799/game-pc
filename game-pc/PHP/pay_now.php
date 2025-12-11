<?php
session_start();
include '../../private_gamepc/connection.php';

$user_id = $_SESSION['userid'] ?? null;

if (!$user_id) {
    // Zorg dat de gebruiker is ingelogd
    $_SESSION['alert'] = "Je moet ingelogd zijn om te bestellen.";
    header("Location: ../index.php?page=login");
    exit;
}

// Ontvang data van het formulier
$components_id = $_POST['components_id'] ?? null;
$first_name = $_POST['username'] ?? '';
$last_name = $_POST['lastname'] ?? '';
$street = $_POST['street'] ?? '';
$house_number = $_POST['house_number'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';

if (!$components_id) {
    $_SESSION['alert'] = "Geen product geselecteerd.";
    header("Location: ../index.php");
    exit;
}

// Update user info in de database
$updateUser = $pdo->prepare("UPDATE users SET username = ?, lastname = ?, street = ?, house_number = ?, zip_code = ? WHERE user_id = ?");
$updateUser->execute([$first_name, $last_name, $street, $house_number, $postal_code, $user_id]);

// Haal product info op
$stmt = $pdo->prepare("SELECT name, price FROM components WHERE components_id = ?");
$stmt->execute([$components_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['alert'] = "Product niet gevonden.";
    header("Location: ../index.php?page=home");
    exit;
}

// Voeg bestelling toe (1 item, quantity = 1)
$total_price = $product['price'];
$status = 'processing';

$insertOrder = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)");
$insertOrder->execute([$user_id, $total_price, $status]);

$order_id = $pdo->lastInsertId();

$insertOrderItem = $pdo->prepare("INSERT INTO order_items (orders_id, product_name, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
$insertOrderItem->execute([$order_id, $product['name'], 1, $product['price'], $product['price']]);

// Verwijder dit product uit cart als het daarin stond (optioneel)
$deleteFromCart = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND components_id = ?");
$deleteFromCart->execute([$user_id, $components_id]);

$_SESSION['success'] = "Bedankt voor uw bestelling!";
header("Location: ../index.php?page=order_history");
exit;
