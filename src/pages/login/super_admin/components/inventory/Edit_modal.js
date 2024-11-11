function openEditModal(inventoryID, itemID, name, uom, beginning, deliveries, transfers_in, transfers_out, spoilage, ending, usage_count) {
    console.log("Opening modal for itemID: " + inventoryID);  // Check if the ID is correctly passed
    console.log("Ending Inventory: " + ending);  // Check if 'ending' is passed correctly

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

    // Ensure inventoryID is passed to the form
    document.getElementById('edit-inventoryID').value = inventoryID;

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
