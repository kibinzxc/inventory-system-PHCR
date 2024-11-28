// Open the Add Modal and optionally fill in the user details
function openAddReport() {
    var modal = document.getElementById('reportModal');
    modal.style.display = 'block';
}

// Close the modal function
function closeModal() {
    var modal = document.getElementById('reportModal');
    modal.style.display = 'none';
}

// Close the modal when clicking on the 'X' button
var closeBtn = document.querySelector('.modal-header .close2');
closeBtn.onclick = closeModal;

// Close the modal when clicking the Cancel button
var cancelBtn = document.getElementById('cancelreportBtn');
if (cancelBtn) {
    cancelBtn.onclick = closeModal;
}
