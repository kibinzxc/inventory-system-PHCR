<link rel="stylesheet" href="edit_password.css">
<div id="editpassModal" class="editpass-modal" style="display: <?php echo $showModal ? 'block' : 'none'; ?>;">
    <div class="editpass-modal-content">
        <!-- Modal Header -->
        <div class="editpass-modal-header">
            <h2>Add Account</h2>
            <span class="editpass-close1">&times;</span>
        </div>

        <!-- Modal Content -->
        <div class="editpass-modal-body">
            <form id="editpassAccountForm" action="edit_pass_query.php" method="POST">
                <input type="hidden" name="uid" id="editpass-uid">

                <div class="editpass-form-group">
                    <label for="editpass-password">Password</label>
                    <input type="password" id="editpass-password" name="password" placeholder="Enter the password">
                </div>

                <div class="editpass-form-group">
                    <label for="editpass-confirm-password">Confirm Password</label>
                    <input type="password" id="editpass-confirm-password" name="confirmPassword" placeholder="Retype the password">
                </div>
            </form>
        </div>

        <!-- Modal Footer / Button Area -->
        <div class="editpass-modal-footer">
            <button type="button" id="editpass-cancelAddBtn" class="editpass-custom_btn editpass-cancel-btn">Cancel</button>
            <button type="submit" form="editpassAccountForm" class="editpass-custom_btn editpass-save-btn">Save Changes</button>
        </div>
    </div>
</div>

<script src="Edit_password.js"></script>