document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-to-order-btn')) {
        const name = e.target.dataset.name;
        const size = e.target.dataset.size;
        const price = parseFloat(e.target.dataset.price); // Retrieve price from the button's data attribute
        const addToOrderDisabled = localStorage.getItem('addToOrderDisabled') === 'true';

        if (addToOrderDisabled) {
            // If the flag is true, prevent the addition to the order
            return; // Exit early if adding to the order is disabled
        }
        // Get existing orders from localStorage
        let orders = JSON.parse(localStorage.getItem('orders')) || [];

        // Check if the product already exists
        const existingOrder = orders.find(order => order.name === name && order.size === size);

        if (existingOrder) {
            // Increment quantity if product exists
            existingOrder.quantity += 1;
        } else {
            // Add new product if it doesn't exist
            orders.push({ name, size, price, quantity: 1 });
        }

        // Save updated orders to localStorage
        localStorage.setItem('orders', JSON.stringify(orders));

        // Update the panel
        renderPanel();
    }
});

function scrollToBottom() {
    const panelCardsContainer = document.querySelector('.panel-cards');
    panelCardsContainer.scrollTop = panelCardsContainer.scrollHeight;
}

function renderPanel() {
    const panelCardsContainer = document.querySelector('.panel-cards');
    const totalPriceContainer = document.querySelector('#total-price');
    const vatContainer = document.querySelector('#vat');
    const vatableContainer = document.querySelector('#subtotal');
    const orders = JSON.parse(localStorage.getItem('orders')) || [];
    let totalPrice = 0;
    let vatableAmount = 0;
    let vatAmount = 0;
    let totalItems = 0; // Variable to track the total number of items



    // Clear the current panel
    panelCardsContainer.innerHTML = '';


    // Populate panel with items
    orders.forEach(order => {
        totalPrice += order.price * order.quantity;

        // Calculate Vatable and VAT
        const vatable = order.price * order.quantity / 1.12;
        const vat = order.price * order.quantity - vatable;

        vatableAmount += vatable;
        vatAmount += vat;

        totalItems += order.quantity; // Add the quantity of each order to totalItems

        const card = document.createElement('div');
        card.className = 'panel-card';
        card.innerHTML = `
            <input type="number" class="quantity-input" value="${order.quantity}" min="1" data-name="${order.name}" data-size="${order.size}" />
            <div class="name-sizepanel">
                <span class="panel-name">${order.name}</span>
                <span class="panel-size">${order.size}</span>
            </div>
            <span class="panel-price">${(order.price * order.quantity).toFixed(2)}</span>
        <button class="delete-btn" data-name="${order.name}" data-size="${order.size}">X</button>
        `;
        panelCardsContainer.appendChild(card);
    });

    // Update item count and total price
    document.getElementById('total-items').textContent = totalItems; // Display the total number of items
    totalPriceContainer.textContent = `₱${totalPrice.toFixed(2)}`;

    // Update Vatable and VAT fields
    vatableContainer.textContent = `₱${vatableAmount.toFixed(2)}`;
    vatContainer.textContent = `₱${vatAmount.toFixed(2)}`;
    toggleButtonsBasedOnCash();
    togglePayButton(orders);

    updateItemLabel(totalItems); // Pass the updated totalItems count to the function

    scrollToBottom();
}


function togglePayButton(orders) {
    const payButton = document.querySelector("#payment-btn");  // Select the 'a' element with ID 'payment-btn'

    // Check if the button exists before trying to modify it
    if (payButton) {
        if (orders.length === 0) {
            // Disable the "Pay" button if there are no orders
            payButton.classList.add("disabled");
            payButton.disabled = true; // Optionally, set the disabled property (if it's an anchor, this may not work as expected)
        } else {
            // Enable the "Pay" button if there are orders
            payButton.classList.remove("disabled");
            payButton.disabled = false;
            const paymentButtonText = document.querySelector(".panel-actions a span");
            paymentButtonText.textContent = "Payment";  // Default button text
        }
    } else {
        console.log("Payment button not found.");
    }
}

function updateItemLabel(totalItems) {
    const itemLabel = document.getElementById('item-label');
    itemLabel.textContent = totalItems === 1 ? 'Item' : 'Items'; // Update the label based on the total item count
}


// Update quantity in localStorage when manually changed
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('quantity-input')) {
        const name = e.target.dataset.name;
        const size = e.target.dataset.size;
        const newQuantity = parseInt(e.target.value, 10);

        // Update quantity in localStorage
        let orders = JSON.parse(localStorage.getItem('orders')) || [];
        const orderToUpdate = orders.find(order => order.name === name && order.size === size);

        if (orderToUpdate) {
            orderToUpdate.quantity = newQuantity > 0 ? newQuantity : 1; // Prevent zero or negative quantities
        }

        localStorage.setItem('orders', JSON.stringify(orders));

        // Re-render panel
        renderPanel();
    }
});

// Load the panel on page load
document.addEventListener('DOMContentLoaded', renderPanel);

// Delete an item from the panel
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('delete-btn')) {
        const name = e.target.closest('.panel-card').querySelector('.panel-name').textContent;

        // Remove item from localStorage
        let orders = JSON.parse(localStorage.getItem('orders')) || [];
        orders = orders.filter(order => order.name !== name);
        localStorage.setItem('orders', JSON.stringify(orders));

        // Re-render panel
        renderPanel();
    }
});

// Get modal element
const modal = document.getElementById("cash-modal");

// Show the modal when the Payment button is clicked
document.querySelector(".panel-actions a").addEventListener("click", function (e) {
    e.preventDefault();
    modal.style.display = "block";
});

// Close the modal
function closeModal() {
    modal.style.display = "none";
}

function toggleButtonsBasedOnCash() {
    // const clearButton = document.querySelector("#clear-btn");
    const deleteButtons = document.querySelectorAll(".delete-btn"); // Select all delete buttons
    const storedCash = localStorage.getItem("cash");
    const storedChange = localStorage.getItem("change");
    const cashContainer = document.getElementById("cash-container");
    const changeContainer = document.getElementById("change-container");

    // Hide cash and change containers if storedCash and storedChange are null
    if (!storedCash && !storedChange) {
        if (cashContainer) cashContainer.style.display = 'none';
        if (changeContainer) changeContainer.style.display = 'none';
    } else {
        if (cashContainer) cashContainer.style.removeProperty('display');
        if (changeContainer) changeContainer.style.removeProperty('display');
    }

    deleteButtons.forEach(button => {
        if (storedCash && storedCash.trim() !== "") {
            button.classList.add('hidden');
            // if (clearButton) clearButton.classList.add('hidden');
        } else {
            button.classList.remove('hidden');
            // if (clearButton) clearButton.classList.remove('hidden');
        }
    });
}


function calculateChange() {
    const cashInput = parseFloat(document.getElementById("cash-input").value.replace('₱', '').replace(',', ''));
    const totalAmount = parseFloat(document.getElementById("total-price").textContent.replace('₱', '').replace(',', ''));

    const errorMessageContainer = document.getElementById('error-message');
    errorMessageContainer.style.display = 'none';

    if (isNaN(cashInput) || cashInput < totalAmount) {
        errorMessageContainer.style.display = 'block';
        errorMessageContainer.textContent = "Insufficient cash amount or invalid input. Please enter a valid amount.";
        return;
    }

    const change = cashInput - totalAmount;
    document.getElementById("cash").textContent = `₱${cashInput.toFixed(2)}`;
    document.getElementById("change").textContent = `₱${change.toFixed(2)}`;

    localStorage.setItem("cash", cashInput.toFixed(2));
    localStorage.setItem("change", change.toFixed(2));
    localStorage.setItem('addToOrderDisabled', 'true');


    const paymentButton = document.querySelector(".panel-actions a span");
    const arrowElement = document.querySelector(".panel-actions a .arrow");
    paymentButton.textContent = "Save";

    if (arrowElement) {
        arrowElement.style.display = 'none';
    }

    closeModal();
    toggleButtonsBasedOnCash();
}

// On page load, retrieve the cash and change values from localStorage
document.addEventListener("DOMContentLoaded", function () {
    renderPanel();
    toggleButtonsBasedOnCash();
    const storedCash = localStorage.getItem("cash");
    const storedChange = localStorage.getItem("change");
    const addToOrderDisabled = localStorage.getItem('addToOrderDisabled');

    // If the cash and change values exist in localStorage, update the UI
    if (storedCash && storedChange) {
        document.getElementById("cash").textContent = `₱${storedCash}`;
        document.getElementById("change").textContent = `₱${storedChange}`;

        // Also update the payment button text to "Save" and hide the arrow/image
        const paymentButton = document.querySelector(".panel-actions a span");
        const arrowElement = document.querySelector(".panel-actions a .arrow");


        paymentButton.textContent = "Save";
        if (arrowElement) {
            arrowElement.style.display = 'none';  // Hide the arrow/image
        }
    }


    console.log('addToOrderDisabled:', addToOrderDisabled);

});

// Clear all orders and reset cash/change
document.getElementById("clear-btn").addEventListener("click", function () {
    // Clear orders from localStorage
    localStorage.removeItem("orders");

    // Clear cash and change from localStorage
    localStorage.removeItem("cash");
    localStorage.removeItem("change");
    localStorage.setItem('addToOrderDisabled', 'false');
    // Re-render the panel
    renderPanel();

    // Reset the cash and change display
    document.getElementById("cash").textContent = "₱0.00";
    document.getElementById("change").textContent = "₱0.00";

    // Reset the payment button text to "Pay"
    const paymentButton = document.querySelector(".panel-actions a span");
    const arrowElement = document.querySelector(".panel-actions a .arrow");

    paymentButton.textContent = "Payment";
    if (arrowElement) {
        arrowElement.style.display = 'inline';  // Show the arrow/image again
    }
    toggleButtonsBasedOnCash();
});

document.querySelector(".panel-actions a").addEventListener("click", function (e) {
    e.preventDefault();

    const paymentButton = document.querySelector(".panel-actions a span");

    // Only show the modal if the button text is "Payment"
    if (paymentButton.textContent === "Payment") {
        modal.style.display = "block";  // Show the modal
    }

    // If the button text is "Save", run the save functionality
    else if (paymentButton.textContent === "Save") {
        modal.style.display = "none";  // Hide the modal
        // Get the data from localStorage
        const orders = JSON.parse(localStorage.getItem('orders')) || [];
        const cash = localStorage.getItem('cash');
        const change = localStorage.getItem('change');

        // Prepare the data to send
        const data = {
            orders: orders,
            cash: cash,
            change: change
        };

        // Send the data to the PHP server using AJAX
        sendDataToServer(data);
    }
});

function sendDataToServer(data) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "submit-order.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");

    // Send the data to PHP
    xhr.send(JSON.stringify(data));

    // Handle the response from PHP
    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log("Raw response:", xhr.responseText); // Debugging the raw response
            try {
                const response = JSON.parse(xhr.responseText);

                if (response.success) {
                    showModal("success", "Order has been saved successfully.");
                    localStorage.removeItem("orders");
                    localStorage.removeItem("cash");
                    localStorage.removeItem("change");
                    renderPanel();

                    document.getElementById("cash").textContent = "₱0.00";
                    document.getElementById("change").textContent = "₱0.00";
                    document.getElementById("cash-input").value = ""; // Clear the cash input field
                    const paymentButton = document.querySelector(".panel-actions a span");
                    const arrowElement = document.querySelector(".panel-actions a .arrow");
                    const clearButton = document.querySelector("#clear-btn");
                    clearButton.classList.remove('hidden');

                    localStorage.setItem('addToOrderDisabled', 'false');

                    paymentButton.textContent = "Payment";
                    if (arrowElement) {
                        arrowElement.style.display = 'inline'; // Show the arrow/image again
                    }
                    const invID = response.invID; // This is the invID returned from PHP

                    generateInvoicePDF(invID); // Generate the PDF with the received invID

                } else {
                    const errorMessage = response.error || "Failed to save your data. Please try again.";

                    if (response.ingredients && response.ingredients.length > 0) {
                        let ingredientsMessage = '';
                        let groupedIngredients = {};

                        // Group ingredients by product
                        response.ingredients.forEach(stock => {
                            // Split the string by the first comma to separate the product and ingredients part
                            const parts = stock.split(', Ingredients:');

                            if (parts.length === 2) {
                                const product = parts[0].replace("Product: ", "").trim(); // Extract product name
                                const ingredients = parts[1].trim(); // Extract the ingredients part

                                // Split the ingredients by commas and trim whitespace for each ingredient
                                const ingredientList = ingredients.split(',').map(ingredient => ingredient.trim());

                                // Group ingredients by product
                                if (!groupedIngredients[product]) {
                                    groupedIngredients[product] = [];
                                }
                                groupedIngredients[product] = groupedIngredients[product].concat(ingredientList);
                            } else {
                                // If the string doesn't have both product and ingredients
                                console.error("Invalid format for stock:", stock);
                            }
                        });

                        // Build the message for the modal
                        Object.keys(groupedIngredients).forEach(product => {
                            ingredientsMessage += `<strong>${product}</strong><br>`;
                            groupedIngredients[product].forEach(ingredient => {
                                ingredientsMessage += `• ${ingredient}<br>`;
                            });
                            ingredientsMessage += "<br>";
                        });

                        // Check if ingredientsMessage is still empty, meaning no valid products or ingredients were found
                        if (ingredientsMessage === '') {
                            showModal("error", "No valid ingredients to display.");
                        } else {
                            // Show modal with the grouped product and ingredient information
                            showModal("error", `${errorMessage} <br> ${ingredientsMessage}`);
                        }
                    } else {
                        showModal("error", errorMessage);
                    }



                }
            } catch (e) {
                console.error("Error parsing response JSON:", e);
                showModal("error", "An error occurred while processing the response. Please try again.");
            }
        } else {
            console.error("Server error:", xhr.status, xhr.statusText);
            showModal("error", "There was a server error. Please try again.");
        }
    };

    // Add an error handler for network issues or server unavailability
    xhr.onerror = function () {
        console.error("Request failed due to network issues.");
        showModal("error", "Network error. Please check your connection and try again.");
    };
}

function generateInvoicePDF(invID) {
    var width = 100; // Width in mm (receipt size)
    var height = 200; // Height in mm (receipt size)

    // Convert to pixels (approximately)
    var widthPx = width * 3.7795275591; // Convert mm to pixels (1mm = 3.7795275591px)
    var heightPx = height * 3.7795275591;

    // Open the window with the specified size
    var newWindow = window.open('generate_inv.php?invID=' + invID, '_blank', 'width=' + widthPx + ',height=' + heightPx);

    // Focus on the new window
    if (newWindow) {
        newWindow.focus();
    }
}

// Function to show a modal with a dynamic message
function showModal(type, message) {
    const modal = document.getElementById(type + "Modal");
    const modalTitle = modal.querySelector("h2");
    const modalMessage = modal.querySelector("p");

    // Update modal content based on type (success or error)
    if (type === "success") {
        modalTitle.textContent = "Success!";
        modalMessage.innerHTML = message;
        modal.style.display = "block";
    } else {
        modalTitle.textContent = "Error!";
        modalMessage.innerHTML = message;
        modal.style.display = "block";
    }
}

// Function to close the modal
function closeModal2(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = "none";
    window.location.reload(); // This will refresh the page

}
