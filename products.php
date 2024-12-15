<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaShop API - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Manage Products - PrestaShop API</h1>
    <hr>

    <!-- Product List -->
    <h2>List of Products</h2>
    <div id="product-list">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $apiKey = 'MYS18LIKQEB2UYBNKQQ9MC4M2XB19VN9';
                $url = 'http://localhost/prestashop/api/products?ws_key=' . $apiKey . '&output_format=JSON';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                $products = json_decode($response, true);

                if (isset($products['products'])) {
                    foreach ($products['products'] as $product) {
                        $id = $product['id'];

                        $productDetailsUrl = "http://localhost/prestashop/api/products/{$id}?ws_key={$apiKey}&output_format=JSON";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $productDetailsUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $productResponse = curl_exec($ch);
                        curl_close($ch);

                        $productData = json_decode($productResponse, true);
                        $productInfo = $productData['product'] ?? [];
                        $name = $productInfo['name'][0]['value'] ?? 'N/A';
                        $price = $productInfo['price'] ?? 'N/A';

                        echo "<tr>
                            <td>{$id}</td>
                            <td>{$name}</td>
                            <td>\${$price}</td>
                            <td>
                                <button class='btn btn-primary btn-sm' onclick='showEditModal({$id}, \"{$name}\", \"{$price}\")'>Edit</button>
                                <button class='btn btn-danger btn-sm' onclick='deleteProduct({$id})'>Delete</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No products found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Product -->
    <h2>Add Product</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>
        <button type="submit" class="btn btn-success">Add Product</button>
    </form>

    <?php
    // Handle POST request to add product
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newProduct = [
            'product' => [
                'name' => [['language' => ['id' => 1], 'value' => $_POST['name']]],
                'price' => $_POST['price'],
                'id_category_default' => 3,
                'active' => 1
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/prestashop/api/products?ws_key=' . $apiKey . '&output_format=JSON');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newProduct));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-product-form" method="POST" action="edit_product.php">
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="edit-price" name="price" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch(`delete_product.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product deleted successfully');
                    location.reload();
                } else {
                    alert('Failed to delete product');
                }
            });
    }
}

function showEditModal(id, name, price) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-price').value = price;
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
