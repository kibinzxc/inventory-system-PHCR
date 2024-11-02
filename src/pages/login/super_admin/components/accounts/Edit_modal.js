// Open the Edit Modal and fill in the user details
function openEditModal(uid, name, email, userType) {
    document.getElementById('edit-uid').value = uid;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-userType').value = userType;

    var modal = document.getElementById('editModal');
    modal.style.display = 'block';
}

// Close the modal function
function closeModal() {
    var modal = document.getElementById('editModal');
    modal.style.display = 'none';
}

// Close the modal when clicking on the 'X' button
var closeBtn = document.querySelector('.modal-header .close');
closeBtn.onclick = closeModal;



// Close the modal when clicking the Cancel button
var cancelBtn = document.getElementById('cancelBtn');
if (cancelBtn) {
    cancelBtn.onclick = closeModal;
}
