<?php
include 'dbconnect.php';

function displayMenuItems($searchQuery = "") 
{
    global $conn;

    if (!empty($searchQuery)) {
        $sql = "SELECT m.pid, m.pname, m.description, m.img, sp.size, sp.price, sp.quantity 
                FROM menu m
                LEFT JOIN size_price sp ON m.pid = sp.pid
                WHERE m.pname ILIKE '%" . pg_escape_string($searchQuery) . "%'";
    } else 
    {
        $sql = "SELECT m.pid, m.pname, m.description, m.img, sp.size, sp.price, sp.quantity 
                FROM menu m
                LEFT JOIN size_price sp ON m.pid = sp.pid";
    }

    $result = pg_query($conn, $sql);

    if (!$result) {
        echo "Error fetching data.";
        return;
    }

    if (pg_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr><th>Image</th><th>Name</th><th>Description</th><th>Full Size</th><th>Half Size</th></tr>";

        $currentPid = null;
        $fullPrice = $halfPrice = $fullQuantity = $halfQuantity = '';

        while ($row = pg_fetch_assoc($result)) {
            if ($row['pid'] != $currentPid) {
                if ($currentPid !== null) {
                    echo "<td>Price: $fullPrice<br><hr>Quantity: $fullQuantity</td>";
                    echo "<td>Price: $halfPrice<br><hr>Quantity: $halfQuantity</td>";
                    echo "</tr>";
                }

                $currentPid = $row['pid'];
                $fullPrice = $halfPrice = $fullQuantity = $halfQuantity = '';

                echo "<tr>";
                echo "<td><img src='" . $row['img'] . "' alt='" . $row['pname'] . "'></td>";
                echo "<td>" . $row['pname'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
            }

            if ($row['size'] == 'Full') {
                $fullPrice = '₹' . $row['price'];
                $fullQuantity = $row['quantity'];
            } elseif ($row['size'] == 'Half') {
                $halfPrice = '₹' . $row['price'];
                $halfQuantity = $row['quantity'];
            }
        }

        echo "<td>Price: $fullPrice<br><hr>Quantity: $fullQuantity</td>";
        echo "<td>Price: $halfPrice<br><hr>Quantity: $halfQuantity</td>";
        echo "</tr>";

        echo "</table>";
    } else {
        echo "<p class='notfound'>No items found.</p>";
    }
}

$searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';

displayMenuItems($searchQuery);
?>
<html>
    <head><style>.notfound {
    background-color: #ffcccc; 
    color: #b30000; 
    padding: 20px;
    text-align: center;
    border: 2px solid #b30000;
    font-size: 1.5em;
    font-weight: bold;
    border-radius: 8px;
    margin-top: 20px;
    width: 80%;
    margin: 20px auto;
}
</style></head>
    </html>