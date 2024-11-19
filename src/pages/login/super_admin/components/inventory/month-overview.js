document.addEventListener("DOMContentLoaded", function () {
    // Event listener for search input
    const searchInput = document.getElementById('search');
    const sortDropdown = document.getElementById('sort');
    const sortOrderDropdown = document.getElementById('sortOrder');
    const refreshBtn = document.getElementById('refresh-btn');

    function loadTable(search = '', sort = 'name', sortOrder = 'asc') {
        // Get the current week, month, and year dynamically from the URL
        const currentUrl = new URL(window.location.href);
        const weekNumber = currentUrl.searchParams.get('week') || '1'; // Default to 1 if 'week' is not present
        const monthNumber = currentUrl.searchParams.get('month') || new Date().getMonth() + 1; // Default to current month
        const yearNumber = currentUrl.searchParams.get('year') || new Date().getFullYear(); // Default to current year

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `month-overviewtable.php?month=${encodeURIComponent(monthNumber)}&year=${encodeURIComponent(yearNumber)}&search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}&order=${encodeURIComponent(sortOrder)}`, true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('month-table').innerHTML = xhr.responseText;
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


    // Initial load of the table
    loadTable();
});
