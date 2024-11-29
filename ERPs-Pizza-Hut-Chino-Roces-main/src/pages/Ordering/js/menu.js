        $(document).ready(function () {
            // Function to calculate and update subtotal
            function updateSubtotal(cartId) {
                var subtotal = 0;
                $('.price-display').each(function () {
                    var currentCartId = $(this).data('id');
                    var quantity = parseInt($(`select[data-id="${currentCartId}"]`).val());
                    var price = parseFloat($(`.price[data-id="${currentCartId}"]`).val());
                    var newPrice = quantity * price;
                    $(this).text('₱' + newPrice.toFixed(0));
                    subtotal += newPrice;
                });

                var deliveryFee = 50; // Change this to your actual delivery fee
                var total = subtotal + deliveryFee;

                $('#subtotal').text('₱' + subtotal.toFixed(0));
                $('#delivery_fee').text('₱' + deliveryFee.toFixed(0));
                $('#total_amount').text('₱' + total.toFixed(0));

                // For cart3
                $('#total_amount1').text('₱' + subtotal.toFixed(0));
            }

            // Event listener for quantity change
            $('.quantity').change(function () {
                var cartId = $(this).data('id');
                updateSubtotal(cartId);
            });
    
            // Initial update of subtotal, delivery fee, and total amount
            $('.quantity').each(function () {
                var cartId = $(this).data('id');
                var quantity = parseInt($(this).val());
                var price = parseFloat($('.price[data-id="' + cartId + '"]').val());
                var newPrice = quantity * price;
                $('.price-display[data-id="' + cartId + '"]').text('₱' + newPrice.toFixed(0));
                updateSubtotal(cartId);
            });
        });

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

