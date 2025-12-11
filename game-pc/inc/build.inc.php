<?php
// PHP kodunuz burada değişmeden kalır
include '../private_gamepc/connection.php';
if (!isset($_SESSION['userid'])) {
    header('Location: index.php?page=login');
    exit();
}

if ($_SESSION['userid'] == 3){
    header('Location: index.php?page=telefonisch_samenstellen');
    exit();
}

$user_id = $_SESSION['userid'];

try {
    $stmt_categories = $pdo->query("SELECT categories_id, name FROM categories ORDER BY categories_id LIMIT 8"); //test
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
    $stmt_components = $pdo->query("
        SELECT
            c.components_id,
            c.name,
            c.brand,
            c.price,
            c.category_id,
            c.image AS component_image,
            cat.name AS category_name,
            cat.categorie_image AS category_image
        FROM
            components c
        LEFT JOIN
            categories cat ON c.category_id = cat.categories_id
        ORDER BY c.brand ASC, c.name ASC
    ");

    $all_components = [];
    while ($component = $stmt_components->fetch(PDO::FETCH_ASSOC)) {
        $all_components[$component['category_id']][] = $component;
    }

} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
?>

<div class="container">
    <h1>PC Builder Wizard</h1>

    <form action="PHP/build.php" method="post" id="pc-builder-form">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

        <div class="pc-builder-grid">

            <div class="components-section">
                <div class="card">
                    <div class="card-content">
                        <div>
                            <label for="build-name" class="form-label">Build Name</label>
                            <input type="text" id="build-name" name="build_name" required class="form-input" placeholder="e.g., Gaming Beast">
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top: 2rem;"> <h2 class="card-header">Select Components</h2>
                    <div class="card-content">
                        <?php foreach ($categories as $category): ?>
                            <div>
                                <label for="category-select-<?= $category['categories_id'] ?>" class="form-label"><?= htmlspecialchars($category['name']) ?></label>
                                <select name="components[<?= $category['categories_id'] ?>]"
                                        id="category-select-<?= $category['categories_id'] ?>"
                                        data-category-id="<?= $category['categories_id'] ?>"
                                        data-category-name="<?= htmlspecialchars($category['name']) ?>"
                                        class="component-select">
                                    <option value="" data-price="0">-- Select a <?= htmlspecialchars($category['name']) ?> --</option>
                                    <?php
                                    $components_in_category = isset($all_components[$category['categories_id']]) ? $all_components[$category['categories_id']] : [];
                                    foreach ($components_in_category as $component):
                                        $display_name = htmlspecialchars($component['brand'] . ' - ' . $component['name']);
                                        ?>
                                        <option value="<?= $component['components_id'] ?>"
                                                data-price="<?= $component['price'] ?>"
                                                data-name="<?= $display_name ?>">
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
                        <div class="total-price-line">
                            <span>Total:</span>
                            <span id="total-price">€0.00</span>
                        </div>
                    </div>
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

                    <label>Email *  </label>
                    <input type="text" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required><br>

                    <button type="submit" class="submit-button">
                        Pay Now
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const componentSelects = document.querySelectorAll('.component-select');
        const summaryDiv = document.getElementById('summary');
        const totalPriceEl = document.getElementById('total-price');
        const euroLocale = 'en-IE';

        const updateSummaryAndPrice = () => {
            let totalPrice = 0;
            let hasSelection = false;
            summaryDiv.innerHTML = '';

            componentSelects.forEach(select => {
                const selectedOption = select.options[select.selectedIndex];
                const componentId = selectedOption.value;

                if (componentId) {
                    hasSelection = true;
                    const price = parseFloat(selectedOption.dataset.price);
                    const name = selectedOption.dataset.name;
                    const categoryName = select.dataset.categoryName;
                    totalPrice += price;

                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'summary-item';
                    itemDiv.innerHTML = `
                        <div class="summary-item-details">
                            <p class="summary-item-category">${categoryName}</p>
                            <p class="summary-item-name">${name}</p>
                        </div>
                        <p class="summary-item-price">${price.toLocaleString(euroLocale, { style: 'currency', currency: 'EUR' })}</p>
                    `;
                    summaryDiv.appendChild(itemDiv);
                }
            });

            if (!hasSelection) {
                summaryDiv.innerHTML = '<p class="text-placeholder">Please select components to begin.</p>';
            }

            totalPriceEl.textContent = totalPrice.toLocaleString(euroLocale, { style: 'currency', currency: 'EUR' });
        };

        componentSelects.forEach(select => {
            select.addEventListener('change', updateSummaryAndPrice);
        });

        updateSummaryAndPrice();
    });
</script>