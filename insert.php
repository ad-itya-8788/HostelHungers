<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = $_POST['pname'];
    $c = $_POST['cid'];
    $fp = $_POST['fullPrice'];
    $hp = $_POST['halfPrice'];
    $fq = $_POST['fullQuantity'];
    $hq = $_POST['halfQuantity'];
    $d = $_POST['description'];

    if (!$fp || !$hp || !$fq || !$hq) {
        echo "Missing price or quantity.";
        exit;
    }

    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    $imgPath = 'uploads/' . uniqid() . '-' . basename($_FILES['image']['name']);
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
        echo "Image upload failed.";
        exit;
    }

    $checkQry = "SELECT * FROM menu WHERE pname = '$n' AND cid = $c";
    if (pg_num_rows(pg_query($conn, $checkQry)) > 0) {
        echo "Item already exists.";
        exit;
    }

    $insertQry = "CALL insert_menu('$n', $c, '$d', true)";
    pg_query($conn, $insertQry);

    $pidQry = "SELECT currval(pg_get_serial_sequence('menu', 'pid')) AS pid";
    $pid = pg_fetch_result(pg_query($conn, $pidQry), 0, 'pid');

    $sizeQry = "INSERT INTO size_price (pid, size, price, quantity) 
                VALUES ('$pid', 'Full', '$fp', '$fq'), 
                       ('$pid', 'Half', '$hp', '$hq')";
    pg_query($conn, $sizeQry);

    $imgQry = "UPDATE menu SET img = '$imgPath' WHERE pid = $pid";
    pg_query($conn, $imgQry);

    echo "Item added successfully!";
}
?>
