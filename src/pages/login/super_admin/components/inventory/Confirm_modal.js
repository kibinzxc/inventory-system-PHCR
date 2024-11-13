document.addEventListener("DOMContentLoaded", function () {
    const confirmModal = document.getElementById("uniqueConfirmModal");
    const confirmBtn = document.getElementById("uniqueConfirmBtn");
    const cancelBtn = document.getElementById("uniqueCancelBtn");
    const modalHeader = document.querySelector('.confirmation-modal-header h2');

    // Opens the confirmation modal and sets the inventoryID for deletion
    window.openConfirmModal = function (inventoryID, itemID, event) {
        if (event) event.preventDefault(); // Prevent page jump

        confirmModal.style.display = "flex";
        modalHeader.textContent = `Delete Item: ${itemID}`;

        confirmBtn.onclick = function () {
            window.location.href = 'delete.php?inventoryID=' + inventoryID;
        };
    };

    // Hides the modal when the close button is clicked
    const closeButton = document.querySelector('.confirmation-close');
    if (closeButton) {
        closeButton.onclick = function () {
            confirmModal.style.display = "none";
        };
    }

    // Hides the modal on cancel button click
    if (cancelBtn) {
        cancelBtn.onclick = function () {
            confirmModal.style.display = "none";
        };
    }

    // Closes the modal if clicked outside of its content
    window.onclick = function (event) {
        if (event.target === confirmModal) {
            confirmModal.style.display = "none";
        }
    };
});
