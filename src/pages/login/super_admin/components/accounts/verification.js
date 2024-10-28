// Open the Verification Modal
function openVerificationModal() {
    var modal = document.getElementById('verificationModal');
    modal.style.display = 'block';
}

// Close the modal function
function closeVerificationModal() {
    var modal = document.getElementById('verificationModal');
    modal.style.display = 'none';
}

// Close the modal when clicking on the 'X' button
var closeBtn = document.querySelector('.verification-modal-header .verification-close1');
if (closeBtn) {
    closeBtn.onclick = closeVerificationModal;
}

// Close the modal when clicking the Cancel button
var cancelBtn = document.getElementById('verification-cancelBtn');
if (cancelBtn) {
    cancelBtn.onclick = closeVerificationModal;
}
