<link rel="stylesheet" href="Edit_modal.css">
<div id="editModal" class="modal">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2>Edit Inventory Item</h2>
            <span class="close">&times;</span>
        </div>

        <!-- Modal Content -->
        <div class="modal-body">
            <form id="editInventoryForm" action="updateAccount.php" method="POST">
                <input type="hidden" name="itemID" id="edit-itemID">

                <div class="form-group">
                    <label for="edit-name">Name</label>
                    <input type="text" id="edit-name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="edit-quantity">Quantity</label>
                    <input type="number" id="edit-quantity" name="qty" required min="0">
                </div>

                <div class="form-group">
                    <label for="edit-measurement">Measurement</label>
                    <input type="text" id="edit-measurement" name="measurement" required>
                </div>

                <div class="form-group">
                    <label for="edit-status">Status</label>
                    <select id="edit-status" name="status" required>
                        <option value="in-stock">In Stock</option>
                        <option value="low-stock">Low Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Modal Footer / Button Area -->
        <div class="modal-footer">
            <button type="button" id="cancelBtn" class="custom_btn cancel-btn">Cancel</button>
            <button type="submit" form="editInventoryForm" class="custom_btn save-btn">Save Changes</button>
        </div>
    </div>
</div>

<script src="Edit_modal.js"></script>