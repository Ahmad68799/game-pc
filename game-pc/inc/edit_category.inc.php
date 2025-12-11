<?php
include '../private_gamepc/connection.php';

if (!isset($_SESSION['userid']) || $_SESSION['role_id'] != 2) {
    header('Location: index.php?page=home');
    exit;
}

$category_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM categories WHERE categories_id = :id LIMIT 1");
$stmt->execute(['id' => $category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Category not found.");
}

if (isset($_POST['update_category'])) {
    $new_category_name = trim($_POST['category_name']);
    $updateImage = false;
    $imageData = null;

    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['category_image']['tmp_name']);
        $updateImage = true;
    }

    if ($updateImage) {
        $update_stmt = $pdo->prepare("
            UPDATE categories SET name = :name, categorie_image = :image WHERE categories_id = :id
        ");
        $success = $update_stmt->execute([
            'name' => $new_category_name,
            'image' => $imageData,
            'id' => $category_id
        ]);
    } else {
        $update_stmt = $pdo->prepare("
            UPDATE categories SET name = :name WHERE categories_id = :id
        ");
        $success = $update_stmt->execute([
            'name' => $new_category_name,
            'id' => $category_id
        ]);
    }

    if ($success) {
        header("Location: index.php?page=manage_categories");
        exit();
    } else {
        echo "Error updating category.";
    }
}
?>

<h2>Edit Category</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <label for="category_name">Name:</label>
    <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['name']); ?>" required><br><br>

    <label>Current image:</label><br>
    <?php if (!empty($category['categorie_image'])): ?>
        <img style="width: 250px" src="data:image/jpeg;base64,<?php echo base64_encode($category['categorie_image']); ?>"><br>
    <?php else: ?>
        <em>No image available</em><br>
    <?php endif; ?>

    <label for="category_image">New image (optional):</label><br>
    <input type="file" id="category_image" name="category_image" accept="image/*"><br><br>

    <button type="submit" name="update_category">Update</button>
</form>
