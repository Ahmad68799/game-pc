<?php
include '../private_gamepc/connection.php';

if (!isset($_SESSION['userid'])) {
    echo "<p style='text-align:center;'>Log in om je bestelgeschiedenis te bekijken.</p>";
    return;
}

$stmt = $pdo->prepare("
    SELECT o.orders_id, o.total_price, o.created_at, o.status
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['userid']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 style="text-align:center;">Mijn Bestellingen</h1>

<?php if (empty($orders)): ?>
    <p style="text-align:center;">Je hebt nog geen bestellingen geplaatst.</p>
<?php else: ?>
    <table style="width:100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th>Bestelling ID</th>
            <th>Totaalprijs</th>
            <th>Datum</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= htmlspecialchars($order['orders_id']) ?></td>
                <td>€<?= number_format($order['total_price'], 2, ',', '.') ?></td>
                <td><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></td>
                <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
