<html>
    <body>
        <header>
            Admin Dashboard - Digital Menu Card System - Delete Items
        </header>
        <div class="main-content">
            <?php
            include 'dbconnect.php';

            function showMenu() {
                global $conn;

                $query = "SELECT pid, pname, description, img FROM menu ORDER BY pid";
                $res = pg_query($conn, $query);
                
                if (!$res) {
                    echo "Error in fetching data.";
                    return;
                }

                if (pg_num_rows($res) > 0) {
                    echo "<h2>Menu</h2>";
                    echo "<table>";
                    echo "<tr><th>Image</th><th>Name</th><th>Description</th><th>Size & Price</th><th>Action</th></tr>";
                    
                    while ($item = pg_fetch_assoc($res)) {
                        $sizeQuery = "SELECT size, price FROM size_price WHERE pid = " . $item['pid'];
                        $sizeRes = pg_query($conn, $sizeQuery);
                        if (!$sizeRes) {
                            echo "Error in fetching size and price.";
                            continue;
                        }

                        echo "<tr>";
                        echo "<td><img src='" . $item['img'] . "' alt='" . $item['pname'] . "' width='50'></td>";
                        echo "<td>" . $item['pname'] . "</td>";
                        echo "<td>" . $item['description'] . "</td>";
                        echo "<td>";
                        while ($size = pg_fetch_assoc($sizeRes)) {
                            echo ucfirst($size['size']) . ": ₹" . $size['price'] . "<br>";
                        }
                        echo "</td>";
                        echo "<td><a href='?del=" . $item['pid'] . "'><button>Delete</button></a></td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<p>No items available.</p>";
                }
            }

            showMenu();

            if (isset($_GET['del'])) {
                $pid = (int) $_GET['del'];

                $delSizePrice = "DELETE FROM size_price WHERE pid = $pid";
                $delMenu = "DELETE FROM menu WHERE pid = $pid";

                if (pg_query($conn, $delSizePrice) && pg_query($conn, $delMenu)) {
                    echo "<p>Item deleted!</p>";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    echo "<p>Error deleting item.</p>";
                }
            }
            ?>
        </div>

        <div class="button-container">
            <a href="index.html" class="back">Go Back</a>
        </div>

        <footer>
            &copy; 2024 Hotel Aditya. All rights reserved.
        </footer>
    </body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding-top: 50px; 
        padding-bottom: 50px; 
    }

    .main-content {
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid #ccc;
    }

    th, td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    img {
        border-radius: 5px;
    }

    button {
        color: red;
        border: 2px solid black;
        border-radius: 4px;
        padding: 5px 10px;
    }

    button:hover {
        background-color: red;
        color: white;
    }

    header {
        background-color: black;
        text-align: center;
        font-size: 1.5em;
        padding: 10px;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
    }

    footer {
        background-color: black;
        text-align: center;
        font-size: 1em;
        padding: 10px;
        color: white;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
    }
    
    button {
        color: white;
        background-color: red;
        border: 2px solid red;
        border-radius: 4px;
        padding: 5px 10px;
        cursor: pointer;
        font-weight: bold;
    }

    button:hover {
        background-color: green;
    }

    a.back{
        background-color: #008CBA;
        color: white;
        padding: 14px;
        margin-right:34px;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
    }

    a.back:hover {
        background-color: #005f75;
    }

    .button-container {
        text-align: right;
        margin-top: 20px;
    }
</style>
