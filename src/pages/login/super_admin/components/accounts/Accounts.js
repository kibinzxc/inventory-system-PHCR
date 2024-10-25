// Event listener for search input
document.getElementById('search').addEventListener('input', function () {
    let searchValue = this.value.toLowerCase();
    console.log("Search Input:", searchValue); // Debugging output
    loadTable(searchValue, document.getElementById('sort').value, document.getElementById('sortOrder').value); // Pass sortOrder as well
});

// Event listener for sort dropdown
document.getElementById('sort').addEventListener('change', function () {
    let sortValue = this.value;
    console.log("Sort Value:", sortValue); // Debugging output
    loadTable(document.getElementById('search').value, sortValue, document.getElementById('sortOrder').value); // Pass sortOrder as well
});

// Event listener for sort order dropdown
document.getElementById('sortOrder').addEventListener('change', function () {
    let sortOrderValue = this.value;
    console.log("Sort Order Value:", sortOrderValue); // Debugging output
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
    console.log("Loading Table with Search:", search, "Sort:", sort, "Order:", sortOrder); // Debugging output

    let xhr = new XMLHttpRequest();
    xhr.open('GET', 'AccountTable.php?search=' + encodeURIComponent(search) + '&sort=' + encodeURIComponent(sort) + '&order=' + encodeURIComponent(sortOrder), true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            // Update the table content
            document.getElementById('account-table').innerHTML = xhr.responseText;
            console.log("Table content updated."); // Debugging output

            // Reattach event listeners for modal buttons
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
