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
        let xhr = new XMLHttpRequest();
        xhr.open('GET', 'itemsTable.php?search=' + encodeURIComponent(search) + '&sort=' + encodeURIComponent(sort) + '&order=' + encodeURIComponent(sortOrder), true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('account-table').innerHTML = xhr.responseText;

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

