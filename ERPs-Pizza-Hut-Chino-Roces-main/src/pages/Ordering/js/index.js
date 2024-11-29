
       
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

