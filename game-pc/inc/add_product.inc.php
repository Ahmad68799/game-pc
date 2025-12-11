<form action="PHP/add_product.php" method="post" enctype="multipart/form-data">
    <div class="small_container">
        <h1>Add Product</h1>
        <div class="product-box">
            <div class="row-2">

                <div class="col-2">
                    <label>Product Name:</label>
                    <input type="text" name="name" required>

                    <label>Product Price:</label>
                    <input type="text" name="price" required>

                    <label>Brand:</label>
                    <input type="text" name="brand" required>

                    <input type="hidden" name="component_id">

                    <label>Choose a Category:</label>
                    <select name="category_id" class="add-categories_product" required>
                        <?php
                        $stmt = $pdo->prepare("SELECT categories_id, name FROM `categories`");
                        $stmt->execute();
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($categories) {
                            foreach ($categories as $category) {
                                echo '<option value="' . htmlspecialchars($category['categories_id']) . '">' . htmlspecialchars($category['name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <h3>Product Details</h3>
                    <textarea name="specs" rows="4" cols="50"></textarea>
                </div>
                <div class="col-2">
                    <label for="file" class="file-input-label">Upload Image</label>
                    <input type="file" id="file" name="image" required>
                    <div class="file-name" id="file-name">No file chosen</div>
                </div>
                <button type="submit" class="btn_add">Add Product</button>
            </div>
        </div>
    </div>
</form>

<script>
    // JavaScript to display the selected file name
    const fileInput = document.getElementById('file');
    const fileNameDisplay = document.getElementById('file-name');

    fileInput.addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        fileNameDisplay.textContent = fileName;
    });
</script>