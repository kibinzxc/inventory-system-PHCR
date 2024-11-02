// Event listener for search input
document.getElementById('search').addEventListener('input', function () {
    let searchValue = this.value.toLowerCase();
    loadTable(searchValue, document.getElementById('sort').value, document.getElementById('sortOrder').value); // Pass sortOrder as well
});

// Event listener for sort dropdown
document.getElementById('sort').addEventListener('change', function () {
    let sortValue = this.value;
    loadTable(document.getElementById('search').value, sortValue, document.getElementById('sortOrder').value); // Pass sortOrder as well
});

// Event listener for sort order dropdown
document.getElementById('sortOrder').addEventListener('change', function () {
    let sortOrderValue = this.value;
    loadTable(document.getElementById('search').value, document.getElementById('sort').value, sortOrderValue); // Pass search value and sort as well
});

// Event listener for refresh button
document.getElementById('refresh-btn').addEventListener('click', function (e) {
    e.preventDefault(); // Prevent default anchor behavior

    // Show loader before reloading the table
    document.getElementById('loader').style.display = 'block';

    // Add the rotating class to the image
    const refreshIcon = this.querySelector('img');
    refreshIcon.classList.add('rotating');

    // Call the function to reload the table
    loadTable(document.getElementById('search').value, document.getElementById('sort').value, document.getElementById('sortOrder').value);

    // Remove the rotation class after the table is loaded
    setTimeout(() => {
        refreshIcon.classList.remove('rotating');
    }, 1000); // Adjust timing to match the animation duration
});

// Function to load the table
function loadTable(search = '', sort = 'uid', sortOrder = 'asc') {

    let xhr = new XMLHttpRequest();
    xhr.open('GET', 'itemsTable.php?search=' + encodeURIComponent(search) + '&sort=' + encodeURIComponent(sort) + '&order=' + encodeURIComponent(sortOrder), true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            // Update the table content
            document.getElementById('account-table').innerHTML = xhr.responseText;

            // Reattach event listeners for modal buttons
            attachDeleteListeners()
            attachModalListeners();
        }
        // Hide loader after the table is loaded
        document.getElementById('loader').style.display = 'none';
    };

    xhr.onerror = function () {
        console.error("Failed to load the data.");
        // Hide loader on error
        document.getElementById('loader').style.display = 'none';
    };

    xhr.send();
}

// Function to attach modal button event listeners
function attachModalListeners() {
    function closeModal() {
        var modal = document.getElementById('editModal');
        modal.style.display = 'none';
    }

    // Close the modal when clicking on the 'X' button
    var closeBtn = document.querySelector('.modal-header .close');
    if (closeBtn) {
        closeBtn.onclick = closeModal;
    }

    // Close the modal when clicking the Cancel button
    var cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.onclick = closeModal;

    }
}

// Function to attach delete listeners
function attachDeleteListeners() {
    const confirmModal = document.getElementById("uniqueConfirmModal");
    const confirmBtn = document.getElementById("uniqueConfirmBtn");
    const cancelBtn = document.getElementById("uniqueCancelBtn");

    // Function to open the confirmation modal and set the UID for deletion
    window.openConfirmModal = function (itemID) {
        // Show the modal
        confirmModal.style.display = "flex";

        // Update the confirm button to redirect to delete with the correct UID
        confirmBtn.onclick = function () {
            window.location.href = 'delete.php?itemID=' + itemID; // Redirect to delete.php with the uid
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

    const deleteButtons = document.querySelectorAll('.remove_icon'); // Select all delete buttons
    deleteButtons.forEach(button => {
        button.onclick = function (e) {
            e.preventDefault(); // Prevent the default anchor action

            // Retrieve the UID from the closest table row (assuming the button is in a table row)
            const row = button.closest('tr'); // Get the closest <tr> to the button
            const uid = row.querySelector('td:nth-child(2)').textContent; // Assuming UID is in the second column

            openConfirmModal(uid); // Call the function to open the confirm modal
        };
    });
}