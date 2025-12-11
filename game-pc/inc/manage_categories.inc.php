<?php
include '../private_gamepc/connection.php';

if (!isset($_SESSION['userid']) || $_SESSION['role_id'] != 2) {
    header('Location: index.php?page=home');
    exit;
}

$query = "
    SELECT c1.categories_id, c1.name, COUNT(p1.category_id) AS counter 
    FROM categories AS c1
    LEFT JOIN components AS p1 ON p1.category_id = c1.categories_id
    GROUP BY c1.categories_id, c1.name
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<?php
if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
    unset($_SESSION['error_message']);
}
?>

<h2>Manage Categories</h2>
<h3>Add Category</h3>
<div class="login" style="margin-left: 0px; width: fit-content">
<form action="PHP/categories.php" method="POST" enctype="multipart/form-data">
    <label for="category_name">Category name</label>
    <input type="text" id="category_name" name="category_name" placeholder="New category..." required>
    <div class="login" style="margin-left: 0px;">
        <label for="file" class="file-input-label">Upload Image</label>
        <input type="file" id="file" name="image" required>
        <div class="file-name" id="file-name">No file chosen</div>
    </div>
    <button type="submit" name="add_category">Add</button>
</form>
</div>

<h3>Categorys</h3>

<table>
    <tbody>
    <?php foreach ($categories as $category): ?>
        <tr>
            <td><?php echo htmlspecialchars($category['name']); ?></td>
            <td>
                <a href="?page=edit_category&id=<?php echo $category['categories_id']; ?>">Edit</a> |
                <?php if ($category['counter'] == 0): ?>
                    <a href="PHP/categories.php?delete_id=<?php echo $category['categories_id']; ?>">Delete</a>
                <?php else: ?>
                    <a href="PHP/categories.php?delete_id=<?php echo $category['categories_id']; ?>"
                       onclick="return confirm('In deze categorie staan <?php echo $category['counter']; ?> producten. Wil je het echt verwijderen?')">
                        Delete
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
