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
                    <label for="add-shelfLife">Shelf Life <span style="color:gray; font-weight:400;">(day/s)</span></label>
                    <input type="number" id="add-shelfLife" name="shelfLife" placeholder="Enter the item's shelf life (e.g. 30 days)"
                        value="<?php echo isset($_GET['shelfLife']) ? htmlspecialchars($_GET['shelfLife']) : ''; ?>">
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