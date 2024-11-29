<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Same styling as before */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .form-group input[type="file"] {
            padding: 5px;
        }

        .ingredients {
            margin-bottom: 20px;
        }

        .ingredient {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .ingredient input,
        .ingredient select {
            margin-right: 10px;
        }

        .ingredient .remove-btn {
            background: #e74c3c;
            color: #fff;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
        }

        .ingredient .remove-btn:hover {
            background: #c0392b;
        }

        .add-ingredient-btn {
            display: block;
            width: 100%;
            background: #3498db;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            text-align: center;
        }

        .add-ingredient-btn:hover {
            background: #2980b9;
        }

        .submit-btn {
            display: block;
            width: 100%;
            background: #006D6D;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            text-align: center;
        }

        .submit-btn:hover {
            background: #004C4C;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Add Product</h1>
        <form id="addProductForm" action="submit-product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="productName">Product Name:</label>
                <input type="text" id="productName" name="productName" required>
            </div>
            <div class="form-group">
                <label for="productImage">Product Image:</label>
                <input type="file" id="productImage" name="productImage" accept="image/*">
            </div>

            <div class="form-group">
                <label for="slogan">Slogan:</label>
                <input type="text" id="slogan" name="slogan" required>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="beverages">Beverages</option>
                    <option value="pasta">Pasta</option>
                    <option value="pizza">Pizza</option>
                </select>
            </div>

            <!-- Add size dropdown that will be updated dynamically based on category selection -->
            <div class="form-group">
                <label for="size">Size:</label>
                <select id="size" name="size" required>
                    <!-- Size options will be populated dynamically -->
                </select>
            </div>

            <div class="ingredients" id="ingredients">
                <h3>Ingredients</h3>
                <div class="ingredient">
                    <input type="text" name="ingredientName[]" placeholder="Ingredient Name" required>
                    <select name="measurement[]">
                        <option value="grams">Grams</option>
                        <option value="kg">Kg</option>
                        <option value="pcs">Pcs</option>
                        <option value="bottle">Bottle</option>
                    </select>
                    <input type="number" name="quantity[]" placeholder="Quantity" required>
                    <button type="button" class="remove-btn" onclick="removeIngredient(this)">-</button>
                </div>
            </div>

            <button type="button" class="add-ingredient-btn" onclick="addIngredient()">+ Add Ingredient</button>
            <button type="submit" class="submit-btn">Submit</button>
        </form>

    </div>

    <script>
        // Add event listener for category change
        document.getElementById('category').addEventListener('change', function() {
            updateSizeOptions(this.value);
        });

        // Function to update size options based on selected category
        function updateSizeOptions(category) {
            const sizeSelect = document.getElementById('size');
            sizeSelect.innerHTML = ''; // Clear current options

            let options = [];

            // Determine the size options based on the selected category
            if (category === 'pizza') {
                options = ['9inch Pan Pizza'];
            } else if (category === 'pasta') {
                options = ['Regular'];
            } else if (category === 'beverages') {
                options = ['1.5L', '500ml'];
            }

            // Populate the size select dropdown with options
            options.forEach(function(size) {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size;
                sizeSelect.appendChild(option);
            });
        }

        // Call function to initialize the size options on page load
        updateSizeOptions(document.getElementById('category').value);

        // Ingredient functions
        function addIngredient() {
            const ingredientsDiv = document.getElementById('ingredients');
            const newIngredientDiv = document.createElement('div');
            newIngredientDiv.className = 'ingredient';
            newIngredientDiv.innerHTML = `
                <input type="text" name="ingredientName[]" placeholder="Ingredient Name" required>
                <select name="measurement[]">
                    <option value="grams">Grams</option>
                    <option value="kg">Kg</option>
                    <option value="pcs">Pcs</option>
                    <option value="bottle">Bottle</option>
                </select>
                <input type="number" name="quantity[]" placeholder="Quantity" required>
                <button type="button" class="remove-btn" onclick="removeIngredient(this)">-</button>
            `;
            ingredientsDiv.appendChild(newIngredientDiv);
        }

        function removeIngredient(button) {
            const ingredientDiv = button.parentElement;
            ingredientDiv.remove();
        }

        // Form submission handling
        document.getElementById('addProductForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form from submitting the usual way

            var formData = new FormData(this);

            fetch('submit-product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Product added successfully!');
                        window.close(); // Close the current window
                    } else {
                        alert('Error: ' + data.message); // Show error message
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
        });
    </script>

</body>

</html>