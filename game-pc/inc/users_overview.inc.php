<?php
if (!isset($_SESSION['userid']) || $_SESSION['role_id'] != 2) {
    header('Location: index.php?page=home');
    exit;
}

include '../private_gamepc/connection.php';

$stmt = $pdo->prepare("SELECT user_id, username, email, role_id FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$roles = [
    1 => 'Customer',
    2 => 'Admin',
    3 => 'Worker'
];
?>

<h2>Users Management</h2>

<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Current Role</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $roles[$user['role_id']] ?? 'Unknown' ?></td>
            <td>
                <?php if ($user['user_id'] == $_SESSION['userid']): ?>
                    <em>Cannot modify your own account</em>
                <?php else: ?>
                    <form action="PHP/update_role.php" method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                        <select name="new_role_id" required>
                            <?php foreach ($roles as $id => $label): ?>
                                <option value="<?= $id ?>" <?= $user['role_id'] == $id ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Edit</button>
                    </form>

                    <form action="PHP/delete_user.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
