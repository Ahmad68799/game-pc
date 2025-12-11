<?php
include '../private_gamepc/connection.php';
?>

<body>
<form action="PHP/edit_product.php" method="POST" enctype="multipart/form-data" class="product-box">
    <h1>Edit Product</h1>
    <div class="product_edit_container">

        <?php
        $stmt = $pdo->prepare("SELECT * FROM components WHERE components_id = :components_id ");
        $stmt->bindParam(':components_id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            die("Product not found.");
        }
        ?>

        <?php if ($product['image']): ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($product['image']) ?>" alt="Product Image" class="current_image">
        <?php endif; ?>

        <label class="file-input-label" for="product_image">Choose Image</label>
        <input type="file" name="image" id="product_image">

        <div class="edit_product">
            <input name="components_id" type="hidden" value="<?= htmlspecialchars($product['components_id']) ?>">
            <input name="categories_id" type="hidden" value="<?= htmlspecialchars($product['category_id']) ?>">

            <label>Product Name:</label>
            <input name="product_name" type="text" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label>Product Price:</label>
            <input name="product_price" type="number" value="<?= htmlspecialchars($product['price']) ?>" required placeholder="0,00" step="0.01" min="0">

            <label>Product Brand:</label>
            <input name="product_brand" type="text" value="<?= htmlspecialchars($product['brand']) ?>" required>

            <label>Product Category:</label>
            <?php
            // Haal alle categorieën op
            $stmt = $pdo->query("SELECT categories_id, name FROM categories ORDER BY name");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <select name="category" class="styled-select">
                <?php foreach($categories as $category): ?>
                    <option value="<?= $category['categories_id'] ?>"
                        <?= ($category['categories_id'] == $product['category_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>


            <label>Product Description:</label>
            <textarea name="product_desc" rows="4" cols="50" class="styled-textarea"><?= htmlspecialchars($product['specs']) ?></textarea>


        </div>
    </div>
    <button type="submit" class="btn_add">Update Product</button>
</form>
</body>