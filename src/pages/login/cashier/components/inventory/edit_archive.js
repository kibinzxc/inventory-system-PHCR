function openArchiveModal(recordID, itemID, name, uom, beginning, deliveries, transfers_in, transfers_out, spoilage, ending, usage_count, event) {

    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();  // Prevent page reload
    }
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-itemID').value = itemID;
    document.getElementById('edit-measurement').value = uom;
    document.getElementById('edit-qty').value = beginning;
    document.getElementById('edit-deliveries').value = deliveries;
    document.getElementById('edit-transfers_in').value = transfers_in;
    document.getElementById('edit-transfers_out').value = transfers_out;
    document.getElementById('edit-spoilage').value = spoilage;
    document.getElementById('edit-ending').value = ending;
    document.getElementById('edit-usage_count').value = usage_count;


    document.getElementById('edit-recordID').value = recordID;

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

function calculateEnding() {
    // Retrieve all the necessary values
    var beginning = parseFloat(document.getElementById('edit-qty').value) || 0;
    var deliveries = parseFloat(document.getElementById('edit-deliveries').value) || 0;
    var transfers_in = parseFloat(document.getElementById('edit-transfers_in').value) || 0;
    var transfers_out = parseFloat(document.getElementById('edit-transfers_out').value) || 0;
    var spoilage = parseFloat(document.getElementById('edit-spoilage').value) || 0;
    var usage_count = parseFloat(document.getElementById('edit-usage_count').value) || 0;

    // Calculate ending inventory: Ending = Beginning + Deliveries + Transfers In - Transfers Out - Spoilage - Usage Count
    var ending = beginning + deliveries + transfers_in - transfers_out - spoilage - usage_count;

    var endingErrorMessage = document.getElementById('ending-error-message');

    // If the ending value is negative, show the error message
    if (ending < 0) {
        endingErrorMessage.style.display = 'block';
        endingErrorMessage.textContent = "Warning: The calculated ending inventory is negative. Please check your inputs.";
    } else {
        endingErrorMessage.style.display = 'none';  // Hide error message if ending is positive
    }

    // Update the ending field with the calculated value
    document.getElementById('edit-ending').value = ending.toFixed(2);  // Fix to two decimal places
}


function calculateUsage() {
    // Retrieve all the necessary values
    var beginning = parseFloat(document.getElementById('edit-qty').value) || 0;
    var deliveries = parseFloat(document.getElementById('edit-deliveries').value) || 0;
    var transfers_in = parseFloat(document.getElementById('edit-transfers_in').value) || 0;
    var transfers_out = parseFloat(document.getElementById('edit-transfers_out').value) || 0;
    var spoilage = parseFloat(document.getElementById('edit-spoilage').value) || 0;
    var ending = parseFloat(document.getElementById('edit-ending').value) || 0;

    // Calculate usage_count using the rearranged formula
    var usage_count = beginning + deliveries + transfers_in - transfers_out - spoilage - ending;

    // Error message element for usage_count
    var usageErrorMessage = document.getElementById('usage-error-message');

    // Validate: Check if usage_count is negative
    if (usage_count < 0) {
        usageErrorMessage.style.display = 'block';  // Show error message
        usageErrorMessage.textContent = "Warning: The calculated usage is negative. Please check your inputs.";  // Specific error message
    } else {
        usageErrorMessage.style.display = 'none';  // Hide error message if valid
    }

    // Update the usage_count field with the calculated value
    document.getElementById('edit-usage_count').value = usage_count.toFixed(2);  // No rounding applied
}

function validateForm() {
    // Retrieve all necessary values
    var beginning = parseFloat(document.getElementById('edit-qty').value) || 0;
    var deliveries = parseFloat(document.getElementById('edit-deliveries').value) || 0;
    var transfers_in = parseFloat(document.getElementById('edit-transfers_in').value) || 0;
    var transfers_out = parseFloat(document.getElementById('edit-transfers_out').value) || 0;
    var spoilage = parseFloat(document.getElementById('edit-spoilage').value) || 0;
    var usage_count = parseFloat(document.getElementById('edit-usage_count').value) || 0;
    var ending = parseFloat(document.getElementById('edit-ending').value) || 0;

    // Get the error message element for displaying errors
    var errorMessage = document.getElementById('form-error-message');

    // Check if any value is negative
    if (beginning < 0 || deliveries < 0 || transfers_in < 0 || transfers_out < 0 || spoilage < 0 || usage_count < 0 || ending < 0) {
        // Display error message
        errorMessage.style.display = 'block';
        errorMessage.textContent = "Warning: Negative values are detected. Please check your inputs.";

        // Prevent form submission by returning false
        return false;
    }

    // If all values are valid (non-negative), allow form submission
    return true;
}

// Function to validate each input for negative values
function validateInput(inputElement) {
    let value = parseFloat(inputElement.value);
    let errorMessage = '';
    let errorElement;

    // Determine which error span to use based on the input field
    if (inputElement.id === 'edit-qty') {
        errorElement = document.getElementById('beginning-error-message');
    } else if (inputElement.id === 'edit-transfers_in') {
        errorElement = document.getElementById('transfers_in-error-message');
    } else if (inputElement.id === 'edit-deliveries') {
        errorElement = document.getElementById('deliveries-error-message');
    } else if (inputElement.id === 'edit-transfers_out') {
        errorElement = document.getElementById('transfers_out-error-message');
    } else if (inputElement.id === 'edit-spoilage') {
        errorElement = document.getElementById('spoilage-error-message');
    }

    // Validate for negative or invalid number
    if (isNaN(value) || value < 0) {
        errorMessage = getErrorMessage(inputElement.id);
        errorElement.innerText = errorMessage;
        errorElement.style.display = 'block';  // Show the error message
    } else {
        errorElement.style.display = 'none';  // Hide the error message if valid
    }
}

// Function to get the specific error message based on the field
function getErrorMessage(inputId) {
    switch (inputId) {
        case 'edit-qty':
            return "Beginning Inventory must be a positive number.";
        case 'edit-transfers_in':
            return "Transfers In must be a positive number.";
        case 'edit-deliveries':
            return "Deliveries must be a positive number.";
        case 'edit-transfers_out':
            return "Transfers Out must be a positive number.";
        case 'edit-spoilage':
            return "Spoilage must be a positive number.";
        default:
            return "Invalid input.";
    }
}
