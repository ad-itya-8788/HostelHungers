<?php
// Database connection
include 'dbconnect.php';

// Function to fetch and display menu items
function displayMenuItems($conn, $searchQuery = "") 
{
    if (!empty($searchQuery)) {
        $sql = "SELECT m.mid, m.pname, m.description, m.img, sp.size, sp.price, sp.quantity 
                FROM menu m
                LEFT JOIN size_price sp ON m.mid = sp.mid
                WHERE m.pname ILIKE '%" . pg_escape_string($conn, $searchQuery) . "%'
                ORDER BY m.pname";
    } else {
        $sql = "SELECT m.mid, m.pname, m.description, m.img, sp.size, sp.price, sp.quantity 
                FROM menu m
                LEFT JOIN size_price sp ON m.mid = sp.mid
                ORDER BY m.pname";
    }

    $result = pg_query($conn, $sql);

    if (!$result) {
        return "<div class='no-results'>Error fetching menu data.</div>";
    }

    if (pg_num_rows($result) == 0) {
        return "<div class='no-results'>No menu items found matching your search.</div>";
    }

    $menuItems = [];
    while ($row = pg_fetch_assoc($result)) {
        $mid = $row['mid'];
        if (!isset($menuItems[$mid])) {
            $menuItems[$mid] = [
                'mid' => $mid,
                'pname' => $row['pname'],
                'description' => $row['description'],
                'img' => $row['img'],
                'sizes' => []
            ];
        }
        
        if (!empty($row['size'])) {
            $menuItems[$mid]['sizes'][$row['size']] = [
                'price' => $row['price'],
                'quantity' => $row['quantity']
            ];
        }
    }

    $output = '';
    foreach ($menuItems as $item) {
        $fullPrice = isset($item['sizes']['Full']) ? '₹' . $item['sizes']['Full']['price'] : 'N/A';
        $fullQuantity = isset($item['sizes']['Full']) ? $item['sizes']['Full']['quantity'] : 'N/A';
        $halfPrice = isset($item['sizes']['Half']) ? '₹' . $item['sizes']['Half']['price'] : 'N/A';
        $halfQuantity = isset($item['sizes']['Half']) ? $item['sizes']['Half']['quantity'] : 'N/A';

        $output .= '
        <div class="menu-item" data-item-name="' . htmlspecialchars($item['pname']) . '" data-full-price="' . $fullPrice . '" data-half-price="' . $halfPrice . '">
            <div class="menu-card">
                <div class="menu-image">
                    <img src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['pname']) . '">
                    <div class="menu-rating">
                        <span>4.2 ★</span>
                    </div>
                </div>
                <div class="menu-details">
                    <h3>' . htmlspecialchars($item['pname']) . '</h3>
                    <p class="menu-desc">' . htmlspecialchars($item['description']) . '</p>
                    <div class="menu-pricing">
                        <div class="size-option">
                            <div class="size-label">Full</div>
                            <div class="price-tag">' . $fullPrice . '</div>
                            <div class="quantity-tag">Qty: ' . $fullQuantity . '</div>
                        </div>
                        <div class="size-option">
                            <div class="size-label">Half</div>
                            <div class="price-tag">' . $halfPrice . '</div>
                            <div class="quantity-tag">Qty: ' . $halfQuantity . '</div>
                        </div>
                    </div>
                    <div class="menu-actions">
                        <button class="order-btn" onclick="openOrderForm(this)">
                            <i class="fab fa-whatsapp"></i> Order Now
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    }

    return '<div class="menu-grid">' . $output . '</div>';
}

// Get search query if any
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Aditya - Menu</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e23744;
            --primary-dark: #c31c2b;
            --secondary: #1d1d1d;
            --text-dark: #1e272e;
            --text-light: #636e72;
            --background: #f9f9f9;
            --card-bg: #ffffff;
            --border-color: #e8e8e8;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            --whatsapp: #25D366;
            --whatsapp-dark: #128C7E;
            --header-bg: rgb(13, 17, 17);
            --header-border: green;
            --animation-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--background);
    color: var(--text-dark);
    line-height: 1.6;
    background: url("photos/hostelhunger.jpg") no-repeat center center fixed;
background-size: 50% 40%;  
}

        .header {
            background-color: var(--header-bg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            border-bottom: 2px solid var(--header-border);
            z-index: 1000;
            padding: 10px 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            flex-wrap: nowrap; /* Prevent wrapping on small screens */
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            white-space: nowrap; /* Prevent logo text from wrapping */
            transition: transform var(--animation-speed) ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo i {
            margin-right: 10px;
            font-size: 28px;
            color: var(--primary);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .search-container {
            position: relative;
            width: 300px;
            max-width: 50%; /* Limit width on small screens */
            margin-left: 20px; /* Add space between logo and search */
        }

        .search-box {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
            outline: none;
            transition: all var(--animation-speed) ease;
            background-color: rgba(255, 255, 255, 0.9);
        }

        .search-box:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(226, 55, 68, 0.2);
            background-color: white;
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary);
            font-size: 16px;
            cursor: pointer;
            padding: 5px 10px;
            transition: transform var(--animation-speed) ease;
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.1);
        }

        /* Hero Section with Animation */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8cmVzdGF1cmFudHxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(226, 55, 68, 0.3), transparent);
            animation: gradientMove 10s infinite alternate;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        .hero-content {
            max-width: 800px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-subtitle {
            font-size: 16px;
            font-weight: 300;
            margin-bottom: 25px;
            opacity: 0;
            animation: fadeIn 1s ease-out 0.5s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Menu Section Styles with Animations */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section-title {
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: var(--text-dark);
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary);
            animation: expandWidth 1s ease-out;
        }

        @keyframes expandWidth {
            from { width: 0; }
            to { width: 80px; }
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .menu-item {
            transition: transform var(--animation-speed) ease, opacity var(--animation-speed) ease;
            opacity: 0;
            animation: fadeInStagger 0.5s ease forwards;
            animation-delay: calc(var(--item-index, 0) * 0.1s);
        }

        @keyframes fadeInStagger {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: box-shadow var(--animation-speed) ease, transform var(--animation-speed) ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .menu-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
        }

        .menu-image {
            height: 180px;
            position: relative;
            overflow: hidden;
        }

        .menu-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .menu-card:hover .menu-image img {
            transform: scale(1.1);
        }

        .menu-rating {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: var(--primary);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: transform var(--animation-speed) ease;
        }

        .menu-card:hover .menu-rating {
            transform: scale(1.1);
        }

        .menu-details {
            padding: 15px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .menu-details h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-dark);
            transition: color var(--animation-speed) ease;
        }

        .menu-card:hover .menu-details h3 {
            color: var(--primary);
        }

        .menu-desc {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 42px;
        }

        .menu-pricing {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 8px 0;
            border-top: 1px dashed var(--border-color);
            border-bottom: 1px dashed var(--border-color);
        }

        .size-option {
            text-align: center;
            flex: 1;
        }

        .size-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .price-tag {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
            transition: transform var(--animation-speed) ease;
        }

        .menu-card:hover .price-tag {
            transform: scale(1.1);
        }

        .quantity-tag {
            font-size: 12px;
            color: var(--text-light);
        }

        .menu-actions {
            display: flex;
            justify-content: center;
            margin-top: auto;
        }

        .order-btn {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all var(--animation-speed) ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .order-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .order-btn:hover::before {
            left: 100%;
        }

        .order-btn i {
            margin-right: 8px;
            font-size: 16px;
        }

        .order-btn:hover {
            background-color: var(--whatsapp-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .no-results {
            text-align: center;
            padding: 30px;
            font-size: 16px;
            color: var(--text-light);
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            grid-column: 1 / -1;
            animation: fadeIn 0.5s ease;
        }

        /* Footer with Animation */
        .footer {
            background-color: var(--header-bg);
;
            color: white;
            padding: 20px 0;
            text-align: center;
            font-size: 14px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .footer-info p {
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform var(--animation-speed) ease;
        }

        .footer-info p:hover {
            transform: translateY(-2px);
        }

        .footer-info i {
            color: var(--primary);
        }
        
        /* Order Form Modal with Enhanced Animation */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .modal.show {
            opacity: 1;
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 25px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transform: translateY(-50px);
            opacity: 0;
            transition: all 0.4s ease;
        }
        
        .modal.show .modal-content {
            transform: translateY(0);
            opacity: 1;
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-size: 22px;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-title i {
            color: var(--whatsapp);
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s, transform 0.2s;
        }
        
        .close:hover {
            color: var(--primary);
            transform: rotate(90deg);
        }
        
        .modal-body {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }
        
        .modal.show .form-group {
            opacity: 1;
            transform: translateX(0);
        }
        
        .modal.show .form-group:nth-child(1) { transition-delay: 0.1s; }
        .modal.show .form-group:nth-child(2) { transition-delay: 0.2s; }
        .modal.show .form-group:nth-child(3) { transition-delay: 0.3s; }
        .modal.show .form-group:nth-child(4) { transition-delay: 0.4s; }
        .modal.show .form-group:nth-child(5) { transition-delay: 0.5s; }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(226, 55, 68, 0.1);
            outline: none;
        }
        
        .size-selector {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .size-radio {
            display: none;
        }
        
        .size-label {
            display: inline-block;
            padding: 8px 20px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            flex: 1;
            position: relative;
            overflow: hidden;
        }
        
        .size-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .size-label:hover::before {
            left: 100%;
        }
        
        .size-radio:checked + .size-label {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: scale(1.05);
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            max-width: 120px;
            margin-top: 10px;
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s;
        }
        
        .quantity-btn:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .quantity-input {
            width: 50px;
            height: 32px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 5px;
        }
        
        .order-summary {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            margin-bottom: 20px;
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.4s ease;
            transition-delay: 0.6s;
        }
        
        .modal.show .order-summary {
            transform: scale(1);
            opacity: 1;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .summary-total {
            font-weight: 600;
            border-top: 1px dashed #ddd;
            padding-top: 8px;
            margin-top: 8px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            transition-delay: 0.7s;
        }
        
        .modal.show .modal-footer {
            opacity: 1;
            transform: translateY(0);
        }
        
        .btn {
            padding: 10px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-cancel {
            background-color: #f5f5f5;
            color: var(--text-dark);
        }
        
        .btn-cancel:hover {
            background-color: #e9e9e9;
            transform: translateY(-2px);
        }
        
        .btn-confirm {
            background-color: var(--whatsapp);
            color: white;
            flex: 1;
        }
        
        .btn-confirm:hover {
            background-color: var(--whatsapp-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-confirm i {
            margin-right: 8px;
        }

        /* Responsive Styles - FIXED FOR HORIZONTAL HEADER */
        @media (max-width: 992px) {
            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
            
            .modal-content {
                margin: 15% auto;
            }
        }
        
        @media (max-width: 768px) {
            /* Keep header horizontal on all devices */
            .nav-container {
                justify-content: space-between;
                padding: 0 15px;
            }
            
            .logo {
                font-size: 20px; /* Slightly smaller font on mobile */
            }
            
            .logo i {
                font-size: 24px;
            }
            
            .search-container {
                width: 200px;
                margin-left: 15px;
            }
            
            .search-box {
                padding: 8px 35px 8px 12px;
                font-size: 13px;
            }
            
            .hero {
                height: 250px;
            }

            .hero-title {
                font-size: 28px;
            }
            
            .modal-content {
                width: 95%;
                margin: 20% auto;
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            /* Further adjustments for very small screens */
            .logo {
                font-size: 18px;
            }
            
            .logo i {
                font-size: 22px;
                margin-right: 5px;
            }
            
            .search-container {
                width: 150px;
                margin-left: 10px;
            }
            
            .search-box {
                padding: 7px 30px 7px 10px;
                font-size: 12px;
            }
            
            .search-btn {
                font-size: 14px;
            }
            
            .hero {
                height: 220px;
            }

            .hero-title {
                font-size: 24px;
            }

            .section-title {
                font-size: 22px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                margin: 25% auto;
                padding: 15px;
            }
            
            .modal-title {
                font-size: 18px;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
        
        /* Accessibility focus styles */
        button:focus, a:focus, input:focus, select:focus, textarea:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
        
        /* Error message styles */
        .error-message {
            color: var(--primary);
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        .form-control.error {
            border-color: var(--primary);
        }
        
        .form-control.error + .error-message {
            display: block;
        }
        
        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Header with Logo and Search -->
    <header class="header">
        <div class="nav-container">
            <a href="login.html" class="logo">
                <i class="fas fa-utensils"></i>
                Hostel Hungers
            </a>
            <div class="search-container">
                <input type="text" id="searchInput" class="search-box" placeholder="Search for dishes..." oninput="liveSearch()" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="search-btn" onclick="liveSearch()" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Delicious Food For Every Mood</h1>
            <p class="hero-subtitle">Experience the perfect blend of traditional flavors at Hostel Hungers</p>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="container">
        <h2 class="section-title">Our Menu</h2>
        
        <div id="menuResults">
            <?php echo displayMenuItems($conn, $searchQuery); ?>
        </div>
    </section>

    <!-- Order Form Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fab fa-whatsapp"></i> Place Your Order</h3>
                <span class="close" aria-label="Close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="orderForm">
                    <div class="form-group">
                        <label for="customerName">Your Name</label>
                        <input type="text" id="customerName" class="form-control" required>
                        <div class="error-message">Please enter your name</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="customerPhone">Your Mobile Number</label>
                        <input type="tel" id="customerPhone" class="form-control" required pattern="[0-9]{10}">
                        <div class="error-message">Please enter a valid 10-digit mobile number</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="customerAddress">Delivery Address</label>
                        <textarea id="customerAddress" class="form-control" rows="2" required></textarea>
                        <div class="error-message">Please enter your delivery address</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Item Size</label>
                        <div class="size-selector">
                            <input type="radio" id="sizeFull" name="itemSize" value="Full" class="size-radio" checked>
                            <label for="sizeFull" class="size-label">Full</label>
                            
                            <input type="radio" id="sizeHalf" name="itemSize" value="Half" class="size-radio">
                            <label for="sizeHalf" class="size-label">Half</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="itemQuantity">Quantity</label>
                        <div class="quantity-control">
                            <button type="button" class="quantity-btn" onclick="decrementQuantity()">-</button>
                            <input type="number" id="itemQuantity" class="quantity-input" value="1" min="1" max="10" required>
                            <button type="button" class="quantity-btn" onclick="incrementQuantity()">+</button>
                        </div>
                    </div>
                    
                    <div class="order-summary">
                        <h4>Order Summary</h4>
                        <div class="summary-item">
                            <span>Item:</span>
                            <span id="summaryItemName">-</span>
                        </div>
                        <div class="summary-item">
                            <span>Size:</span>
                            <span id="summaryItemSize">Full</span>
                        </div>
                        <div class="summary-item">
                            <span>Price:</span>
                            <span id="summaryItemPrice">-</span>
                        </div>
                        <div class="summary-item">
                            <span>Quantity:</span>
                            <span id="summaryItemQuantity">1</span>
                        </div>
                        <div class="summary-item summary-total">
                            <span>Total:</span>
                            <span id="summaryTotal">-</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cancelOrder" class="btn btn-cancel">Cancel</button>
                <button id="confirmOrder" class="btn btn-confirm"><i class="fab fa-whatsapp"></i> Place Order</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Hostel Hungers. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- JavaScript for Live Search and WhatsApp Integration -->
    <script>
        // Current item data
        let currentItem = {
            name: '',
            fullPrice: '',
            halfPrice: '',
            selectedSize: 'Full',
            quantity: 1,
            unitPrice: 0,
            totalPrice: 0
        };
        
        // Apply animation to menu items
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach((item, index) => {
                item.style.setProperty('--item-index', index);
            });
        });
        
        // Live search functionality
        function liveSearch() {
            const searchQuery = document.getElementById('searchInput').value;
            
            // Create an XMLHttpRequest object
            const xhr = new XMLHttpRequest();
            
            // Configure it: GET-request for the URL
            xhr.open('GET', '?search=' + encodeURIComponent(searchQuery), true);
            
            // Send the request
            xhr.send();
            
            // This will be called after the response is received
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Extract the menu results from the response
                    const parser = new DOMParser();
                    const htmlDoc = parser.parseFromString(xhr.responseText, 'text/html');
                    const menuResults = htmlDoc.getElementById('menuResults').innerHTML;
                    
                    // Update the menu results
                    document.getElementById('menuResults').innerHTML = menuResults;
                    
                    // Reapply animation to new menu items
                    const menuItems = document.querySelectorAll('.menu-item');
                    menuItems.forEach((item, index) => {
                        item.style.setProperty('--item-index', index);
                    });
                }
            };
        }
        
        // Modal elements
        const modal = document.getElementById('orderModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        const cancelBtn = document.getElementById('cancelOrder');
        const confirmBtn = document.getElementById('confirmOrder');
        
        // Form elements
        const customerName = document.getElementById('customerName');
        const customerPhone = document.getElementById('customerPhone');
        const customerAddress = document.getElementById('customerAddress');
        const sizeFullRadio = document.getElementById('sizeFull');
        const sizeHalfRadio = document.getElementById('sizeHalf');
        const itemQuantity = document.getElementById('itemQuantity');
        
        // Summary elements
        const summaryItemName = document.getElementById('summaryItemName');
        const summaryItemSize = document.getElementById('summaryItemSize');
        const summaryItemPrice = document.getElementById('summaryItemPrice');
        const summaryItemQuantity = document.getElementById('summaryItemQuantity');
        const summaryTotal = document.getElementById('summaryTotal');
        
        // Function to open the order form with animation
        function openOrderForm(button) {
            // Get the menu item data
            const menuItem = button.closest('.menu-item');
            currentItem.name = menuItem.getAttribute('data-item-name');
            currentItem.fullPrice = menuItem.getAttribute('data-full-price');
            currentItem.halfPrice = menuItem.getAttribute('data-half-price');
            
            // Set the default selected size
            currentItem.selectedSize = 'Full';
            sizeFullRadio.checked = true;
            
            // Set the default quantity
            currentItem.quantity = 1;
            itemQuantity.value = 1;
            
            // Update the summary
            updateOrderSummary();
            
            // Show the modal with animation
            modal.style.display = 'block';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
        
        // Function to update the order summary
        function updateOrderSummary() {
            summaryItemName.textContent = currentItem.name;
            summaryItemSize.textContent = currentItem.selectedSize;
            
            // Set the unit price based on the selected size
            if (currentItem.selectedSize === 'Full') {
                summaryItemPrice.textContent = currentItem.fullPrice;
                currentItem.unitPrice = parseFloat(currentItem.fullPrice.replace('₹', '').replace('N/A', '0'));
            } else {
                summaryItemPrice.textContent = currentItem.halfPrice;
                currentItem.unitPrice = parseFloat(currentItem.halfPrice.replace('₹', '').replace('N/A', '0'));
            }
            
            summaryItemQuantity.textContent = currentItem.quantity;
            
            // Calculate the total price
            currentItem.totalPrice = currentItem.unitPrice * currentItem.quantity;
            summaryTotal.textContent = '₹' + currentItem.totalPrice.toFixed(2);
        }
        
        // Function to increment quantity
        function incrementQuantity() {
            if (currentItem.quantity < 10) {
                currentItem.quantity++;
                itemQuantity.value = currentItem.quantity;
                updateOrderSummary();
            }
        }
        
        // Function to decrement quantity
        function decrementQuantity() {
            if (currentItem.quantity > 1) {
                currentItem.quantity--;
                itemQuantity.value = currentItem.quantity;
                updateOrderSummary();
            }
        }
        
        // Update summary when quantity input changes
        itemQuantity.addEventListener('change', function() {
            currentItem.quantity = parseInt(this.value) || 1;
            if (currentItem.quantity < 1) currentItem.quantity = 1;
            if (currentItem.quantity > 10) currentItem.quantity = 10;
            this.value = currentItem.quantity;
            updateOrderSummary();
        });
        
        // Update summary when size selection changes
        sizeFullRadio.addEventListener('change', function() {
            if (this.checked) {
                currentItem.selectedSize = 'Full';
                updateOrderSummary();
            }
        });
        
        sizeHalfRadio.addEventListener('change', function() {
            if (this.checked) {
                currentItem.selectedSize = 'Half';
                updateOrderSummary();
            }
        });
        
        // Close the modal with animation
        function closeModal() {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
        
        // Close the modal when clicking the close button
        closeBtn.onclick = closeModal;
        
        // Close the modal when clicking the cancel button
        cancelBtn.onclick = closeModal;
        
        // Validate form and send WhatsApp message
        confirmBtn.onclick = function() {
            // Validate form
            let isValid = true;
            
            if (!customerName.value.trim()) {
                customerName.classList.add('error');
                isValid = false;
            } else {
                customerName.classList.remove('error');
            }
            
            if (!customerPhone.value.trim() || !customerPhone.checkValidity()) {
                customerPhone.classList.add('error');
                isValid = false;
            } else {
                customerPhone.classList.remove('error');
            }
            
            if (!customerAddress.value.trim()) {
                customerAddress.classList.add('error');
                isValid = false;
            } else {
                customerAddress.classList.remove('error');
            }
            
            if (!isValid) {
                return;
            }
            
            // Show loading state
            const originalBtnText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<span class="loading"></span>Sending...';
            confirmBtn.disabled = true;
            
         // Create WhatsApp message
let whatsappMsg = "👋 *Hello!* I would like to place an order 🛍️:\n\n";

whatsappMsg += "📋 *Customer Details:*\n";
whatsappMsg += "👤 Name: " + customerName.value + "\n";
whatsappMsg += "📞 Phone: " + customerPhone.value + "\n";
whatsappMsg += "🏠 Address: " + customerAddress.value + "\n\n";

whatsappMsg += "🍽️ *Order Details:*\n";
whatsappMsg += "🍔 Item: " + currentItem.name + "\n";
whatsappMsg += "📏 Size: " + currentItem.selectedSize + "\n";
whatsappMsg += "💰 Price: " + (currentItem.selectedSize === 'Full' ? currentItem.fullPrice : currentItem.halfPrice) + "\n";
whatsappMsg += "🔢 Quantity: " + currentItem.quantity + "\n";
whatsappMsg += "🧾 Total: ₹" + currentItem.totalPrice.toFixed(2) + "\n\n";

whatsappMsg += "🙏 Thank you so much! Can't wait to enjoy my meal! 😋❤️";
   
            // Encode the WhatsApp message
            const encodedMsg = encodeURIComponent(whatsappMsg);
            const whatsappLink = "https://wa.me/917066201454?text=" + encodedMsg;
            
            // Simulate a short delay for better UX
            setTimeout(() => {
                // Close the modal
                closeModal();
                
                // Reset button state
                confirmBtn.innerHTML = originalBtnText;
                confirmBtn.disabled = false;
                
                // Open WhatsApp in a new tab
                window.open(whatsappLink, '_blank');
            }, 800);
        }
        
        // Close the modal if clicked outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>