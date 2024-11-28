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
    function loadTable(search = '', sort = 'name', sortOrder = 'asc') {
        console.log('Search:', search);
        console.log('Sort:', sort);
        console.log('Order:', sortOrder);

        let xhr = new XMLHttpRequest();
        xhr.open('GET', 'ingredients-table.php?search=' + encodeURIComponent(search) + '&sort=' + encodeURIComponent(sort) + '&order=' + encodeURIComponent(sortOrder), true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('ingredient-table').innerHTML = xhr.responseText;
            }
            document.getElementById('loader').style.display = 'none';
        };

        xhr.onerror = function () {
            console.error("Failed to load the data.");
            document.getElementById('loader').style.display = 'none';
        };

        xhr.send();
    }

});
