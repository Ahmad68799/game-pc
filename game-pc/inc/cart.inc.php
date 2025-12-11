<?php
include '../private_gamepc/connection.php';

$cart = [];
$cartAssoc = [];
$total = 0;
$products = [];

if (isset($_SESSION['userid'])) {
    // Cart from database
    $stmt = $pdo->prepare("
        SELECT c.components_id, c.name, c.brand, c.specs, c.price, cart.quantity
        FROM cart
        JOIN components c ON cart.components_id = c.components_id
        WHERE cart.user_id = ?
    ");
    $stmt->execute([$_SESSION['userid']]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Cart from session
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        echo '<h2 style="text-align:center; margin-top: 50px;">Your cart is empty.</h2>';
        echo '<div style="text-align:center;"><a href="index.php?page=home" class="button">Back to shop</a></div>';
        return;
    }

    $ids = array_column($cart, 'product_id');
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT components_id, name, brand, specs, price FROM components WHERE components_id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cart as $item) {
            $cartAssoc[$item['product_id']] = $item['quantity'];
        }
    }
}
?>

<h1>Shopping Cart Overview</h1>

<?php if (!empty($products)): ?>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th>Name</th>
            <th>Brand</th>
            <th>Specs</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product):
            $productId = $product['components_id'];
            $quantity = isset($_SESSION['userid'])
                ? $product['quantity']
                : ($cartAssoc[$productId] ?? 1);
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['brand']) ?></td>
                <td><?= htmlspecialchars($product['specs']) ?></td>
                <td>€<?= number_format($product['price'], 2, ',', '.') ?></td>
                <td>
                    <form method="post" action="PHP/update_cart.php" style="display: flex; gap: 5px;">
                        <input type="hidden" name="cart_id" value="<?= $productId ?>">
                        <input type="number" name="quantity" value="<?= $quantity ?>" min="1" max="99" style="width: 100px;">
                        <button type="submit" class="button" style="padding: 5px 10px;">Update</button>
                    </form>
                </td>
                <td>€<?= number_format($subtotal, 2, ',', '.') ?></td>
                <td>
                    <form method="post" action="PHP/delete_cart.php">
                        <input type="hidden" name="cart_id" value="<?= $productId ?>">
                        <button type="submit" class="button" style="background-color: #e74c3c;">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h3 style="text-align:right;">Total: €<?= number_format($total, 2, ',', '.') ?></h3>

    <div style="text-align: right; margin-top: 20px;">
        <form action="PHP/check_out.php" method="post">
            <button type="submit" class="button">Check Out</button>
        </form>
    </div>
<?php else: ?>
    <p>Your cart is empty.</p>
<?php endif; ?>
