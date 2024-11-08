function openEditModal(inventoryID, itemID, name, qty, measurement) {
    console.log("Opening modal for itemID: " + inventoryID);  // Check if the ID is correctly passed
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-itemID').value = itemID;
    document.getElementById('edit-qty').value = qty;
    document.getElementById('edit-measurement').value = measurement;
    document.getElementById('edit-inventoryID').value = inventoryID;  // Ensure inventoryID is passed to the form


    // Show the modal
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
