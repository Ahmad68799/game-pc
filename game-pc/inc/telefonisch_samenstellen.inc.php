<?php
include '../private_gamepc/connection.php';
if (!isset($_SESSION['userid'])) {
    header('Location: index.php?page=login');
    exit();
}

try {
    // Haal gebruikers op voor de dropdown
    $stmt_users = $pdo->query("SELECT user_id, username, lastname FROM users WHERE role_id=1 ORDER BY username");
    $gebruikers = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    // Haal categorieën op
    $stmt_categories = $pdo->query("SELECT categories_id, name FROM categories ORDER BY categories_id LIMIT 8");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

    // Haal componenten op
    $stmt_components = $pdo->query("
        SELECT c.components_id, c.name, c.brand, c.price, c.category_id,
               c.image AS component_image, cat.name AS category_name, cat.categorie_image AS category_image
        FROM components c
        LEFT JOIN categories cat ON c.category_id = cat.categories_id
        ORDER BY c.brand ASC, c.name ASC
    ");

    $all_components = [];
    while ($component = $stmt_components->fetch(PDO::FETCH_ASSOC)) {
        $all_components[$component['category_id']][] = $component;
    }

} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}
?>

<div class="container">
    <h1>PC Builder (for Worker)</h1>

    <form action="PHP/telefonisch_samenstellen.php" method="post" id="pc-builder-form">
        <div class="pc-builder-grid">

        <div class="components-section">
            <div class="card">
                <div class="card-content">
                    <div>
                        <label for="build-name" class="form-label">Build Name</label>
                        <input type="text" id="build-name" name="build_name" required class="form-input" placeholder="e.g., Gaming Beast">
                    </div>
                </div>



                <label for="klant-select" class="form-label">Selecteer Klant</label>
                <select name="user_id" id="klant-select" required class="component-select">
                    <option value="">-- Selecteer een klant --</option>
                    <?php foreach ($gebruikers as $gebruiker): ?>
                        <option value="<?= $gebruiker['user_id'] ?>"><?= htmlspecialchars($gebruiker['username']) ?> <?= htmlspecialchars($gebruiker['lastname']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

                <div class="card" style="margin-top: 2rem;"> <h2 class="card-header">Select Components</h2>
                    <div class="card-content">
                    <?php foreach ($categories as $category): ?>
                        <div>
                            <label for="category-<?= $category['categories_id'] ?>" class="form-label"><?= htmlspecialchars($category['name']) ?></label>
                            <select name="components[<?= $category['categories_id'] ?>]" id="category-<?= $category['categories_id'] ?>" class="component-select w-full p-3 border border-gray-300 rounded-lg bg-white" data-category-name="<?= htmlspecialchars($category['name']) ?>">
                                <option value="" data-price="0">-- Kies een <?= htmlspecialchars($category['name']) ?> --</option>
                                <?php
                                $components_in_category = $all_components[$category['categories_id']] ?? [];
                                foreach ($components_in_category as $component):
                                    $display_name = htmlspecialchars($component['brand'] . ' - ' . $component['name']);
                                    ?>
                                    <option value="<?= $component['components_id'] ?>" data-price="<?= $component['price'] ?>" data-name="<?= $display_name ?>">
                                        <?= $display_name ?> (€<?= number_format($component['price'], 2, '.', ',') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                    <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="summary-section">
                <div class="card summary-box">
                    <h2 class="card-header" style="margin-bottom: 1rem;">Build Summary</h2>
                    <div id="summary">
                        <p class="text-placeholder">Please select components to begin.</p>
                    </div>
                    <div class="summary-footer">
                        <span>Total:</span>
                        <span id="total-price">€0.00</span>
                    </div>


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

                    <label>Email *  </label>
                    <input type="text" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required><br>


                    <button type="submit" class="submit-button">
                        Save build
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selects = document.querySelectorAll('.component-select');
        const summaryDiv = document.getElementById('summary');
        const totalPriceEl = document.getElementById('total-price');
        const euroLocale = 'nl-NL';

        function updateSummary() {
            let total = 0;
            summaryDiv.innerHTML = '';

            let anySelected = false;
            selects.forEach(select => {
                const selected = select.options[select.selectedIndex];
                if (selected.value) {
                    anySelected = true;
                    const price = parseFloat(selected.dataset.price);
                    const name = selected.dataset.name;
                    const category = select.dataset.categoryName;
                    total += price;

                    const div = document.createElement('div');
                    div.className = 'flex justify-between items-start';
                    div.innerHTML = `
                    <div class="pr-2">
                        <p class="font-semibold">${category}</p>
                        <p class="text-gray-600">${name}</p>
                    </div>
                    <p class="whitespace-nowrap">${price.toLocaleString(euroLocale, { style: 'currency', currency: 'EUR' })}</p>
                `;
                    summaryDiv.appendChild(div);
                }
            });

            if (!anySelected) {
                summaryDiv.innerHTML = '<p class="text-gray-500">Selecteer componenten om te beginnen.</p>';
            }

            totalPriceEl.textContent = total.toLocaleString(euroLocale, { style: 'currency', currency: 'EUR' });
        }

        selects.forEach(select => select.addEventListener('change', updateSummary));
        updateSummary();
    });
</script>