<body>
<?php
if (isset($_GET['components_id'])) {
    $components_id = $_GET['components_id'];

    $stmt = $pdo->prepare("SELECT * FROM components WHERE components_id = :components_id");
    $stmt->bindParam(':components_id', $components_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        ?>
        <div class="product-card">
            <!-- Product image -->
            <?php if (!empty($product['image'])): ?>
                <div class="product-photo">
                    <img src="data:image/jpeg;base64,<?= base64_encode($product['image']) ?>" alt="Product Image">
                </div>
            <?php else: ?>
                <img src="css/placeholder.png" style="width: 350px; height: auto;" alt="Placeholder Image">
            <?php endif; ?>

            <!-- Product details and buttons -->
            <div class="product-details">
                <h2 class="product-title"><?= htmlspecialchars($product['name']) . ' - ' . htmlspecialchars($product['brand']) ?></h2>
                <p class="product-price">€<?= htmlspecialchars($product['price']) ?></p>

                <!-- Add to cart form -->
                <form method="post" action="PHP/add_to_cart.php" style="display:inline;">
                    <input type="hidden" name="components_id" value="<?= $product['components_id'] ?>">
                    <button type="submit" class="add-to-cart-btn">Add to cart</button>
                </form>

                <!-- Pay now form -->
                <a href="index.php?page=check_out&components_id=<?= $product['components_id'] ?>" class="add-to-cart-btn">Pay now</a>


                <div class="product-description">
                    <textarea name="product-info" rows="4" cols="50" readonly><?= htmlspecialchars($product['specs']) ?></textarea>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo 'Product not found.';
    }
}
?>
</body>
