document.addEventListener("DOMContentLoaded", function () {

    // Event listener for search input
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            let searchValue = this.value.toLowerCase();
            loadTable(searchValue, document.getElementById('sort').value, document.getElementById('sortOrder').value);
        });
    }

    // Event listener for sort dropdown
    const sortDropdown = document.getElementById('sort');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function () {
            let sortValue = this.value;
            loadTable(document.getElementById('search').value, sortValue, document.getElementById('sortOrder').value);
        });
    }

    // Event listener for sort order dropdown
    const sortOrderDropdown = document.getElementById('sortOrder');
    if (sortOrderDropdown) {
        sortOrderDropdown.addEventListener('change', function () {
            let sortOrderValue = this.value;
            loadTable(document.getElementById('search').value, document.getElementById('sort').value, sortOrderValue);
        });
    }

    // Event listener for refresh button
    const refreshBtn = document.getElementById('refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function (e) {
            e.preventDefault();

            document.getElementById('loader').style.display = 'block';

            const refreshContainer = this;
            const loaderCircle = refreshContainer.querySelector('.loader-circle');
            const refreshIcon = refreshContainer.querySelector('img');

            loaderCircle.style.display = 'block';
            refreshIcon.style.display = 'none';

            loadTable(document.getElementById('search').value, document.getElementById('sort').value, document.getElementById('sortOrder').value);

            setTimeout(() => {
                loaderCircle.style.display = 'none';
                refreshIcon.style.display = 'block';
            }, 1000);
        });
    }

    // Function to load the table
    function loadTable(search = '', sort = 'itemID', sortOrder = 'asc') {
        const currentUrl = new URL(window.location.href);

        // Get the 'date' parameter from the URL
        let selectedDate = currentUrl.searchParams.get('date'); // Get the selected date

        // Only load data if a date is selected
        if (!selectedDate) {
            // Hide the table and message if no date is selected
            document.getElementById('archive-table').innerHTML = '';
            const selectedDateElement = document.getElementById('selected-date');
            if (selectedDateElement) {
                selectedDateElement.style.display = 'none';
            }
            return; // Exit the function as no data should be loaded
        }

        // Format the selected date as "Month Day, Year"
        const formattedDate = formatDate(selectedDate);

        // Display the formatted date in the UI
        const selectedDateElement = document.getElementById('selected-date');
        if (selectedDateElement) {
            selectedDateElement.textContent = `Order Log Date: ${formattedDate}`;
            selectedDateElement.style.display = 'block'; // Ensure the h2 is visible
        }

        // Prepare the URL for the request with the date parameter
        let xhr = new XMLHttpRequest();
        let url = 'archive-table.php?search=' + encodeURIComponent(search) +
            '&sort=' + encodeURIComponent(sort) +
            '&order=' + encodeURIComponent(sortOrder) +
            '&date=' + encodeURIComponent(selectedDate);

        xhr.open('GET', url, true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('archive-table').innerHTML = xhr.responseText;
                attachDeleteListeners();
                attachModalListeners();
            }
            document.getElementById('loader').style.display = 'none';
        };

        xhr.onerror = function () {
            console.error("Failed to load the data.");
            document.getElementById('loader').style.display = 'none';
        };

        xhr.send();
    }

    // Event listener to update the URL when the date is changed
    document.getElementById('inventory-date').addEventListener('change', function () {
        const selectedDate = this.value; // Get the selected date
        const currentUrl = new URL(window.location.href);

        // Update the 'date' parameter in the URL
        if (selectedDate) {
            currentUrl.searchParams.set('date', selectedDate); // Set or update the 'date' parameter
        } else {
            currentUrl.searchParams.delete('date'); // If no date is selected, remove the 'date' parameter
        }

        // Update the URL in the browser without reloading the page
        window.history.pushState({}, '', currentUrl);

        // Reload the table with the selected date
        loadTable();
    });

    // Function to format the date in "Month Day, Year" format
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    // Ensure that the input date is not pre-selected and no data is shown on initial load
    window.onload = function () {
        const currentUrl = new URL(window.location.href);
        const selectedDate = currentUrl.searchParams.get('date'); // Get the date from the URL

        // Hide the selected date message by default
        const selectedDateElement = document.getElementById('selected-date');
        if (selectedDateElement) {
            selectedDateElement.style.display = 'none';
        }

        // Ensure the input date field is cleared on initial load
        const inventoryDateInput = document.getElementById('inventory-date');
        if (inventoryDateInput) {
            inventoryDateInput.value = ''; // Clear the input field
        }

        // Only load the table if a date is selected
        if (selectedDate) {
            inventoryDateInput.value = selectedDate; // Set the date field to the date from the URL
            loadTable();
        } else {
            document.getElementById('archive-table').innerHTML = ''; // Ensure no data is shown
        }

    };



    // Function to attach modal button event listeners
    function attachModalListeners() {
        function closeModal() {
            const editModal = document.getElementById('editModal');
            const addReportModal = document.getElementById('addReport');

            if (editModal) {
                editModal.style.display = 'none';
            }
            if (addReportModal) {
                addReportModal.style.display = 'none';
            }
        }

        var closeBtn = document.querySelector('.modal-header .close');
        if (closeBtn) {
            closeBtn.onclick = closeModal;
        }
        var closeBtn3 = document.querySelector('.modal-header .close3');
        if (closeBtn3) {
            closeBtn3.onclick = closeModal;
        }

        var cancelBtn = document.getElementById('cancelBtn');
        if (cancelBtn) {
            cancelBtn.onclick = closeModal;
        }

        var cancelBtn3 = document.getElementById('cancelBtn3');
        if (cancelBtn3) {
            cancelBtn3.onclick = closeModal;
        }
    }

    // Function to attach delete listeners
    function attachDeleteListeners() {
        const confirmModal = document.getElementById("uniqueConfirmModal");
        const confirmBtn = document.getElementById("uniqueConfirmBtn");
        const cancelBtn = document.getElementById("uniqueCancelBtn");
        const modalHeader = document.querySelector('.confirmation-modal-header h2');


        window.openConfirmModal = function (inventoryID, itemID, event) {
            if (event) event.preventDefault(); // Prevent page jump

            confirmModal.style.display = "flex";
            modalHeader.textContent = `Delete Item: ${itemID}`;

            confirmBtn.onclick = function () {
                window.location.href = 'delete.php?inventoryID=' + inventoryID + '&itemID=' + itemID;
            };
        };

        const closeBtn = document.querySelector('.confirmation-close');
        if (closeBtn) {
            closeBtn.onclick = function () {
                confirmModal.style.display = "none";
            };
        }

        if (cancelBtn) {
            cancelBtn.onclick = function () {
                confirmModal.style.display = "none";
            };
        }

        window.onclick = function (event) {
            if (event.target === confirmModal) {
                confirmModal.style.display = "none";
            }
        };

        const deleteButtons = document.querySelectorAll('.remove_icon');
        deleteButtons.forEach(button => {
            button.onclick = function (e) {
                e.preventDefault();
                const row = button.closest('tr');
                const itemID = row.querySelector('td:nth-child(3)').textContent;
                openConfirmModal(itemID);
            };
        });
    }

});

