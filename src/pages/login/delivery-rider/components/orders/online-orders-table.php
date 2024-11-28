<?php
include '../../connection/database.php';
error_reporting(1);

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Query to fetch order data
$query = "SELECT orderID, name, address, items, totalPrice, payment, del_instruct, orderPlaced, status FROM orders WHERE status = 'delivery' ORDER BY orderPlaced DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $orderID = $row['orderID'];
    $name = $row['name'];
    $address = $row['address'];
    $items = json_decode($row['items'], true);
    $totalPrice = $row['totalPrice'];
    $payment = $row['payment'];
    $del_instruct = $row['del_instruct'];
    $orderPlaced = $row['orderPlaced'];
    $status = $row['status'];

    echo '<div class="order-card">';
    echo '<div class="order-header">';
    echo '<h2>Order #' . htmlspecialchars($orderID) . '</h2>';
    echo '<p><strong>Placed on:</strong> ' . date('F j, Y g:i A', strtotime($orderPlaced)) . '</p>';
    echo '</div>';
    echo '<div class="order-body">';
    echo '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>';
    echo '<p><strong>Address:</strong> ' . htmlspecialchars($address) . '</p>';
    echo '<p><strong>Total Price:</strong> ₱' . number_format($totalPrice, 2) . '</p>';
    echo '</div>';

    if (!empty($items)) {
        echo '<div class="order-items">';
        echo '<strong>Order Details:</strong>';
        echo '<ul>';
        foreach ($items as $item) {
            echo '<li>' . htmlspecialchars($item['name']) . ' (Size: ' . htmlspecialchars($item['size']) . ') - ₱' . number_format($item['price'], 2) . ' x ' . htmlspecialchars($item['qty']) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    echo '<div class="order-actions">';
    echo '<button type="button" class="btn btn-done" data-order-id="' . $orderID . '">Delivered</button>';
    echo '</div>';

    echo '</div>';
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<div id="deliveryModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Attach Delivery Image</h2>
        <form id="deliveryForm" enctype="multipart/form-data">
            <input type="hidden" name="orderID" id="modalOrderID">

            <div class="form-group">
                <label for="deliveryImage">Attach Image:</label>
                <input type="file" name="deliveryImage" id="deliveryImage" accept="image/*" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn" id="cancelButton">Cancel</button>
                <button type="submit" class="btn submit-btn">Submit</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* General Modern Style */
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        background-color: #f4f4f9;
        color: #333;
    }

    /* Order Card */
    .order-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 15px;
        padding: 20px;
        margin: 20px auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 600px;
    }

    .order-header h2 {
        font-size: 1.6rem;
        margin: 0;
        color: #222;
    }

    .order-body p {
        margin: 8px 0;
        line-height: 1.5;
    }

    .btn-done {
        background-color: #4CAF50;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1rem;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-done:hover {
        background-color: #45A049;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
    }

    .modal-content {
        background: #fff;
        margin: 10% auto;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        max-width: 400px;
        text-align: center;
    }

    .close {
        color: #bbb;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        right: 20px;
        top: 15px;
        cursor: pointer;
        transition: 0.3s;
    }

    .close:hover {
        color: #333;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 1rem;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
        margin-bottom: 20px;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1rem;
        cursor: pointer;
        transition: 0.3s;
    }

    .cancel-btn {
        background: #e0e0e0;
        color: #555;
    }

    .cancel-btn:hover {
        background: #d6d6d6;
    }

    .submit-btn {
        background: #007BFF;
        color: #fff;
        border: none;
    }

    .submit-btn:hover {
        background: #0056b3;
    }
</style>

<script>
    // Get the modal
    var modal = document.getElementById("deliveryModal");

    // Get the button that opens the modal
    var buttons = document.getElementsByClassName("btn-done");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Get the cancel button
    var cancelButton = document.getElementById("cancelButton");

    // When the user clicks the button, open the modal
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].onclick = function() {
            var orderID = this.getAttribute("data-order-id");
            document.getElementById("modalOrderID").value = orderID;
            modal.style.display = "block";
        }
    }

    // When the user clicks on <span> (x) or cancel button, close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    cancelButton.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Handle form submission
    document.getElementById("deliveryForm").onsubmit = function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        fetch('delivered.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                modal.style.display = "none";
                location.reload(); // Reload the page to reflect changes
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
</script>