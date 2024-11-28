<?php
include '../../connection/database.php';
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['productName'];
    $slogan = $_POST['slogan'];
    $category = $_POST['category'];
    $size = $_POST['size'];  // New variable for size
    $ingredientNames = $_POST['ingredientName'];
    $measurements = $_POST['measurement'];
    $quantities = $_POST['quantity'];

    // Handle the product image upload
    $imagePath = '';
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $uploadDir = '../../assets/products/';  // Directory where images will be uploaded
        $fileName = $_FILES['productImage']['name'];
        $fileTmpName = $_FILES['productImage']['tmp_name'];
        $fileType = $_FILES['productImage']['type'];

        // Ensure only image files are uploaded
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes)) {
            $imagePath = $uploadDir . basename($fileName);
            if (move_uploaded_file($fileTmpName, $imagePath)) {
                // Image uploaded successfully, continue with database insertion
            } else {
                echo "Error uploading the image.";
                exit;
            }
        } else {
            echo "Only image files are allowed.";
            exit;
        }
    } else {
        echo "No image uploaded or an error occurred.";
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

    // Insert product into the database (including image path, slogan, category, and size)
    $insertProductSql = "INSERT INTO products (name, slogan, category, size, ingredients, img) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertProductSql);
    $stmt->bind_param("ssssss", $productName, $slogan, $category, $size, $ingredientsJson, $imagePath);

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
