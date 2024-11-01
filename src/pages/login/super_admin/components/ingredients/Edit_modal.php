<link rel="stylesheet" href="Edit_modal.css">
<div id="editModal" class="modal">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2>Edit User</h2>
            <span class="close">&times;</span>
        </div>

        <!-- Modal Content -->
        <div class="modal-body">
            <form id="editAccountForm" action="updateAccount.php" method="POST">
                <input type="hidden" name="uid" id="edit-uid">
                <div class="form-group">
                    <label for="edit-name">Name</label>
                    <input type="text" id="edit-name" name="name">
                </div>
                <div class="form-group">
                    <label for="edit-email">Email</label>
                    <input type="email" id="edit-email" name="email">
                </div>
                <div class="form-group">
                    <label for="edit-password">Password</label>
                    <input type="password" id="edit-password" name="password" placeholder="Leave blank if not changing">
                </div>
                <div class="form-group">
                    <label for="edit-confirm-password">Confirm Password</label>
                    <input type="password" id="edit-confirm-password" name="confirmPassword" placeholder="Leave blank if not changing">
                </div>
            </form>
        </div>

        <!-- Modal Footer / Button Area -->
        <div class="modal-footer">
            <button type="button" id="cancelBtn" class="custom_btn cancel-btn">Cancel</button>
            <button type="submit" form="editAccountForm" class="custom_btn save-btn">Save Changes</button>
        </div>
    </div>
</div>

<script src="Edit_modal.js"></script>