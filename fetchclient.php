<?php 
include 'dbconnect.php';

function displayMenuItems($searchQuery = "") 
{
    global $conn;

    // SQL query to fetch the menu items based on the search query
    if (!empty($searchQuery)) {
        $sql = "SELECT m.mid, m.pname, m.description, m.img, sp.size, sp.price, sp.quantity 
                FROM menu m
                LEFT JOIN size_price sp ON m.mid = sp.mid
                WHERE m.pname ILIKE '%" . pg_escape_string($searchQuery) . "%'";
    } else {
        $sql = "SELECT m.mid, m.pname, m.description, m.img, sp.size, sp.price, sp.quantity 
                FROM menu m
                LEFT JOIN size_price sp ON m.mid = sp.mid";
    }

    $result = pg_query($conn, $sql);

    if (!$result) {
        echo "Error fetching data.";
        return;
    }

    // Checking if there are rows to display
    if (pg_num_rows($result) > 0) {
        // Array to hold the menu items by product id to prevent duplicates
        $menuItems = [];
        
        while ($row = pg_fetch_assoc($result)) {
            $mid = $row['mid'];

            if (!isset($menuItems[$mid])) {
                // If the menu item is not already added, initialize the array with item info
                $menuItems[$mid] = [
                    'pname' => $row['pname'],
                    'description' => $row['description'],
                    'img' => $row['img'],
                    'fullSize' => null,
                    'halfSize' => null
                ];
            }

            // Add full or half size data based on the size type
            if ($row['size'] == 'Full') {
                $menuItems[$mid]['fullSize'] = [
                    'price' => '₹' . $row['price'],
                    'quantity' => $row['quantity']
                ];
            } elseif ($row['size'] == 'Half') {
                $menuItems[$mid]['halfSize'] = [
                    'price' => '₹' . $row['price'],
                    'quantity' => $row['quantity']
                ];
            }
        }

        // Now, we can iterate over the menu items and display them
        foreach ($menuItems as $item) {
            echo "<div class='menu-item'>";
            
            // Displaying product image
            if ($item['img']) {
                echo "<img src='" . $item['img'] . "' alt='" . $item['pname'] . "' class='menu-img'>";
            } else {
                echo "<img src='default-image.jpg' alt='Default Image' class='menu-img'>";
            }

            // Displaying product name and description
            echo "<div class='menu-info'>";
            echo "<h3>" . $item['pname'] . "</h3>";
            echo "<p>" . $item['description'] . "</p>";

            // Display Full and Half Size details within the same div
            echo "<div class='size-info'>";
            if ($item['fullSize']) {
                echo "<div class='size'>
                        <span>Full Size:</span> 
                        <span>" . $item['fullSize']['price'] . "</span> 
                        <span>Qty: " . $item['fullSize']['quantity'] . "</span>
                      </div>";
            }

            if ($item['halfSize']) {
                echo "<div class='size'>
                        <span>Half Size:</span> 
                        <span>" . $item['halfSize']['price'] . "</span> 
                        <span>Qty: " . $item['halfSize']['quantity'] . "</span>
                      </div>";
            }
            echo "</div>"; // End of size-info div

            echo "</div>"; // End of menu-info div
            echo "</div>"; // End of menu-item div
        }
    } else {
        echo "<p class='notfound'>No items found.</p>";
    }
}

// Check if search query exists and call displayMenuItems function
$searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';
displayMenuItems($searchQuery);
?>


