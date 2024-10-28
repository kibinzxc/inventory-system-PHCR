document.addEventListener("DOMContentLoaded", function () {
    const successModal = document.getElementById("successModal");
    const errorModal = document.getElementById("errorModal");
    const successMessage = document.getElementById("successMessage");
    const errorMessage = document.getElementById("errorMessage");

    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action'); // Get action parameter
    const reason = urlParams.get('reason');
    const message = urlParams.get('message'); // Using 'messages' for error messages


    // Show success modal if action is add or update
    if (action === 'add') {
        successMessage.textContent = message || "User successfully added."; // Default message for add
        successModal.style.display = "flex"; // Show the success modal

        setTimeout(() => {
            successModal.style.display = "none";
        }, 5000); // Hide after 5 seconds
    } else if (action === 'update') {
        successMessage.textContent = message || "User successfully updated."; // Default message for update
        successModal.style.display = "flex"; // Show the success modal

        setTimeout(() => {
            successModal.style.display = "none";
        }, 5000); // Hide after 5 seconds
    } else if (action === 'newpass') {
        successMessage.textContent = message || "Password successfully updated."; // Default message for update
        successModal.style.display = "flex"; // Show the success modal

        setTimeout(() => {
            successModal.style.display = "none";
        }, 5000); // Hide after 5 seconds

    } else if (action === 'del') {
        successMessage.textContent = message || "User successfully deleted."; // Default message for update
        successModal.style.display = "flex"; // Show the success modal

        setTimeout(() => {
            successModal.style.display = "none";
        }, 5000); // Hide after 5 seconds
    }

    // Show error modal if there is an error
    else if (action === 'error') {
        let errorMsg = "An error occurred. ";

        // Check for specific reasons and set error message accordingly
        switch (reason) {
            case 'password_mismatch':
                errorMsg += "The passwords do not match.";
                break;
            case 'sql_failure':
                errorMsg += "There was a database error.";
                break;
            case 'empty_fields':
                errorMsg += "Please fill in all fields.";
                break;
            case 'name_empty':
                errorMsg += "Name cannot be blank.";
                break;
            case 'email_empty':
                errorMsg += "Email cannot be blank.";
                break;
            case 'userType_empty':
                errorMsg += "User type cannot be blank.";
                break;
            case 'password_empty':
                errorMsg += message;
                break;
            case 'password_criteria':
                errorMsg += message || "Password does not meet criteria.";
                break;
            case 'no_account_specified':
                errorMsg += "No account specified for deletion.";
                break;
            case 'uid_missing':
                errorMsg += "UID is missing.";
                break;
            case 'incorrect_password':
                errorMsg += message;
            default:
                errorMsg += "Please try again.";
                break;
        }

        errorMessage.textContent = errorMsg;
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
