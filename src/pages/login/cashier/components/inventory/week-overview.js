document.addEventListener("DOMContentLoaded", function () {

    // Event listener for search input
    const searchInput = document.getElementById('search');
    const sortDropdown = document.getElementById('sort');
    const sortOrderDropdown = document.getElementById('sortOrder');
    const refreshBtn = document.getElementById('refresh-btn');

    function loadTable(search = '', sort = 'name', sortOrder = 'asc') {

        // Get the current week number dynamically from the URL
        const currentUrl = new URL(window.location.href);
        const weekNumber = currentUrl.searchParams.get('week') || '1'; // Default to 1 if 'week' is not present

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `week-overviewtable.php?week=${encodeURIComponent(weekNumber)}&search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}&order=${encodeURIComponent(sortOrder)}`, true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('week-table').innerHTML = xhr.responseText;
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


    // Event listener for search input
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            loadTable(this.value, sortDropdown.value, sortOrderDropdown.value);
        });
    }

    // Event listener for sort dropdown
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function () {
            loadTable(searchInput.value, this.value, sortOrderDropdown.value);
        });
    }

    // Event listener for sort order dropdown
    if (sortOrderDropdown) {
        sortOrderDropdown.addEventListener('change', function () {
            loadTable(searchInput.value, sortDropdown.value, this.value);
        });
    }

    // Event listener for refresh button
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function (e) {
            e.preventDefault();

            document.getElementById('loader').style.display = 'block';
            const loaderCircle = this.querySelector('.loader-circle');
            const refreshIcon = this.querySelector('img');

            loaderCircle.style.display = 'block';
            refreshIcon.style.display = 'none';

            loadTable(searchInput.value, sortDropdown.value, sortOrderDropdown.value);

            setTimeout(() => {
                loaderCircle.style.display = 'none';
                refreshIcon.style.display = 'block';
            }, 1000);
        });
    }

    // Function to attach modal button event listeners
    function attachModalListeners() {
        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
        }

        // Close modal buttons
        document.querySelectorAll('.modal-header .close, #cancelBtn, #cancelBtn3').forEach(btn => {
            btn.onclick = closeModal;
        });
    }

    // Function to attach delete listeners
    function attachDeleteListeners() {
        const confirmModal = document.getElementById("uniqueConfirmModal");
        const confirmBtn = document.getElementById("uniqueConfirmBtn");
        const cancelBtn = document.getElementById("uniqueCancelBtn");
        const modalHeader = document.querySelector('.confirmation-modal-header h2');

        // Open confirm modal
        window.openConfirmModal = function (inventoryID, itemID, event) {
            if (event) event.preventDefault(); // Prevent page jump
            confirmModal.style.display = "flex";
            modalHeader.textContent = `Delete Item: ${itemID}`;

            confirmBtn.onclick = function () {
                window.location.href = `delete.php?inventoryID=${inventoryID}&itemID=${itemID}`;
            };
        };

        // Close modal on click outside
        window.onclick = function (event) {
            if (event.target === confirmModal) {
                confirmModal.style.display = "none";
            }
        };

        // Attach delete functionality to remove icon buttons
        document.querySelectorAll('.remove_icon').forEach(button => {
            button.onclick = function (e) {
                e.preventDefault();
                const itemID = button.closest('tr').querySelector('td:nth-child(3)').textContent;
                openConfirmModal(itemID);
            };
        });

        // Close confirm modal
        if (cancelBtn) {
            cancelBtn.onclick = function () {
                confirmModal.style.display = "none";
            };
        }
    }

    // Initial load of the table
    loadTable();

});
