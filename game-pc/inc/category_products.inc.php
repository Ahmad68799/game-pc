<?php
include '../private_gamepc/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Ongeldige categorie.</p>";
    return;
}

$categoryId = (int)$_GET['id'];


$stmt = $pdo->prepare("SELECT name FROM categories WHERE categories_id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetch();

if (!$category) {
    echo "<p>Categorie niet gevonden.</p>";
    return;
}

echo '<h1 class="text_center">Producten in categorie: ' . htmlspecialchars($category['name']) . '</h1>';

// Haal producten op in deze categorie
$stmt = $pdo->prepare("SELECT * FROM components WHERE category_id = ?");
$stmt->execute([$categoryId]);
$products = $stmt->fetchAll();

if ($products) {
    echo '<div class="product-grid">';
    foreach ($products as $product) {
        echo '
        <div class="products-wrapper">
            <div class="product-container">
                <div class="product">
                    <img src="data:image/jpeg;base64,' . base64_encode($product['image']) . '" alt="">
                    <h2>' . htmlspecialchars($product['name']) . '</h2>
                    <h3 class="price">€' . htmlspecialchars($product['price']) . '</h3>
                    <p class="see_more">See more!</p>
                </div>
            </div>
        </div>';
    }
    echo '</div>';
} else {
    echo '<p class="text_center">Geen producten gevonden in deze categorie.</p>';
}
?>
