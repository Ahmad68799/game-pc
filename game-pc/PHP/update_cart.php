<?php
session_start();
include '../../private_gamepc/connection.php';

$cartId = (int)($_POST['cart_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if ($quantity < 1 || $quantity > 99) {
    header("Location: ../index.php?page=cart");
    exit;
}

if (isset($_SESSION['userid'])) {
    // Update in database
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND components_id = ?");
    $stmt->execute([$quantity, $_SESSION['userid'], $cartId]);
} else {
    // Update in session
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $cartId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
        unset($item); // good practice
    }
}

header("Location: ../index.php?page=cart");
exit;
