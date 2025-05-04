<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $productName = $_POST['pname'];
    $categoryId = $_POST['cid'];
    $fullPrice = $_POST['fullPrice'];
    $halfPrice = $_POST['halfPrice'];
    $fullQuantity = $_POST['fullQuantity'];
    $halfQuantity = $_POST['halfQuantity'];
    $description = $_POST['description'];

    // 1. Check if any required fields are empty
    if (empty($fullPrice) || empty($halfPrice) || empty($fullQuantity) || empty($halfQuantity)) {
        echo "Missing price or quantity.";
        exit;
    }

    // 2. Handle image upload
    $uploadsDir = 'uploads/';
    if (!file_exists($uploadsDir)) {
        mkdir($uploadsDir, 0777, true); // Create 'uploads' directory if not exists
    }

    // Generate a unique name for the uploaded image
    $imgName = uniqid() . '-' . basename($_FILES['image']['name']);
    $imgPath = $uploadsDir . $imgName;

    // 3. Move the uploaded image to the server
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
        echo "Image upload failed.";
        exit;
    }

    // 4. Check if the product already exists
    $checkProductQuery = "SELECT 1 FROM menu WHERE pname = '$productName' AND cid = $categoryId";
    $result = pg_query($conn, $checkProductQuery);

    if (pg_num_rows($result) > 0) {
        echo "Item already exists.";
        exit;
    }

    // 5. Modify description based on selected category (Veg or Non-Veg)
    if ($categoryId == 1) {  // Veg category
        $description = "Veg:" . $description;  
    } elseif ($categoryId == 2) {  // Non-Veg category
        $description = "Non-Veg:" . $description;
    }

    // 6. Insert the menu item into the 'menu' table
    $insertMenuQuery = "INSERT INTO menu (pname, cid, description, avl, img) 
                        VALUES ('$productName', $categoryId, '$description', true, '$imgPath')";
    pg_query($conn, $insertMenuQuery);

    // 7. Retrieve the last inserted 'mid' (menu ID) for size & price insertion
    $midQuery = "SELECT currval(pg_get_serial_sequence('menu', 'mid')) AS mid";
    $midResult = pg_query($conn, $midQuery);
    $mid = pg_fetch_result($midResult, 0, 'mid');

    // 8. Insert size and price information into 'size_price' table
    $insertSizesQuery = "INSERT INTO size_price (mid, size, price, quantity) 
                         VALUES ($mid, 'Full', '$fullPrice', '$fullQuantity'), 
                                ($mid, 'Half', '$halfPrice', '$halfQuantity')";
    pg_query($conn, $insertSizesQuery);

    // 9. Return success message
    echo "Item added successfully!";
}
?>
