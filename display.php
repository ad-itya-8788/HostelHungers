<html>
<head>
   <title>Menu Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .hd {
            color: white;
            text-align: center;
            padding: 30px;
            background-color:black;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
        }

        

        .search {
            position: fixed;
            top: 20px;
            padding:4px;
            background-color:black;
            border:1px solid black;
            right: 10px;
            z-index: 1000;
        }

        .search input {
            padding: 5px;
        }

        .search button {
            padding: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            width: 60px;
            height: 60px;
            border-radius: 5px;
        }

        hr {
            margin: 5px 0;
            border-top: 1px solid black;
        }

        .ft {
            color: white;
            background-color:black;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .ft p {
            margin: 0;
        }

        #res {
            display: block;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .hd {
                padding: 20px;
                font-size: 1.5em;
            }

            .search {
                position: relative;
                top: 0;
                right: 0;
                margin-top: 10px;
                text-align: center;
                width: 100%;
            }

            .search input,
            .search button {
                width: 100%;
                
                margin: 5px 0;
                padding: 12px;
            }
        }

        .hd b {
            font-size: 2em;
            letter-spacing: 4px;
            text-shadow: 2px 2px 10px green;
            text-transform: uppercase;
        }

    </style>
</head>

<body>

    <div class="hd"><b>
        Welcome to Hotel Aditya
    </b> </div>

    <div class="search">
        <form id="frm">
            <input type="text" oninput="fch()" id="qry" placeholder="Search Menu..." />
            <button type="submit">Search</button>
        </form>
    </div>

    <div id="res"> </div>

   

    <script>
        window.onload = function () {
            fch(); 
        }

        document.getElementById("frm").addEventListener("submit", function (e) {
            e.preventDefault(); 
            fch(); 
        });

        function fch() {
            var q = document.getElementById("qry").value; 
            var resDiv = document.getElementById("res");

            var xhr = new XMLHttpRequest(); 
            xhr.open("POST", "fetch_menu.php", true); 
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    resDiv.innerHTML = xhr.responseText; 
                }
            };

            if (q === "") {
                xhr.send(); 
            } else {
                xhr.send("searchQuery=" + encodeURIComponent(q)); 
            }
        }
    </script>
     <div class="ft">
        <p>&copy; 2024 Aditya Hotel. All Rights Reserved.</p>
    </div>
</body>

</html>
