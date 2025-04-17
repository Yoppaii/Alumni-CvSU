<?php
require_once 'main_db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

function getTableName($type)
{
    return $type === 'room' ? 'room_price' : $type . '_pricing';
}

if (isset($_POST['delete'])) {
    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/json');
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    $id = $_POST['id'];
    $table = getTableName($_POST['table']);

    $delete_query = "DELETE FROM $table WHERE id = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $id);

    try {
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Record deleted successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error deleting record: ' . $mysqli->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }

    $stmt->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    try {
        $price = floatval($_POST['price']);
        $occupancy = $_POST['occupancy'];
        $type = $_POST['type'];
        $table = getTableName($type);

        if (isset($_POST['edit_id'])) {

            $id = intval($_POST['edit_id']);
            $update_query = "UPDATE $table SET price = ?, occupancy = ? WHERE id = ?";
            $stmt = $mysqli->prepare($update_query);
            $stmt->bind_param("dsi", $price, $occupancy, $id);
        } else {

            $insert_query = "INSERT INTO $table (price, occupancy) VALUES (?, ?)";
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("ds", $price, $occupancy);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Record saved successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            throw new Exception("Error saving record: " . $stmt->error);
        }

        $stmt->close();

        while (ob_get_level()) {
            ob_end_clean();
        }

        header("Location: ?section=booking-room&tab=" . $type);
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "error";

        while (ob_get_level()) {
            ob_end_clean();
        }

        header("Location: ?section=booking-room&tab=" . $type);
        exit();
    }
}

function fetchPricingRecords($mysqli, $type)
{
    $table = getTableName($type);
    $query = "SELECT id, price, occupancy FROM $table ORDER BY price ASC";
    $result = $mysqli->query($query);

    if (!$result) {
        die("Error fetching records: " . $mysqli->error);
    }

    return $result;
}

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'room';
$pricing_types = ['room', 'lobby', 'conference', 'board'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .center-text th,
        .center-text td {
            text-align: center;
            vertical-align: middle;
        }

        .pricing-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .pricing-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .pricing-tabs {
            display: flex;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 20px;
            gap: 10px;
        }

        .pricing-tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            color: #6b7280;
            transition: all 0.3s ease;
        }

        .pricing-tab:hover {
            color: #10b981;
        }

        .pricing-tab.active {
            color: #10b981;
            border-bottom-color: #10b981;
            font-weight: 600;
        }

        .pricing-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
        }

        .form-input {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background-color: #059669;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .table-container {
            overflow-x: auto;
        }

        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .pricing-table th,
        .pricing-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .pricing-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
        }

        .pricing-table tr:hover {
            background-color: #f9fafb;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .btn-icon {
            padding: 6px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            color: #3b82f6;
        }

        .btn-delete {
            color: #ef4444;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #059669;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #dc2626;
        }

        @media (max-width: 768px) {
            .pricing-form {
                grid-template-columns: 1fr;
            }

            .pricing-tabs {
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }

            .modal-content {
                width: 95%;
                margin: 0 10px;
            }
        }
    </style>
</head>

<body>
    <div class="pricing-container">
        <div class="pricing-card">
            <div class="pricing-tabs">
                <?php foreach ($pricing_types as $type): ?>
                    <div class="pricing-tab <?php echo ($current_tab == $type) ? 'active' : ''; ?>"
                        data-tab="<?php echo $type; ?>">
                        <?php echo ucfirst($type); ?> Pricing
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                </div>
            <?php endif; ?>

            <?php foreach ($pricing_types as $type):
                $is_active = ($current_tab == $type);
            ?>
                <div class="tab-content <?php echo $is_active ? 'active' : ''; ?>"
                    id="<?php echo $type; ?>-content"
                    style="display: <?php echo $is_active ? 'block' : 'none'; ?>">

                    <form method="POST" class="pricing-form">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                        <div class="form-group">
                            <label class="form-label">Price (₱)</label>
                            <input type="number" name="price" class="form-input" step="0.01" required
                                placeholder="Enter price">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Occupancy</label>
                            <input type="text" name="occupancy" class="form-input" required
                                placeholder="Enter occupancy">
                        </div>
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" name="submit" class="btn btn-primary">
                                Add <?php echo ucfirst($type); ?> Price
                            </button>
                        </div>
                    </form>

                    <div class="table-container">
                        <table class="pricing-table center-text">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th>No.</th>
                                    <th>Price</th>
                                    <th>Occupancy</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = fetchPricingRecords($mysqli, $type);
                                $counter = 1; // ← start row counter
                                while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td> <!-- This displays the current row number -->
                                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['occupancy']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" class="btn-icon btn-edit"
                                                    onclick="editPrice('<?php echo $type; ?>', <?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn-icon btn-delete"
                                                    onclick="deletePrice('<?php echo $type; ?>', <?php echo $row['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Price</h3>
                <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="hidden" name="type" id="edit_type">
                <div class="form-group">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" id="edit_price" class="form-input" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Occupancy</label>
                    <input type="text" name="occupancy" id="edit_occupancy" class="form-input" required>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" name="submit" classbutton type="submit" name="submit" class="btn btn-primary">Update Price</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Delete</h3>
                <button type="button" class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p>Are you sure you want to delete this price record? This action cannot be undone.</p>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let deleteType = '';
        let deleteId = null;
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.pricing-tab');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    const tabType = tab.getAttribute('data-tab');

                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.style.display = 'none';
                    });

                    const selectedContent = document.getElementById(`${tabType}-content`);
                    if (selectedContent) {
                        selectedContent.style.display = 'block';
                    }

                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('tab', tabType);
                    window.history.pushState({}, '', currentUrl.toString());
                });
            });

            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 3000);
            });
        });

        function editPrice(type, data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_price').value = data.price;
            document.getElementById('edit_occupancy').value = data.occupancy;

            const modal = document.getElementById('editModal');
            modal.style.display = 'flex';
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.style.display = 'none';
        }

        function deletePrice(type, id) {
            deleteType = type;
            deleteId = id;
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'flex';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'none';
            deleteType = '';
            deleteId = null;
        }

        function confirmDelete() {
            if (!deleteId || !deleteType) return;

            const formData = new FormData();
            formData.append('delete', true);
            formData.append('id', deleteId);
            formData.append('table', deleteType);

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(rawResponse => {
                    let data;
                    try {
                        data = JSON.parse(rawResponse);

                        showAlert(data.status, data.message);

                        if (data.status === 'success') {
                            setTimeout(() => {
                                const currentTab = document.querySelector('.pricing-tab.active').getAttribute('data-tab');
                                window.location.href = `?section=booking-room&tab=${currentTab}`;
                            }, 1000);
                        }
                    } catch (e) {
                        console.error('Parse error:', rawResponse);
                        if (rawResponse.includes('success')) {
                            showAlert('success', 'Record deleted successfully');
                            setTimeout(() => {
                                const currentTab = document.querySelector('.pricing-tab.active').getAttribute('data-tab');
                                window.location.href = `?section=booking-room&tab=${currentTab}`;
                            }, 1000);
                        } else {
                            showAlert('error', 'Error processing the response');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('success', 'Record deleted successfully');
                    setTimeout(() => {
                        const currentTab = document.querySelector('.pricing-tab.active').getAttribute('data-tab');
                        window.location.href = `?section=booking-room&tab=${currentTab}`;
                    }, 1000);
                })
                .finally(() => {
                    closeDeleteModal();
                });
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;

            document.querySelector('.pricing-card').insertBefore(
                alertDiv,
                document.querySelector('.pricing-tabs').nextSibling
            );

            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');

            if (event.target === editModal) {
                closeEditModal();
            }
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeEditModal();
                closeDeleteModal();
            }
        });
    </script>
</body>

</html>