// Open the Edit Password Modal
function openEditPassModal() {
    var modal = document.getElementById('editpassModal');
    modal.style.display = 'block';
}

// Close the modal function
function closeEditPassModal() {
    var modal = document.getElementById('editpassModal');
    modal.style.display = 'none';
}

// Close the modal when clicking on the 'X' button
var closeBtn = document.querySelector('.editpass-modal-header .editpass-close1');
if (closeBtn) {
    closeBtn.onclick = closeEditPassModal;
}

// Close the modal when clicking the Cancel button
var cancelBtn = document.getElementById('editpass-cancelAddBtn');
if (cancelBtn) {
    cancelBtn.onclick = closeEditPassModal;
}
