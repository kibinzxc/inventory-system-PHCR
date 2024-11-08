<link rel="stylesheet" href="add-items.css">
<div id="addModal" class="modal" style="display: <?php echo $showModal ? 'block' : 'none'; ?>;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Item</h2>
            <span class="close1">&times;</span>
        </div>

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
                    <input type="number" id="quantity" name="beginning" placeholder="Enter quantity of item" step="0.01">
                </div>
                <div class="form-group">
                    <label for="add-measurement">Unit of Measurement (UoM)</label>
                    <select id="add-measurement" name="uom">
                        <option value="bag" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'bag' ? 'selected' : ''; ?>>BAG</option>
                        <option value="bt" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'bt' ? 'selected' : ''; ?>>BT (Bottle)</option>
                        <option value="box" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'box' ? 'selected' : ''; ?>>BOX</option>
                        <option value="gal" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'gal' ? 'selected' : ''; ?>>GAL (Gallon)</option>
                        <option value="grams" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'grams' ? 'selected' : ''; ?>>GRAMS</option>
                        <option value="kg" <?php echo (isset($_GET['measurement']) && $_GET['measurement'] == 'kg') || empty($_GET['measurement']) ? 'selected' : ''; ?>>KG (Kilogram)</option>
                        <option value="l" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'l' ? 'selected' : ''; ?>>L (Liter)</option>
                        <option value="pac" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'pac' ? 'selected' : ''; ?>>PAC (Pack)</option>
                        <option value="pc" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'pc' ? 'selected' : ''; ?>>PC (Piece)</option>
                        <option value="rl" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'rl' ? 'selected' : ''; ?>>RL (Roll)</option>
                        <option value="set" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'set' ? 'selected' : ''; ?>>SET</option>
                        <option value="tnk" <?php echo isset($_GET['measurement']) && $_GET['measurement'] == 'tnk' ? 'selected' : ''; ?>>TNK (Tank)</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="modal-footer2">
            <button type="button" id="cancelAddBtn" class="custom_btn cancel-btn">Cancel</button>
            <button type="submit" form="addItemForm" class="custom_btn save-btn">Save</button>
        </div>
    </div>
</div>

<script src="add-items.js"></script>