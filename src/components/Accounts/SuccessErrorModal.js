document.addEventListener("DOMContentLoaded", function () {
    const successModal = document.getElementById("successModal");
    const errorModal = document.getElementById("errorModal");
    const successMessage = document.getElementById("successMessage");
    const errorMessage = document.getElementById("errorMessage");

    const urlParams = new URLSearchParams(window.location.search);
    const update = urlParams.get('update');
    const reason = urlParams.get('reason');
    const messages = urlParams.get('messages');

    console.log("Update parameter:", update); // Debugging line
    console.log("Reason parameter:", reason); // Debugging line
    console.log("Messages parameter:", messages); // Debugging line

    // Show success modal if update is successful
    if (update === 'success') {
        if (reason === 'account_updated') {
            successMessage.textContent = "Account updated successfully.";
        } else if (reason === 'account_deleted') {
            successMessage.textContent = "Account deleted successfully.";
        }
        successModal.style.display = "flex"; // Show the success modal

        setTimeout(() => {
            successModal.style.display = "none";
        }, 5000); // Hide after 5 seconds
    }
    // Show error modal if there is an error
    else if (update === 'error') {
        let message = "An error occurred. ";

        if (reason === 'password_mismatch') {
            message += "The passwords do not match.";
        } else if (reason === 'sql_failure') {
            message += "There was a database error.";
        } else if (reason === 'empty_fields') {
            message += "Please fill in all fields.";
        } else if (reason === 'password_criteria') {
            if (messages) {
                message += messages.replace(/%20/g, ' '); // Decode URL-encoded spaces
            }
        } else if (reason === 'no_account_specified') {
            message += "No account specified for deletion.";
        } else {
            message += "Please try again.";
        }

        errorMessage.textContent = message;
        errorModal.style.display = "flex"; // Show the error modal

        setTimeout(() => {
            errorModal.style.display = "none";
        }, 5000); // Hide after 5 seconds
    }

    // Close modals on click (optional)
    successModal.addEventListener("click", () => {
        successModal.style.display = "none";
    });

    errorModal.addEventListener("click", () => {
        errorModal.style.display = "none";
    });
});
