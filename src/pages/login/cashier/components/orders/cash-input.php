<link rel="stylesheet" href="cash-input.css" />

<div id="cash-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Change for?</h2>
            <span class="close1" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="cash-input">Input Cash Amount:</label>
                <div class="input-wrapper">
                    <input type="text" id="cash-input" placeholder="0.00" />
                </div>
                <div id="error-message" style="color: red; display: none;"></div>
            </div>
        </div>
        <div class="modal-footer2">
            <button class="cancel-btn custom_btn" onclick="closeModal()">Cancel</button>
            <button class="save-btn custom_btn" onclick="calculateChange()">Submit</button>
        </div>
    </div>
</div>