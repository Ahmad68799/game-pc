<?php
session_start();
include '../../private_gamepc/connection.php';



if (isset($_POST['components_id'])) {
    $id = (int)$_POST['components_id'];

    // Haal het product op uit de database
    $stmt = $pdo->prepare("SELECT * FROM components WHERE components_id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $user_id = $_SESSION['userid'] ?? null;

        if ($user_id) {
            $checkCart = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND components_id = ?");
            $checkCart->execute([$user_id, $id]);
            $existing = $checkCart->fetch();

            if ($existing) {
                $update = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND components_id = ?");
                $update->execute([$user_id, $id]);
            } else {
                $insert = $pdo->prepare("INSERT INTO cart (user_id, components_id, quantity) VALUES (?, ?, 1)");
                $insert->execute([$user_id, $id]);
            }

        } else {
            $item = [
                'product_id' => $product['components_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1
            ];

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $found = false;
            foreach ($_SESSION['cart'] as &$cartItem) {
                if ($cartItem['components_id'] == $item['components_id']) {
                    $cartItem['quantity'] += 1;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $_SESSION['cart'][] = $item;
            }
        }
    }
}

header("Location: ../index.php?page=cart");
exit;
