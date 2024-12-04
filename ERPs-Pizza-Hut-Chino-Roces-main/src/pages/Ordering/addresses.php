<?php
session_start();
include 'connection/database-conn.php';
include 'connection/database-db.php';

// Assuming you have a session for the logged-in user
if (!isset($_SESSION['uid'])) {
    echo "You must be logged in to view your address.";
    exit();
}

$currentUserId = $_SESSION['uid']; // Get the logged-in user ID from the session

// Retrieve user addresses
$sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId";
$result = $conn->query($sql);

// Check if the user has an address saved
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userAddressJson = $row['address']; // Get the saved address as a JSON string

    // Decode the JSON into an associative array
    $addresses = json_decode($userAddressJson, true);

    // Check if JSON decoding was successful
    if (json_last_error() === JSON_ERROR_NONE) {
        $addressesFound = true;
    } else {
        $addressesFound = false;
    }
} else {
    $addressesFound = false;
}

// Function to capitalize the first letter of each word and convert the rest to lowercase
function capitalizeAddress($str)
{
    return ucwords(strtolower($str));
}

// Handling address update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_address'])) {
        // Set the specific address to edit mode
        $addressId = $_POST['address_id'];
        foreach ($addresses as &$address) {
            if ($address['id'] == $addressId) {
                $address['edit_mode'] = true; // Set edit_mode to true for the specific address
            }
        }
    }

    // Save changes
    if (isset($_POST['save_changes'])) {
        $addressId = $_POST['address_id'];
        $newAddress = $_POST['address'];
        $newName = $_POST['name'];

        // Capitalize address fields
        $newAddress = capitalizeAddress($newAddress);
        $newName = capitalizeAddress($newName);

        foreach ($addresses as &$address) {
            if ($address['id'] == $addressId) {
                $address['address'] = $newAddress;
                $address['name'] = $newName;
                // Remove edit_mode after saving changes
                unset($address['edit_mode']);
                break;
            }
        }

        // Update the JSON in the database
        $updatedAddressesJson = json_encode($addresses);
        $escapedJson = mysqli_real_escape_string($conn, $updatedAddressesJson);

        $sql = "UPDATE customerInfo SET address = '$escapedJson' WHERE uid = $currentUserId";
        $conn->query($sql);
        echo "
            <script>
                if (window.opener) {
                    window.opener.location.reload(); // Refresh the parent window
                }
            </script>
        ";
        echo "
            <script>
                if (window.opener) {
                    window.opener.location.reload(); // Refresh the parent window
                }
            </script>
        ";
        $conn->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Handle address removal
    if (isset($_POST['remove_address'])) {
        $addressId = $_POST['address_id'];

        // Remove the address from the database
        $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $addresses = json_decode($row['address'], true);

        foreach ($addresses as $key => $address) {
            if ($address['id'] == $addressId) {
                unset($addresses[$key]);
                break;
            }
        }

        // Update the JSON in the database
        $updatedAddressesJson = json_encode(array_values($addresses)); // Re-index the array
        $escapedJson = mysqli_real_escape_string($conn, $updatedAddressesJson);

        $sql = "UPDATE customerInfo SET address = '$escapedJson' WHERE uid = $currentUserId";
        $conn->query($sql);

        echo "
            <script>
                if (window.opener) {
                    window.opener.location.reload(); // Refresh the parent window
                }
                window.history.back(); // Go back to the form page

            </script>
        ";
        exit();
    }

    // Handle adding a new address
    if (isset($_POST['add_address'])) {
        // Get the new address details from the form
        $houseNo = preg_replace('/[^\w\s]/', '', $_POST['house_no']);
        $street = preg_replace('/[^\w\s]/', '', $_POST['street']); // Remove special characters
        $street = preg_replace('/\s*st$/i', '', $street); // Remove "st" at the end, case-insensitive
        $barangay = preg_replace('/[^\w\s]/', '', $_POST['barangay']);
        $city = preg_replace('/[^\w\s]/', '', $_POST['city']);
        $province = preg_replace('/[^\w\s]/', '', $_POST['province']);
        $newAddress = "$houseNo, $street, $barangay, $city, $province";
        $newName = $_POST['address_name'];

        // Capitalize the address fields
        $newAddress = capitalizeAddress($newAddress);
        $newName = capitalizeAddress($newName);

        // Retrieve the current list of addresses from the database
        $sql = "SELECT address FROM customerInfo WHERE uid = $currentUserId";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $addresses = json_decode($row['address'], true);

        // Find the highest existing ID and increment it by 1
        $maxId = 0;
        foreach ($addresses as $address) {
            $addressId = (int) $address['id'];  // Make sure ID is treated as an integer
            if ($addressId > $maxId) {
                $maxId = $addressId;
            }
        }

        // Set the new address ID as the next available number
        $newAddressId = $maxId + 1;

        // Create a new address entry
        $newAddressArray = [
            'id' => $newAddressId,  // Use the incremented ID
            'name' => $newName,      // Title for the address
            'address' => $newAddress
        ];

        // Add the new address to the existing addresses array
        $addresses[] = $newAddressArray;

        // Update the JSON in the database
        $updatedAddressesJson = json_encode($addresses);
        $escapedJson = mysqli_real_escape_string($conn, $updatedAddressesJson);
        $sql = "UPDATE customerInfo SET address = '$escapedJson' WHERE uid = $currentUserId";
        $conn->query($sql);

        echo "
            <script>
                if (window.opener) {
                    window.opener.location.reload(); // Refresh the parent window
                }
                window.history.back(); // Go back to the form page

            </script>
        ";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Saved Addresses</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        h3 {
            color: #007BFF;
            text-align: center;
            margin-top: 50px;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .address-card {
            background-color: #fff;
            width: 80%;
            max-width: 600px;
            margin: 15px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .address-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .address-title {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .address-text {
            font-size: 1.2em;
            color: #555;
            line-height: 1.6;
            width: 100%;
        }

        .address-buttons {
            margin-top: 20px;
        }

        .address-buttons button {
            padding: 8px 16px;
            margin-right: 10px;
            border: none;
            background-color: #007BFF;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        .address-buttons button:hover {
            background-color: #0056b3;
        }

        .no-address {
            font-size: 1.2em;
            color: #888;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #555;
        }

        .address-input {
            width: 90%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .address-form {
            background-color: #fff;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .address-form input,
        .address-form textarea {
            width: 90%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .address-form button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            margin-top: 15px;
        }

        .address-form button:hover {
            background-color: #0056b3;
        }

        .add-address-button {
            padding: 12px 24px;
            background-color: #28a745;
            color: white;
            font-size: 1.2em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 30px;
        }

        .add-address-button:hover {
            background-color: #218838;
        }

        .cancel-button {
            background-color: #dc3545;
            padding: 10px 20px;
            color: white;
            font-size: 1.2em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        .cancel-button:hover {
            background-color: #c82333;
        }

        /* Specific style for Remove Address button */
        .address-buttons button[name="remove_address"] {
            background-color: #dc3545;
        }

        .address-buttons button[name="remove_address"]:hover {
            background-color: #c82333;
            /* Dark red for hover */
            transition: background-color 0.3s ease;
        }

        #addressForm {
            display: none;
            /* Hidden by default */
        }

        @media (max-width: 400px) {}
    </style>
</head>

<body>
    <h3>Your Saved Addresses</h3>

    <div class="container">
        <?php if ($addressesFound): ?>
            <?php foreach ($addresses as $address): ?>
                <div class="address-card">
                    <form action="" method="POST">
                        <div class="address-title">
                            <?php if (isset($address['edit_mode']) && $address['edit_mode']): ?>
                                <input type="text" name="name" value="<?php echo isset($address['name']) ? htmlspecialchars($address['name']) : ''; ?>" class="address-input" required>
                            <?php else: ?>
                                <?php echo isset($address['name']) ? htmlspecialchars($address['name']) : 'No name'; ?>
                            <?php endif; ?>
                        </div>
                        <div class="address-text">
                            <?php if (isset($address['edit_mode']) && $address['edit_mode']): ?>
                                <textarea name="address" class="address-input" required><?php echo isset($address['address']) ? htmlspecialchars($address['address']) : ''; ?></textarea>
                            <?php else: ?>
                                <?php echo isset($address['address']) ? htmlspecialchars($address['address']) : 'No address'; ?>
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                        <div class="address-buttons">
                            <?php if (isset($address['edit_mode']) && $address['edit_mode']): ?>
                                <button type="submit" name="save_changes">Save Changes</button>
                            <?php else: ?>
                            <?php endif; ?>
                            <button type="submit" name="remove_address" class="remove-address-btn">Remove Address</button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-address">
                You have no saved addresses.
            </div>
        <?php endif; ?>

        <!-- Button to Toggle New Address Form -->
        <button class="add-address-button" onclick="toggleAddressForm()" id="addAddressButton">Add New Address</button>

        <!-- Add New Address Form -->
        <div class="address-form" id="addressForm">
            <h4>Add a New Address</h4>
            <form action="" method="POST">
                <input type="text" name="address_name" placeholder="Address Title (e.g., Home, Office)" required>
                <input type="text" name="house_no" placeholder="House No." required>
                <input type="text" name="street" placeholder="Street" required>
                <input type="text" name="barangay" placeholder="Barangay" required>
                <input type="text" name="city" placeholder="City" required>
                <input type="text" name="province" placeholder="Province" required>
                <button type="submit" name="add_address">Add Address</button>
                <button type="button" class="cancel-button" onclick="toggleAddressForm()">Cancel</button>
            </form>
        </div>

    </div>

    <script>
        function toggleAddressForm() {
            var form = document.getElementById('addressForm');
            var addButton = document.getElementById('addAddressButton');

            // Toggle form visibility
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block'; // Show the form
                addButton.style.display = 'none'; // Hide the button
                // Smoothly scroll to the form
                form.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                form.style.display = 'none'; // Hide the form
                addButton.style.display = 'block'; // Show the button
            }
        }

        // Add click event listener to the "Add New Address" button
        document.getElementById("addAddressButton").addEventListener("click", function() {
            // Find the address form element
            const addressForm = document.getElementById("addressForm");

            // Smoothly scroll to the address form
            addressForm.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        });
    </script>
</body>

</html>