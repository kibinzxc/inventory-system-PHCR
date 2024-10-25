document.addEventListener("DOMContentLoaded", function () {
    const confirmModal = document.getElementById("uniqueConfirmModal");
    const confirmBtn = document.getElementById("uniqueConfirmBtn");
    const cancelBtn = document.getElementById("uniqueCancelBtn");

    // Function to open the confirmation modal and set the UID for deletion
    window.openConfirmModal = function (uid) {
        // Show the modal
        confirmModal.style.display = "flex";

        // Update the confirm button to redirect to delete with the correct UID
        confirmBtn.onclick = function () {
            window.location.href = 'delete.php?uid=' + uid; // Redirect to delete.php with the uid
        };
    };

    // Close modal functionality
    document.querySelector('.confirmation-close').onclick = function () {
        confirmModal.style.display = "none"; // Hide modal on close button click
    };

    cancelBtn.onclick = function () {
        confirmModal.style.display = "none"; // Hide modal on cancel
    };

    // Optional: Close modal when clicking outside of the modal content
    window.onclick = function (event) {
        if (event.target === confirmModal) {
            confirmModal.style.display = "none"; // Hide modal if clicked outside
        }
    };
});
