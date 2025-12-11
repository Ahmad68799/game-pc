<?php
include '../private_gamepc/connection.php';

$selectedCategory = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$sortBy = $_GET['sort_by'] ?? 'default';
$sortOrder = $_GET['sort_order'] ?? 'DESC';

$allowedSortBy = ['name', 'price', 'default'];
$allowedSortOrder = ['ASC', 'DESC'];

if (!in_array($sortBy, $allowedSortBy)) {
    $sortBy = 'default';
}
if (!in_array($sortOrder, $allowedSortOrder)) {
    $sortOrder = 'DESC';
}

$categoryStmt = $pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$current_user_role_id = $_SESSION['role_id'] ?? null;


$sql = "
    SELECT 
        c.*,
        c.image AS component_image, 
        cat.categorie_image AS category_image,
        cat.name AS category_name 
    FROM 
        components c
    LEFT JOIN 
        categories cat ON c.category_id = cat.categories_id
    LEFT JOIN 
        build_components bc ON bc.component_id = c.components_id
";

if ($selectedCategory !== null) {
    $sql .= " WHERE c.category_id = :category_id ";
}

if ($sortBy === 'default') {
    $sql .= " ORDER BY c.components_id DESC ";
} else {
    $sql .= " ORDER BY c.{$sortBy} {$sortOrder} ";
}

$stmt = $pdo->prepare($sql);

if ($selectedCategory !== null) {
    $stmt->bindValue(':category_id', $selectedCategory, PDO::PARAM_INT);
}

$stmt->execute();
$components = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <h1>Components</h1>

    <form action="index.php" method="get" id="filterForm">
        <input type="hidden" name="page" value="product_overview" />

        <div class="filter-controls" style="display: flex; gap: 20px; align-items: center; margin-bottom: 20px;">

            <div>
                <label for="category">Filter by Category</label>
                <select class="login" name="category" id="category" style="max-width:300px; margin-top: 0px;">
                    <option value="">All</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['categories_id'] ?>" <?= ($selectedCategory == $category['categories_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="sort_by">Sort By</label>
                <select class="login" name="sort_by" id="sort_by" style="max-width:200px; margin-top: 0px;">
                    <option value="default" <?= ($sortBy == 'default') ? 'selected' : '' ?>>Default</option>
                    <option value="name" <?= ($sortBy == 'name') ? 'selected' : '' ?>>Name</option>
                    <option value="price" <?= ($sortBy == 'price') ? 'selected' : '' ?>>Price</option>
                </select>
            </div>

            <div>
                <label for="sort_order">Order</label>
                <select class="login" name="sort_order" id="sort_order" style="max-width:200px; margin-top: 0px;">
                    <option value="ASC" <?= ($sortOrder == 'ASC') ? 'selected' : '' ?>>Ascending</option>
                    <option value="DESC" <?= ($sortOrder == 'DESC') ? 'selected' : '' ?>>Descending</option>
                </select>
            </div>

            <div>
                <button type="submit" class="login" style="margin-top: 18px; color: #3674B5">Apply Filters</button>
            </div>
        </div>
    </form>


<?php if ($components): ?>
    <div class="products-wrapper">
        <?php foreach ($components as $product): ?>
            <div class="product-container">
                <div class="product">
                    <a class="a_home_link" href="index.php?page=product_page&components_id=<?= htmlspecialchars($product['components_id']) ?>">
                    <?php
                    $display_image = $product['component_image'] ?? $product['category_image'];
                    ?>
                    <?php if ($display_image): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($display_image) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="150">
                    <?php else: ?>
                        <img src="images/placeholder.png" alt="No Image Available" width="150">
                    <?php endif; ?>

                    <h2><?= htmlspecialchars($product['name']) ?></h2>

                    <p style="margin: 15px 10px; color: #ffffff;">
                        <strong style="color: #0f1111"p>Brand:</strong> <?= htmlspecialchars($product['brand']) ?>
                    </p>
                    <p style="margin: 15px 10px; color: #ffffff;">
                        <strong style="color: #0f1111">Category:</strong> <?= htmlspecialchars($product['category_name']) ?>
                    </p>

                    <h3 class="price">€ <?= number_format($product['price'], 2, ',', '.') ?></h3>
                    <?php if ($current_user_role_id == 2): ?>
                        <div>
                            <a href="index.php?page=edit_product&id=<?= $product['components_id'] ?>">
                                <button>Edit</button>
                            </a>
                            <a href="PHP/delete_product.php?id=<?= $product['components_id'] ?>" onclick="return confirm('Are you sure you want to delete this component?');">
                                <button>Delete</button>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($current_user_role_id == null): ?>
                        <div class="add-to-cart-btn-overview" >
                            <a href="index.php?page=cart&id=<?= $product['components_id'] ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>There are no components that match the filter.</p>
<?php endif; ?>