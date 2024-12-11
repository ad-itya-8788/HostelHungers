<?php 
include 'dbconnect.php';

$nonvegQuery = "SELECT COUNT(*) FROM menu WHERE cid = 2";
$vegQuery = "SELECT COUNT(*) FROM menu WHERE cid = 1";

$nvcResult = pg_query($conn, $nonvegQuery);
$vcResult = pg_query($conn, $vegQuery);

$nvc = pg_fetch_result($nvcResult, 0, 0);
$vc = pg_fetch_result($vcResult, 0, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu and Price Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }

        header {
            background-color: black;
            color: white;
            text-transform: uppercase;
            padding: 5px;
            text-align: center;

        }

        .formContainer {
            background-color: #fff;
            box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 850px;
            margin: 40px auto;
        }

        fieldset {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        legend {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .formRow {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .formRow div {
            width: 30%;
            font-size: 1rem;
            text-align: right;
        }

        .formField {
            width: 70%;
            padding: 7px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .buttonContainer {
            text-align: center;
            margin-top: 20px;
        }

        input[type="submit"], input[type="reset"] {
            background-color: #0b0b0b;
            color: white;
            padding: 15px;
            width: 210px;
            border-radius: 6px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 0 10px;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        input[type="reset"]:hover {
            background-color: #c93241;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        #itemCount {
            font-size:1.2em;
            padding:3px;
            border-radius:2px;
            border:3px solid cyan;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <h1>Menu Card Items Add 😋</h1>
    <div id="itemCount">

        Total Veg Items: <?php echo $vc; ?>
        🫛 | Total Non-Veg Items: <?php echo $nvc; ?>
   🐓 </div>
</header>

<div class="formContainer">
    <form id="menuForm" enctype="multipart/form-data">
        <fieldset>
            <legend>General Information</legend>
            <div class="formRow">
                <div>Item Name:</div>
                <input type="text" id="pname" name="pname" class="formField" placeholder="Item Name" required>
            </div>

            <div class="formRow">
                <div>Category:</div>
                <select id="cid" name="cid" class="formField" required>
                    <option value="1">Veg</option>
                    <option value="2">Non-Veg</option>
                </select>
            </div>

            <div class="formRow">
                <div>Description:</div>
                <input type="text" id="description" name="description" class="formField" placeholder="Description" required>
            </div>
        </fieldset>

        <fieldset>
            <legend>Pricing</legend>
            <div class="formRow">
                <div>Full Size Price:</div>
                <input type="number" id="fullPrice" name="fullPrice" class="formField" placeholder="Full Size Price" required>
            </div>

            <div class="formRow">
                <div>Full Size Quantity:</div>
                <input type="text" id="fullQuantity" name="fullQuantity" class="formField" placeholder="Full Size Quantity" required>
            </div>

            <div class="formRow">
                <div>Half Size Price:</div>
                <input type="number" id="halfPrice" name="halfPrice" class="formField" placeholder="Half Size Price" required>
            </div>

            <div class="formRow">
                <div>Half Size Quantity:</div>
                <input type="text" id="halfQuantity" name="halfQuantity" class="formField" placeholder="Half Size Quantity" required>
            </div>
        </fieldset>

        <fieldset>
            <legend>Image Upload</legend>
            <div class="formRow">
                <div>Image:</div>
                <input type="file" id="image" name="image" class="formField" accept="image/*" required>
            </div>
        </fieldset>

        <div class="buttonContainer">
            <input type="submit" value="Add Menu Item">
            <input type="reset" value="Clear">
        </div>
    </form>

    <div id="result"></div>
</div>

<footer>
    © 2024 Hotel Aditya All rights reserved
</footer>

<script>
    document.getElementById('menuForm').onsubmit = function(event) {
        event.preventDefault();
        const xhr = new XMLHttpRequest();
        const formData = new FormData(this);

        xhr.open('POST', 'insert.php', true);
        xhr.onload = function() {
            const replay = xhr.status === 200 ? xhr.responseText : 'Error: ' + xhr.statusText;
          alert(replay);
        };

        xhr.send(formData);
    };
</script>

</body>
</html>
