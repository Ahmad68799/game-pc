<?php
include '../private_gamepc/connection.php';
$current_user_id = $_SESSION['userid'] ?? null;
$current_user_role_id = $_SESSION['role_id'] ?? null;
if ($current_user_id === null || $current_user_role_id === null) {
    header('Location: index.php?page=login');
    exit;
}
$query = "
    SELECT
        b.builds_id,
        b.name AS build_name,
        b.total_price,
        u.email AS user_email,
        c.name AS component_name,
        b.created_at AS order_date,
        b.status AS order_status
    FROM
        builds AS b
    LEFT JOIN
        users AS u ON b.user_id = u.user_id
    LEFT JOIN
        build_components AS bc ON b.builds_id = bc.build_id
    LEFT JOIN
        components AS c ON bc.component_id = c.components_id
";

if ($current_user_role_id == 1) {
    $query .= " WHERE b.user_id = :current_user_id";
}
$query .= " ORDER BY b.builds_id DESC";
$stmt = $pdo->prepare($query);
if ($current_user_role_id == 1) {
    $stmt->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$builds = [];
foreach ($results as $row) {
    $build_id = $row['builds_id'];
    if (!isset($builds[$build_id])) {
        $builds[$build_id] = [
            'build_name' => $row['build_name'],
            'user_email' => $row['user_email'],
            'total_price' => $row['total_price'],
            'order_date' => $row['order_date'],
            'order_status' => $row['order_status'],
            'components' => []
        ];
    }

    if ($row['component_name'] !== null) {
        $builds[$build_id]['components'][] = $row['component_name'];
    }
}
?>

<h2>Saved Builds</h2>
<table style="color: #0f1111; border-collapse: collapse;" border="1" cellpadding="8" cellspacing="0">
    <thead>
    <tr>
        <th>Build Name</th>
        <th>Created By</th>
        <th>Components</th>
        <th>Total Price</th>
        <th>Order Date</th>
        <th>Status</th>
        <?php if ($current_user_role_id == 3): ?>
            <th>Actions</th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($builds)): ?>
        <tr>
            <td colspan="<?php echo ($current_user_role_id == 3) ? '5' : '4'; ?>" style="text-align: center;">No saved builds found.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($builds as $builds_id => $build): ?>
            <tr>
                <td><?= htmlspecialchars($build['build_name']) ?></td>
                <td><?= htmlspecialchars($build['user_email']) ?></td>
                <td>
                    <textarea readonly style="width: 100%; height: 100px; resize: vertical;" disabled><?= htmlspecialchars(implode("\n", $build['components'])) ?></textarea>
                </td>

                <td>€<?= number_format($build['total_price'], 2, ',', '.') ?></td>
                <td>
                    <?= $build['order_date']
                        ? date('d-m-Y H:i', strtotime($build['order_date']))
                        : 'Not ordered' ?>
                </td>
                <?php if ($current_user_role_id == 3): ?>
                    <td>
                        <?= ucfirst($build['order_status']) ?>
                    </td>
                <td>
                    <form method="post" action="PHP/update_build_orders_status.php">
                        <input type="hidden" name="build_id" value="<?= $builds_id ?>">
                        <select name="new_status">
                            <option value="processing" <?= $build['order_status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $build['order_status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="completed" <?= $build['order_status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <button type="submit" class="button">Save</button>
                    </form>
                </td>
                <?php else: ?>
                <td>
                    <?= $build['order_status']
                        ? ucfirst($build['order_status'])
                        : 'N/A' ?>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>