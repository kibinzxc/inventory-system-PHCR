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
                    <label for="add-qty">Quantity</label>
                    <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" value="0">
                </div>
                <div class="form-group">
                    <label for="add-measurement">Measurement</label>
                    <select id="add-measurement" name="measurement">
                        <option value="pcs" <?php echo (isset($_GET['measurement']) && $_GET['measurement'] == 'pcs') || empty($_GET['measurement']) ? 'selected' : ''; ?>>pcs</option>
                        <option value="grams" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'grams' ? 'selected' : ''; ?>>grams</option>
                    </select>
                </div>

            </form>
        </div>

        <!-- Modal Footer / Button Area -->
        <div class="modal-footer">
            <button type="button" id="cancelAddBtn" class="custom_btn cancel-btn">Cancel</button>
            <button type="submit" form="addItemForm" class="custom_btn save-btn">Save Changes</button>
        </div>
    </div>
</div>

<script src="add-items.js"></script>