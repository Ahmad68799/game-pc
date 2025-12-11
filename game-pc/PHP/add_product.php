<?php
session_start();

include '../../private_gamepc/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $component_id = $_POST['component_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $brand = $_POST['brand'];
    $specs = $_POST['specs'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['image']['size'] > 1000000) {
            $_SESSION['alert'] = 'Image size too big!';
            header('Location: ../index.php?page=add_product');
            exit;
        }
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    } else {
        $_SESSION['alert'] = 'Image upload failed!';
        header('Location: ../index.php?page=add_product');
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM components WHERE name = :name");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['alert'] = "Component with this name already exists!";
        header('Location: ../index.php?page=add_product');
        exit;
    }

    if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price < 0) {
        $_SESSION['alert'] = 'Product price must be a non-negative number!';
        header('Location: ../index.php?page=add_product');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO components (name, brand, category_id, price, specs, image)
    VALUES (:name, :brand, :category_id, :price, :specs, :image)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':specs', $specs);
    $stmt->bindParam(':image', $imageData, PDO::PARAM_LOB);

    $stmt->execute();
    $_SESSION['success'] = 'Product added successfully!';
    header('Location: ../index.php?page=add_product');
    exit;
}
?>