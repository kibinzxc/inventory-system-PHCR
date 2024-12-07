<link rel="stylesheet" href="confirm_submit.css">
<div class="confirmation-modal" id="uniqueSubmitModal">
    <div class="confirmation-modal-content1">
        <div class="confirmation-modal-header">
            <h2 id="deleteItemHeading">Submit Daily Inventory Report</h2> <!-- This will be dynamically updated -->
            <span class="confirmation-close2">&times;</span>
        </div>
        <div class="confirmation-modal-body">
            <p>This action cannot be undone. Please confirm that you want to submit the inventory for today.</p>
        </div>
        <div class="confirmation-modal-footer">
            <button type="button" id="CancelBtn2" class="custom_btn1 confirmation-cancel-btn">Cancel</button>
            <button type="button" id="ConfirmBtn2" class="custom_btn1 confirmation-confirm-btn">Confirm</button>
        </div>
    </div>
</div>

<script src="confirm_submit.js"></script>