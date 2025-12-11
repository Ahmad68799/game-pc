<?php
include '../../private_gamepc/connection.php';
session_start();

if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);

    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['category_image']['tmp_name'];
        $imageData = file_get_contents($fileTmpPath);
    } else {
        $_SESSION['error_message'] = "Please select a valid image.";
        header('Location: ../index.php?page=manage_categories');
        exit();
    }

    $check_stmt = $pdo->prepare("SELECT * FROM categories WHERE name = :category");
    $check_stmt->execute(['category' => $category_name]);

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error_message'] = "Error: Category '$category_name' already exists!";
        header('Location: ../index.php?page=manage_categories');
        exit();
    } else {
        $insert_stmt = $pdo->prepare("INSERT INTO categories (name, categorie_image) VALUES (:category, :image)");
        $success = $insert_stmt->execute([
            'category' => $category_name,
            'image' => $imageData
        ]);

        if ($success) {
            header('Location: ../index.php?page=manage_categories');
            exit();
        } else {
            $_SESSION['error_message'] = "Error adding category.";
            header('Location: ../index.php?page=manage_categories');
            exit();
        }
    }
}

if (isset($_GET['delete_id'])) {
    $category_id = intval($_GET['delete_id']);

    $delete_stmt = $pdo->prepare("DELETE FROM categories WHERE categories_id = :id");
    $success = $delete_stmt->execute(['id' => $category_id]);

    if ($success) {
        header('Location: ../index.php?page=manage_categories');
        exit();
    } else {
        $_SESSION['error_message'] = "Error deleting category.";
        header('Location: ../index.php?page=manage_categories');
        exit();
    }
}
?>
