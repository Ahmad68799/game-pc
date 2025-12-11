<?php
include '../private_gamepc/connection.php';

$stmt = $pdo->query("
    SELECT o.orders_id, o.total_price, o.created_at, o.status, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    ORDER BY o.created_at DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage Orders</h1>

<table>
    <thead>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Total Price</th>
        <th>Date</th>
        <th>Status</th>
        <th>Change Status</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td>#<?= $order['orders_id'] ?></td>
            <td><?= htmlspecialchars($order['username'] ?? 'Guest') ?></td>
            <td>€<?= number_format($order['total_price'], 2, ',', '.') ?></td>
            <td><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></td>
            <td><?= ucfirst($order['status']) ?></td>
            <td>
                <form method="post" action="PHP/update_orders_status.php">
                    <input type="hidden" name="order_id" value="<?= $order['orders_id'] ?>">
                    <select name="new_status">
                        <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                    <button type="submit" class="button">Save</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
