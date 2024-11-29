$(document).ready(function () {
const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    // Dummy data for demonstration
    const data = [
        'Seafood Supreme',
        'Pepperoni Pizza',
    
        // Add more data as needed
    ];

    searchInput.addEventListener('input', function () {
        const inputValue = this.value.toLowerCase();
        const filteredData = data.filter(item => item.toLowerCase().includes(inputValue));
        displayResults(filteredData);
    });

    function displayResults(results) {
        searchResults.innerHTML = '';
        if (results.length === 0) {
            searchResults.style.display = 'none';
            return;
        }

        results.forEach(result => {
            const li = document.createElement('li');
            li.textContent = result;
            li.addEventListener('click', function () {
                searchInput.value = result;
                searchResults.style.display = 'none';
            });
            searchResults.appendChild(li);
        });

        searchResults.style.display = 'block';
    }

    // Hide results on outside click
    document.addEventListener('click', function (e) {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });
});