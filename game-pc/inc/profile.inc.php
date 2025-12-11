<?php
include '../private_gamepc/connection.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['userid']]);
    $user = $stmt->fetch();

    if (isset($_POST['update_profile'])) {
        $name = htmlspecialchars($_POST['name']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $adres = htmlspecialchars($_POST['adres']);

        $updateStmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, adres = ? WHERE user_id = ?");
        $updateStmt->execute([$name, $email, $adres, $_SESSION['user_id']]);

        header('Location: index.php?page=profile&updated=true');
        exit();
    }
} catch (PDOException $e) {
    $error = "Fout: " . $e->getMessage();
}
?>

<div class="profile-container">
    <h1>Profielgegevens</h1>

    <?php if (isset($_GET['updated'])): ?>
        <div class="success-message">Profiel succesvol bijgewerkt!</div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="profile-form">
        <div class="form-group">
            <label for="name">Naam:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">E-mailadres:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="adres">Adres:</label>
            <textarea id="adres" name="adres" rows="3"><?php echo htmlspecialchars($user['street'] ?? ''); ?></textarea>
        </div>

        <button type="submit" name="update_profile" class="button">Profiel Bijwerken</button>
    </form>

    <div class="orders-section">
        <?php
        include 'inc/orders.inc.php';
        include 'inc/order_history.inc.php';
        ?>
    </div>
</div>