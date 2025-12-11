<?php

include '../private_gamepc/connection.php';

$categoryStmt = $pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
$selectedCategory = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$sql = "
    SELECT c.*
    FROM components c
    LEFT JOIN build_components bc ON bc.component_id = c.components_id
";

if ($selectedCategory !== null) {
    $sql .= " WHERE c.category_id = :category_id ";
}

$sql .= " ORDER BY c.components_id DESC ";
$unusedStmt = $pdo->prepare($sql);
if ($selectedCategory !== null) {
    $unusedStmt->bindValue(':category_id', $selectedCategory, PDO::PARAM_INT);
}
$unusedStmt->execute();
$unusedComponents = $unusedStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Components</h1>


<form action="index.php" method="get" id="filterForm">
    <input type="hidden" name="page" value="product_overview" />
    <label>Filter by Category</label>
    <select class="login" style="max-width:300px; margin-top: 0px  " name="category" id="category" onchange="document.getElementById('filterForm').submit()">
        <option value="">All</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['categories_id'] ?>" <?= ($selectedCategory == $category['categories_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>



<?php if ($unusedComponents): ?>
    <div class="products-wrapper">
        <?php foreach ($unusedComponents as $product): ?>
            <div class="product-container">
                <div class="product">
                    <img src="data:image/jpeg;base64,<?= base64_encode($product['image']) ?>" alt="product" width="150">
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <h3 class="price">€ <?= number_format($product['price'], 2, ',', '.') ?></h3>
                    <div>
                        <a href="index.php?page=edit_product&id=<?= $product['components_id'] ?>">
                            <button>Edit</button>
                        </a>
                        <a href="PHP/delete_product.php?id=<?= $product['components_id'] ?>" onclick="return confirm('Are you sure you want to delete this component?');">
                            <button>Delete</button>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>There are no components match the filter.</p>
<?php endif; ?>
