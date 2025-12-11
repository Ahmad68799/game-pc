<?php
include '../private_gamepc/connection.php';

$user = null;
if (isset($_SESSION['userid'])) {
    $stmt = $pdo->prepare("
        SELECT username, lastname, street, house_number, zip_code
        FROM users
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['userid']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Checkout</h2>

<form method="post" action="PHP/pay_now.php">
    <input type="hidden" name="components_id" value="<?= htmlspecialchars($_GET['components_id'] ?? '') ?>">

    <label>First Name *</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required><br>

    <label>Last Name *</label>
    <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>" required><br>

    <label>Street *</label>
    <input type="text" name="street" value="<?= htmlspecialchars($user['street'] ?? '') ?>" required><br>

    <label>House Number *</label>
    <input type="text" name="house_number" value="<?= htmlspecialchars($user['house_number'] ?? '') ?>" required><br>

    <label>Postal Code *</label>
    <input type="text" name="postal_code" value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>" required><br>

    <button type="submit">Place Order</button>
</form>
