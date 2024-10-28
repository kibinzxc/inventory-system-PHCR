<link rel="stylesheet" href="verification.css">
<div id="verificationModal" class="verification-modal" style="display: <?php echo $showModal ? 'block' : 'none'; ?>;">
    <div class="verification-modal-content">
        <!-- Modal Header -->
        <div class="verification-modal-header">
            <h2>Verification</h2>
            <span class="verification-close1">&times;</span>
        </div>

        <!-- Modal Content -->
        <div class="verification-modal-body">
            <form id="verificationAccountForm" action="verification_query.php" method="POST">
                <input type="hidden" name="uid" id="verification-uid">

                <div class="verification-form-group">
                    <label for="verification-password">Current Password</label>
                    <input type="password" id="verification-password" name="password" placeholder="Enter your current password">
                </div>
            </form>
        </div>

        <!-- Modal Footer / Button Area -->
        <div class="verification-modal-footer">
            <button type="button" id="verification-cancelBtn" class="verification-custom_btn verification-cancel-btn">Cancel</button>
            <button type="submit" form="verificationAccountForm" class="verification-custom_btn verification-save-btn">Proceed</button>
        </div>
    </div>
</div>

<script src="verification.js"></script>