// Event listener for search input
document.getElementById('search').addEventListener('input', function() {
    let searchValue = this.value.toLowerCase();
    loadTable(searchValue, document.getElementById('sort').value, false); // No loader for search
});

// Event listener for sort dropdown
document.getElementById('sort').addEventListener('change', function() {
    let sortValue = this.value;
    loadTable(document.getElementById('search').value, sortValue, false); // No loader for sort
});

// Event listener for refresh button
document.getElementById('refresh-btn').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent default anchor behavior

    // Add the rotating class to the image
    const refreshIcon = this.querySelector('img');
    refreshIcon.classList.add('rotating');

    // Call the function to reload the table
    loadTable();

    // Remove the rotation class after the table is loaded
    setTimeout(() => {
        refreshIcon.classList.remove('rotating');
    }, 1000); // Adjust timing to match the animation duration
});

// Function to load the table with optional search and sort parameters
function loadTable(search = '', sort = 'uid', showLoader = true) {
    // Show loader only if specified
    if (showLoader) {
        document.getElementById('loader').style.display = 'block';
    }

    let xhr = new XMLHttpRequest();
    xhr.open('GET', 'AccountTable.php?search=' + search + '&sort=' + sort, true);
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Update the table content
            document.getElementById('account-table').innerHTML = xhr.responseText;
        }

        // Hide loader after the table is updated if it was shown
        if (showLoader) {
            document.getElementById('loader').style.display = 'none';
        }
    };

    // Handle errors (optional)
    xhr.onerror = function() {
        console.error("Failed to load the data.");
        if (showLoader) {
            document.getElementById('loader').style.display = 'none';
        }
    };

    xhr.send();
}
