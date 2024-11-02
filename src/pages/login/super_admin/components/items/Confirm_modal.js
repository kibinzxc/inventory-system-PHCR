document.addEventListener("DOMContentLoaded", function () {
    const confirmModal = document.getElementById("uniqueConfirmModal");
    const confirmBtn = document.getElementById("uniqueConfirmBtn");
    const cancelBtn = document.getElementById("uniqueCancelBtn");

    // Opens the confirmation modal and sets the itemID for deletion
    window.openConfirmModal = function (itemID) {
        confirmModal.style.display = "flex";
        confirmBtn.onclick = function () {
            window.location.href = 'delete.php?itemID=' + itemID; // Redirects to delete.php with the specified itemID
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
