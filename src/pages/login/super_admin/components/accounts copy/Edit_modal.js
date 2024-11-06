// Open the Edit Modal and fill in the inventory item details
function openEditModal(itemID, name, qty, measurement, status) {
    document.getElementById('edit-itemID').value = itemID;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-quantity').value = qty;
    document.getElementById('edit-measurement').value = measurement;
    document.getElementById('edit-status').value = status;

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
