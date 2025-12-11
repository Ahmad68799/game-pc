<?php
include '../private_gamepc/connection.php';
?>

<div class="home_page">
    <img src="css/homepage.png">
    <div class="text-overlay">
        <!-- Optional text overlay -->
        <h1>The best Game PC’s</h1>
        <p>We have the best game pc's for you!</p>
        <a href="index.php?page=build" class="button-link">
            <button>Build a custom pc now! </button>
        </a>
    </div>
</div>
<div class="extrapng">"
    <img src="css/extra.png">
</div>

<img class="arrow_down" src="css/down-arrow.png">
<h1 class="text_center">Built your own pc now!</h1>

<div class="categories_container">
    <h1 class="text_center">Categories</h1>
    <div class="categories_grid">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM categories");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($categories) {
            foreach ($categories as $categorie) {
                echo '
                <div class="categories_wrapper" onclick="location.href=\'index.php?page=product_overview&category=' . $categorie['categories_id'] . '\'">
                    <div class="categories_box">
                        <div class="categories">
                            <img src="data:image/jpeg;base64,' . base64_encode($categorie['categorie_image']) . '" alt="">
                            <h2>' . htmlspecialchars($categorie['name']) . '</h2>
                            <p class="see_more">See more!</p>
                        </div>
                    </div>
                </div>
                ';
            }
        } else {
            echo '<p>No categories found.</p>';
        }
        ?>
    </div>
</div>
<img class="red_arrow" src="css/redarrow.png">
<img class="foto_1" src="css/foto_1.png">
<img class="foto_2" src="css/foto2.png">
<img class="foto_3" src="css/foto3.png">
<img class="foto_4" src="css/foto4.png">
<div class="prebuild_pc">
    <h1 class="text_center">Prebuilt Pc's</h1>
    <img class=" arrow_down" src="css/down-arrow.png">

    <h3 class="text_center"> We have some prebuilt pc's that are ready to be shipped!</h3>
    <div class="product-grid">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM components WHERE category_id = 9");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($products) {
            foreach ($products as $product) {
                echo '
               <div class="products-wrapper">
                <div class="product-container">
                    <div class="product">
                        <img src="data:image/jpeg;base64,' . base64_encode($product['image']) . '" alt="">
                        <h2>' . htmlspecialchars($product['name']) . '</h2>
                        <h3 class="price">' . htmlspecialchars($product['price']) . '</h3>
                        <p class="see_more" > see more!</p>
                    </div>
                </div>
                </div>
                ';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        ?>
    </div>
    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h2>Customer Service</h2>
                <a href="#">Delivery & Returns</a>
                <a href="#">Product & Stock</a>
                <a href="#">Technical Support</a>
                <a href="#">Term of Use</a>
                <a href="#">Contact Us</a>
            </div>
            <div class="footer-section links">
                <h2>Quick Links</h2>
                <ul>
                    <li><a href="index.php?page=home">Home</a></li>
                    <li><a href="index.php?page=register">Register</a></li>
                    <li><a href="index.php?page=contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section contact">
                <h2>Contact Us</h2>
                <p>Email: support@gamepc.com</p>
                <p>Phone: +123 456 7890</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2023 SK GAMING | Designed by SK GAMING
        </div>
    </footer>
</div>