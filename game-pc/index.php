<?php
session_start();
include '../private_gamepc/connection.php';
if(isset($_GET['page'])){
    $page = $_GET['page'];
} else {
    $page = 'home';
}

if (isset($_SESSION['userid'])) {
    // Teller uit database voor ingelogde gebruiker
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['userid']]);
    $result = $stmt->fetch();
    $cartCount = $result['total'] ?? 0;
} else {
    $cartCount = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += $item['quantity'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game PC</title>
    <link rel="icon" href="css/EWA.svg" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body><div class="auth-buttons">
    <?php if (isset($_SESSION['userid'])): ?>
        <div class="dropdown">
            <a class="button"><?= htmlspecialchars($_SESSION['username']) ?> ▼</a>
            <div class="dropdown-content">
                <?php if ($_SESSION['role_id'] == 2): ?>
                    <a href="index.php?page=product_overview">Products Overview</a>
                    <a href="index.php?page=add_product">Add Products</a>
                    <a href="index.php?page=manage_categories">Authorise Categories</a>
                    <a href="index.php?page=users_overview">Users Overview</a>
                    <a href="index.php?page=orders">Orders</a>
                <?php elseif ($_SESSION['role_id'] == 1): ?>
                    <a href="index.php?page=profile">Profile</a>
                    <a href="index.php?page=cart">Cart (<?= $cartCount ?>)</a>
                    <a href="index.php?page=order_history">Bestelgeschiedenis</a>

                <?php elseif ($_SESSION['role_id'] == 3): ?>
                    <a href="index.php?page=orders">Build Orders</a>
                    <a href="index.php?page=medewerker_orders">User Orders</a>
                    <a href="index.php?page=telefonisch_samenstellen">Telefonisch Samenstellen</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Logout apart -->
        <a href="PHP/logout.php" class="button">Log out</a>
    <?php else: ?>
        <a href="index.php?page=login" class="button">Login</a>
        <a href="index.php?page=register" class="button">Register</a>
    <?php endif; ?>
</div>

<div class="content">
    <?php
    include 'inc/navbar.inc.php';

    if (isset($_SESSION['alert'])): ?>
        <div class="alert">
            <span onclick="this.parentElement.style.display='none'" class="close-btn">&times;</span>
            <p><?php echo $_SESSION['alert']; ?></p>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php endif;

    if (isset($_SESSION['success'])): ?>
        <div class="success">
            <span onclick="this.parentElement.style.display='none'" class="close-btn">&times;</span>
            <p><?php echo $_SESSION['success']; ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif;


    include 'inc/' . htmlspecialchars($page) . '.inc.php';
    ?>
    <footer style="text-align: center">
        <p>&copy; 2025 <a href="https://ernygen.github.io/cardemir/">Cardemir Design </a><a>- Eren Aygen  - Ahmad Eknan احمد اكنان - Wang An Yang </a></p>
    </footer>
</div>

</body>
</html>
