document.addEventListener("DOMContentLoaded", function () {
    const confirmModal = document.getElementById("uniqueSubmitModal");
    const confirmBtn = document.getElementById("ConfirmBtn2");
    const cancelBtn = document.getElementById("CancelBtn2");


    window.openSubmitModal = function (event) {
        if (event) event.preventDefault(); // Prevent page jump

        confirmModal.style.display = "flex";

        confirmBtn.onclick = function () {
            window.location.href = 'submit-inventory.php?';
        };
    };

    // Hides the modal when the close button is clicked
    const closeButton = document.querySelector('.confirmation-close2');
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
