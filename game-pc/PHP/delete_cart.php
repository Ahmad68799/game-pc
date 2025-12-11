<?php
session_start();
include '../../private_gamepc/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $productId = (int) $_POST['cart_id'];

    if (isset($_SESSION['userid'])) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND components_id = :components_id");
        $stmt->execute([
            'user_id' => $_SESSION['userid'],
            'components_id' => $productId
        ]);


    } elseif (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['product_id'] == $productId) {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
    }
}

header("Location: ../index.php?page=cart");
exit;
