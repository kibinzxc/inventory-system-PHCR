<link rel="stylesheet" href="Edit_modal.css">
<div id="editModal" class="modal">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2>Edit Item</h2>
            <span class="close">&times;</span>
        </div>

        <!-- Modal Content -->
        <div class="modal-body">
            <form id="editItemForm" action="updateItem.php" method="POST">
                <input type="hidden" name="inventoryID" id="edit-inventoryID">

                <div class="form-group">
                    <label for="edit-name">Name</label>
                    <input type="text" id="edit-name" name="name" disabled>
                </div>
                <div class="form-group">
                    <label for="edit-itemID">Code</label>
                    <input type="text" id="edit-itemID" name="itemID" disabled>
                </div>

                <div class="form-group">
                    <label for="edit-measurement">Measurement</label>
                    <!-- Measurement Select Dropdown -->
                    <select id="edit-measurement" name="measurement" required>
                        <option value="pcs">pcs</option>
                        <option value="grams">grams</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-qty">Quantity</label>
                    <input type="number" id="edit-qty" name="qty" required>
                </div>

            </form>
        </div>

        <!-- Modal Footer / Button Area -->
        <div class="modal-footer">
            <button type="button" id="cancelBtn" class="custom_btn cancel-btn">Cancel</button>
            <button type="submit" form="editItemForm" class="custom_btn save-btn">Save Changes</button>
        </div>
    </div>
</div>

<script src="Edit_modal.js"></script>