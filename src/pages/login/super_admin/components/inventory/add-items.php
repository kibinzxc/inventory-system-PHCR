<link rel="stylesheet" href="add-items.css">
<div id="addModal" class="modal" style="display: <?php echo $showModal ? 'block' : 'none'; ?>;">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2>Add New Item</h2>
            <span class="close1">&times;</span>
        </div>

        <!-- Modal Content -->
        <div class="modal-body">
            <form id="addItemForm" action="add-item-query.php" method="POST">
                <input type="hidden" name="uid" id="add-uid">
                <div class="form-group">
                    <label for="add-name">Item Name</label>
                    <input type="text" id="add-name" name="name" placeholder="Enter the item name"
                        value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="add-qty">Beginning</label>
                    <input type="number" id="quantity" name="beginning" placeholder="Enter quantity of item" value="0" step="0.1">
                </div>
                <div class="form-group">
                    <label for="add-measurement">Unit of Measurement (UoM)</label>
                    <select id="add-measurement" name="uom">
                        <option value="kg" <?php echo (isset($_GET['measurement']) && $_GET['measurement'] == 'kg') || empty($_GET['measurement']) ? 'selected' : ''; ?>>KG (Kilogram)</option>

                    </select>
                </div>
                <!-- Add closing tag for the form here -->
            </form> <!-- This was missing, now added -->
        </div>

        <!-- Modal Footer / Button Area -->
        <div class="modal-footer2">
            <button type="button" id="cancelAddBtn" class="custom_btn cancel-btn">Cancel</button>
            <button type="submit" form="addItemForm" class="custom_btn save-btn">Save</button>
        </div>
    </div>
</div>

<script src="add-items.js"></script>