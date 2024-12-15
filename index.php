<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaShop API - Customers</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Manage Customers - PrestaShop API</h1>
    <hr>

    <!-- Customer List -->
    <h2>List of Customers</h2>
    <div id="customer-list">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $apiKey = 'MYS18LIKQEB2UYBNKQQ9MC4M2XB19VN9';
                $url = 'http://localhost/prestashop/api/customers?ws_key=' . $apiKey . '&output_format=JSON';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                $customers = json_decode($response, true);

                if (isset($customers['customers'])) {
                    foreach ($customers['customers'] as $customer) {
                        $id = $customer['id'];

                        $customerDetailsUrl = "http://localhost/prestashop/api/customers/{$id}?ws_key={$apiKey}&output_format=JSON";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $customerDetailsUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $customerResponse = curl_exec($ch);
                        curl_close($ch);
                
                        $customerData = json_decode($customerResponse, true);
                
                        // Extraer datos del cliente
                        $customerInfo = $customerData['customer'] ?? [];
                        $firstname = $customerInfo['firstname'] ?? 'N/A';
                        $lastname = $customerInfo['lastname'] ?? 'N/A';
                        $email = $customerInfo['email'] ?? 'N/A';

                        echo "<tr>
                            <td>{$id}</td>
                            <td>{$firstname}</td>
                            <td>{$lastname}</td>
                            <td>{$email}</td>
                            <td>
                                <button class='btn btn-primary btn-sm' onclick='showEditModal({$id}, \"{$firstname}\", \"{$lastname}\", \"{$email}\")'>Edit</button>
                                <button class='btn btn-danger btn-sm' onclick='deleteCustomer({$id})'>Delete</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No customers found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Customer -->
    <h2>Add Customer</h2>
    <form id="add-customer-form" method="POST" action="">
        <div class="mb-3">
            <label for="firstname" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        <div class="mb-3">
            <label for="lastname" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-success">Add Customer</button>
    </form>

    <?php
    // Handle POST request to add customer
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newCustomer = [
            'customer' => [
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'email' => $_POST['email'],
                'id_default_group' => 3,
                'active' => 1
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/prestashop/api/customers?ws_key=' . $apiKey . '&output_format=JSON');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newCustomer));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-customer-form" method="POST" action="edit_customer.php">
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="mb-3">
                            <label for="edit-firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit-firstname" name="firstname" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit-lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
function deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer?')) {
        fetch(`delete_customer.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Customer deleted successfully');
                    location.reload();
                } else {
                    alert('Failed to delete customer');
                }
            });
    }
}

function showEditModal(id, firstname, lastname, email) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-firstname').value = firstname;
    document.getElementById('edit-lastname').value = lastname;
    document.getElementById('edit-email').value = email;
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}
</script>
</body>
</html>
