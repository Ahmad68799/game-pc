<?php
session_start();
include '../../private_gamepc/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $components_id = $_POST['components_id'];
    $name = $_POST['product_name'];
    $price =  $_POST['product_price'];
    $specs = $_POST['product_desc'];
    $brand = $_POST['product_brand'];
    $image = $_FILES['image'];
    $category_id = $_POST['category'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM components WHERE name = :name AND components_id != :components_id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':components_id', $components_id);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        $_SESSION['alert'] = 'Product name already exists!';
        header('Location: ../index.php?page=edit_product&id=' . urlencode($components_id));
        exit;
    }

    if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price < 0) {
        $_SESSION['alert'] = 'Product price must be a valid non-negative number!';
        header('Location: ../index.php?page=edit_product&id=' . urlencode($components_id));
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['image']['size'] > 1000000) {
            $_SESSION['alert'] = 'Image size too big!';
            header('Location: ../index.php?page=edit_product&id=' . urlencode($components_id));
            exit;
        }
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    } else {
        // Als er geen nieuwe afbeelding is geüpload, gebruik de bestaande afbeelding
        $stmt = $pdo->prepare("SELECT image FROM components WHERE components_id = :components_id");
        $stmt->bindParam(':components_id', $components_id);
        $stmt->execute();
        $imageData = $stmt->fetchColumn();
    }

    $stmt = $pdo->prepare("UPDATE components SET name=:name, price=:price, specs=:specs, brand=:brand, category_id=:category_id, image=:image WHERE components_id=:components_id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':specs', $specs);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':image', $imageData, PDO::PARAM_LOB); // Gebruik $imageData in plaats van $image
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':components_id', $components_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Product updated successfully!';
        header('Location: ../index.php?page=product_overview');
        exit;
    } else {
        $_SESSION['alert'] = 'Error updating product!';
        header('Location: ../index.php?page=edit_product&id=' . urlencode($components_id));
        exit;
    }
} else {
    header('Location: ../index.php?page=product_overview');
    exit;
}
?>