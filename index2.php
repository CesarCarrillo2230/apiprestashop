<?php
$apiKey = 'MYS18LIKQEB2UYBNKQQ9MC4M2XB19VN9';
$apiUrl = 'http://localhost/prestashop/api/customers';
$outputFormat = 'JSON';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'create') {
        // Crear cliente
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
        curl_setopt($ch, CURLOPT_URL, "{$apiUrl}?ws_key={$apiKey}&output_format={$outputFormat}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newCustomer));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        echo $response;
        exit;
    } elseif ($action === 'edit') {
        // Editar cliente
        $id = $_POST['id'];
        $updatedCustomer = [
            'customer' => [
                'id' => $id,
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'email' => $_POST['email']
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$apiUrl}/{$id}?ws_key={$apiKey}&output_format={$outputFormat}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updatedCustomer));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        echo $response;
        exit;
    } elseif ($action === 'delete') {
        // Eliminar cliente
        $id = $_POST['id'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$apiUrl}/{$id}?ws_key={$apiKey}&output_format={$outputFormat}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        echo $response;
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrestaShop Customer Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage Customers</h1>

    <!-- Formulario para agregar clientes -->
    <form id="add-customer-form" method="POST">
        <input type="hidden" name="action" value="create">
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

    <hr class="my-4">

    <!-- Tabla de clientes existentes (dummy table for example) -->
    <h2>Customer List</h2>
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
        <tbody id="customer-table">
        <!-- Example row -->
        <tr>
            <td>1</td>
            <td>John</td>
            <td>Doe</td>
            <td>john.doe@example.com</td>
            <td>
                <button class="btn btn-primary btn-sm" onclick="showEditModal(1, 'John', 'Doe', 'john.doe@example.com')">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteCustomer(1)">Delete</button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<!-- Modal para editar clientes -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-customer-form" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function deleteCustomer(id) {
        if (confirm('Are you sure you want to delete this customer?')) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete&id=${id}`
            })
                .then(response => response.text())
                .then(data => {
                    alert('Customer deleted successfully');
                    location.reload();
                })
                .catch(err => alert('Failed to delete customer'));
        }
    }

    function showEditModal(id, firstname, lastname, email) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-firstname').value = firstname;
        document.getElementById('edit-lastname').value = lastname;
        document.getElementById('edit-email').value = email;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>
</body>
</html>
