<link rel="stylesheet" href="Edit_modal.css">
<div id="editModal" class="modal">
    <div class="modal-content2">
        <div class="modal-header">
            <h2>Edit Item</h2>
            <span class="close">&times;</span>
        </div>

        <div class="modal-body">
            <form id="editItemForm" action="updateItem.php" method="POST" onsubmit="return validateForm()">
                <input type="hidden" name="inventoryID" id="edit-inventoryID">

                <!-- Row 1: Name -->
                <div class="form-row">
                    <label for="edit-name">Name</label>
                    <input type="text" id="edit-name" name="name">
                </div>

                <!-- Row 2: Code -->
                <div class="form-row">
                    <label for="edit-itemID">Code</label>
                    <input type="text" id="edit-itemID" name="itemID">
                </div>

                <!-- Row 3: Measurement -->
                <div class="form-row">
                    <label for="edit-measurement">Base Unit of Measurement</label>
                    <select id="edit-measurement" name="uom">
                        <option value="bag">BAG</option>
                        <option value="bt">BT (Bottle)</option>
                        <option value="box">BOX</option>
                        <option value="gal">GAL (Gallon)</option>
                        <option value="grams">GRAMS</option>
                        <option value="kg">KG (Kilogram)</option>
                        <option value="l">L (Liter)</option>
                        <option value="pac">PAC (Pack)</option>
                        <option value="pc">PC (Piece)</option>
                        <option value="rl">RL (Roll)</option>
                        <option value="set">SET</option>
                        <option value="tnk">TNK (Tank)</option>
                    </select>
                </div>

                <!-- Row 4: Beginning -->
                <div class="form-row">
                    <label for="edit-qty">Beginning Inventory</label>
                    <input type="number" id="edit-qty" name="beginning" step="0.01" oninput="validateInput(this); calculateEnding();">
                    <span id="beginning-error-message" style="color: red; display: none; font-size: 12px;"></span> <!-- Error message for Beginning Inventory -->
                </div>

                <!-- Row 5: Transfers In - Deliveries -->
                <div class="form-row transfers-row">
                    <div class="half">
                        <label for="edit-transfers_in">Transfers In</label>
                        <input type="number" id="edit-transfers_in" name="transfers_in" step="0.01" oninput="validateInput(this); calculateEnding();">
                        <span id="transfers_in-error-message" style="color: red; display: none; font-size: 12px;"></span> <!-- Error message for Transfers In -->
                    </div>
                    <div class="half">
                        <label for="edit-deliveries">Deliveries</label>
                        <input type="number" id="edit-deliveries" name="deliveries" step="0.01" oninput="validateInput(this); calculateEnding();">
                        <span id="deliveries-error-message" style="color: red; display: none; font-size: 12px;"></span> <!-- Error message for Deliveries -->
                    </div>
                </div>

                <!-- Row 6: Transfers Out - Spoilage -->
                <div class="form-row transfers-row">
                    <div class="half">
                        <label for="edit-transfers_out">Transfers Out</label>
                        <input type="number" id="edit-transfers_out" name="transfers_out" step="0.01" oninput="validateInput(this); calculateEnding();">
                        <span id="transfers_out-error-message" style="color: red; display: none; font-size: 12px;"></span> <!-- Error message for Transfers Out -->
                    </div>
                    <div class="half">
                        <label for="edit-spoilage">Spoilage</label>
                        <input type="number" id="edit-spoilage" name="spoilage" step="0.01" oninput="validateInput(this); calculateEnding();">
                        <span id="spoilage-error-message" style="color: red; display: none; font-size: 12px;"></span> <!-- Error message for Spoilage -->
                    </div>
                </div>

                <!-- Row 7: Ending -->
                <div class=" form-row">
                    <label for="edit-usage">Usage</label>
                    <input type="number" id="edit-usage_count" name="usage_count" step="0.001" oninput="calculateEnding();">
                    <span id="ending-error-message" style="color: red; display: none; font-size: 12px;"></span> <!-- Error message container -->

                </div>

                <!-- Row 8: Ending -->
                <div class="form-row">
                    <label for="edit-ending">Ending Inventory</label>
                    <input type="number" id="edit-ending" name="ending" step="0.001" oninput="calculateUsage()">
                    <span id="usage-error-message" style="color: red; display: none; font-size: 12px;"></span> <!-- Error message container -->

                </div>
                <span id="ending-error-message" style="color: red; display: none;"></span>

            </form>
        </div>

        <div class="modal-footer">
            <button type="button" id="cancelBtn" class="custom_btn cancel-btn">Cancel</button>
            <button type="submit" form="editItemForm" class="custom_btn save-btn">Save Changes</button>
        </div>
    </div>
</div>
<script src="Edit_modal.js"></script>