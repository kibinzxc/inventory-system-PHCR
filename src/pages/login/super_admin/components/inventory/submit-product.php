<?php
include '../../connection/database.php';
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['productName'];
    $slogan = $_POST['slogan'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $ingredientNames = $_POST['ingredientName'];
    $measurements = $_POST['measurement'];
    $quantities = $_POST['quantity'];

    $fileName = ''; // To store the image file name
    $response = []; // Initialize response for JSON output

    // Handle the product image upload
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $uploadDirs = [
            '../../assets/products/',
            '../../../admin/assets/products/',
            '../../../stockman/assets/products/',
            '../../../cashier/assets/products/'
        ];

        $fileName = basename($_FILES['productImage']['name']);
        $fileTmpName = $_FILES['productImage']['tmp_name'];
        $fileType = $_FILES['productImage']['type'];

        // Ensure only image files are uploaded
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes)) {
            $firstDir = $uploadDirs[0];
            $firstImagePath = $firstDir . $fileName;

            // Move to the first directory
            if (!move_uploaded_file($fileTmpName, $firstImagePath)) {
                echo json_encode(['status' => 'error', 'message' => "Error uploading the image to $firstDir."]);
                exit;
            }

            // Copy to the other directories
            foreach (array_slice($uploadDirs, 1) as $uploadDir) {
                $imagePath = $uploadDir . $fileName;
                if (!copy($firstImagePath, $imagePath)) {
                    echo json_encode(['status' => 'error', 'message' => "Error copying the image to $uploadDir."]);
                    exit;
                }
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Only image files are allowed.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No image uploaded or an error occurred.']);
        exit;
    }

    // Prepare ingredients array
    $ingredients = [];
    for ($i = 0; $i < count($ingredientNames); $i++) {
        $ingredients[] = [
            'ingredient_name' => $ingredientNames[$i],
            'measurement' => $measurements[$i],
            'quantity' => $quantities[$i]
        ];
    }

    // Convert ingredients array to JSON
    $ingredientsJson = json_encode($ingredients);

    // Insert product into the database (including only the file name for the image)
    $insertProductSql = "INSERT INTO products (name, slogan, category, size, ingredients, img) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertProductSql);
    $stmt->bind_param("ssssss", $productName, $slogan, $category, $size, $ingredientsJson, $fileName);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
