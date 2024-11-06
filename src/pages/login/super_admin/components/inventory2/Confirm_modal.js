document.addEventListener("DOMContentLoaded", function () {
    const confirmModal = document.getElementById("uniqueConfirmModal");
    const confirmBtn = document.getElementById("uniqueConfirmBtn");
    const cancelBtn = document.getElementById("uniqueCancelBtn");
    const modalHeader = document.querySelector('.confirmation-modal-header h2'); // The modal's header

    // Opens the confirmation modal and sets the itemID for deletion
    window.openConfirmModal = function (itemID, itemName) {
        confirmModal.style.display = "flex";

        // Update the modal header with the item name
        modalHeader.textContent = `Delete Item: ${itemName}`; // Set the text in the header to "Delete Item: itemName"

        confirmBtn.onclick = function () {
            window.location.href = 'delete.php?itemID=' + itemID; // Redirect to delete.php with the specified itemID
        };
    };


    // Hides the modal when the close button is clicked
    document.querySelector('.confirmation-close').onclick = function () {
        confirmModal.style.display = "none";
    };

    // Hides the modal on cancel button click
    cancelBtn.onclick = function () {
        confirmModal.style.display = "none";
    };

    // Closes the modal if clicked outside of its content
    window.onclick = function (event) {
        if (event.target === confirmModal) {
            confirmModal.style.display = "none";
        }
    };
});
