<link rel="stylesheet" href="add-account.css">
<div id="addModal" class="modal" style="display: <?php echo $showModal ? 'block' : 'none'; ?>;">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2>Add Account</h2>
            <span class="close1">&times;</span>
        </div>

        <!-- Modal Content -->
        <div class="modal-body">
            <form id="addAccountForm" action="add-account-query.php" method="POST">
                <input type="hidden" name="uid" id="add-uid">
                <div class="form-group">
                    <label for="add-name">Name</label>
                    <input type="text" id="add-name" name="name" placeholder="Enter the name"
                        value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="add-email">Email</label>
                    <input type="email" id="add-email" name="email" placeholder="Enter the email"
                        value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="add-userType">Role</label>
                    <select id="add-userType" name="userType">
                        <option value="" disabled selected hidden>Select user type</option>
                        <option value="admin" <?php echo (isset($_GET['userType']) && $_GET['userType'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="stockman" <?php echo (isset($_GET['userType']) && $_GET['userType'] === 'stockman') ? 'selected' : ''; ?>>Stockman</option>
                        <option value="cashier" <?php echo (isset($_GET['userType']) && $_GET['userType'] === 'stockman') ? 'selected' : ''; ?>>Cashier</option>
                        <option value="cashier" <?php echo (isset($_GET['userType']) && $_GET['userType'] === 'stockman') ? 'selected' : ''; ?>>Rider</option>


                    </select>
                </div>
                <div class="form-group">
                    <label for="add-password">Password</label>
                    <input type="password" id="add-password" name="password" placeholder="Enter the password">
                </div>
                <div class="form-group">
                    <label for="add-confirm-password">Confirm Password</label>
                    <input type="password" id="add-confirm-password" name="confirmPassword" placeholder="Retype the password">
                </div>
            </form>
        </div>

        <!-- Modal Footer / Button Area -->
        <div class="modal-footer">
            <button type="button" id="cancelAddBtn" class="custom_btn cancel-btn">Cancel</button>
            <button type="submit" form="addAccountForm" class="custom_btn save-btn">Save Changes</button>
        </div>
    </div>
</div>

<script src="add-account.js"></script>