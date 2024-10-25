// Open the Add Modal and optionally fill in the user details
function openAddModal(uid = '', name = '', email = '', userType = '') {
    document.getElementById('add-uid').value = uid;
    document.getElementById('add-name').value = name;
    document.getElementById('add-email').value = email;
    document.getElementById('add-userType').value = "stockman";

    var modal = document.getElementById('addModal');
    modal.style.display = 'block';
}

// Close the modal function
function closeModal() {
    var modal = document.getElementById('addModal');
    modal.style.display = 'none';
}

// Close the modal when clicking on the 'X' button
var closeBtn = document.querySelector('.modal-header .close1');
closeBtn.onclick = closeModal;

// Close the modal when clicking the Cancel button
var cancelBtn = document.getElementById('cancelAddBtn');
if (cancelBtn) {
    cancelBtn.onclick = closeModal;
}
