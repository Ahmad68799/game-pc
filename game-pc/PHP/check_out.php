<?php
session_start();
include '../../private_gamepc/connection.php';

$user_id = $_SESSION['userid'] ?? null;
$guest_name = $_POST['naam'] ?? null;
$guest_email = $_POST['email'] ?? null;

$cart = [];

if ($user_id) {
    // Logged-in user → get cart from database
    $stmt = $pdo->prepare("
        SELECT c.name, c.price, cart.quantity
        FROM cart
        JOIN components c ON c.components_id = cart.components_id
        WHERE cart.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Guest user → get cart from session
    $cart = $_SESSION['cart'] ?? [];
}

// Check if cart is empty
if (empty($cart)) {
    $_SESSION['alert'] = "Your cart is empty.";
    header("Location: ../index.php?page=cart");
    exit;
}

// Calculate total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert order with status 'processing'
$stmt = $pdo->prepare("INSERT INTO orders (user_id, guest_name, guest_email, total_price, status) VALUES (?, ?, ?, ?, 'processing')");
$stmt->execute([$user_id, $guest_name, $guest_email, $total]);

$orderId = $pdo->lastInsertId();

// Insert order items
$insertItem = $pdo->prepare("INSERT INTO order_items (orders_id, product_name, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");

foreach ($cart as $item) {
    $insertItem->execute([
        $orderId,
        $item['name'],
        $item['quantity'],
        $item['price'],
        $item['price'] * $item['quantity']
    ]);
}

// Clear cart
if ($user_id) {
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
} else {
    $_SESSION['cart'] = [];
}

$_SESSION['success'] = "Thank you for your order!";
header("Location: ../index.php?page=order_history");
exit;
