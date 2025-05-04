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
    <title>Menu Management | Hotel Aditya</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d4af37;
            --secondary-color: #1a1a1a;
            --accent-color: #8b0000;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        
        .header {
            background: var(--secondary-color);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .header-title {
            font-weight: 600;
            margin: 0;
        }
        
        .header-title .gold {
            color: var(--primary-color);
        }
        
        .item-count {
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            margin-top: 10px;
            display: inline-block;
        }
        
        .item-count .veg-count {
            color: #28a745;
        }
        
        .item-count .nonveg-count {
            color: #dc3545;
        }
        
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 50px;
        }
        
        .form-section {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .section-title {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        
        .form-label {
            font-weight: 500;
        }
        
        .form-control, .form-select {
            padding: 10px 15px;
            border-radius: 6px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        
        .btn-submit {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            border-radius: 6px;
        }
        
        .btn-submit:hover {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-reset {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            border-radius: 6px;
        }
        
        .image-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px dashed #ced4da;
            display: none;
            margin-top: 10px;
        }
        
        .footer {
            background: var(--secondary-color);
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container text-center">
            <h1 class="header-title"><span class="gold">Hotel Aditya</span> - Menu Management</h1>
            <div class="item-count">
                <span class="veg-count"><i class="fas fa-leaf me-1"></i> <?php echo $vc; ?> Veg</span>
                <span class="mx-2">|</span>
                <span class="nonveg-count"><i class="fas fa-drumstick-bite me-1"></i> <?php echo $nvc; ?> Non-Veg</span>
            </div>
        </div>
    </header>

    <!-- Main Form -->
    <div class="container">
        <div class="form-container">
            <form id="menuForm" enctype="multipart/form-data">
                <!-- General Information -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-info-circle me-2"></i>Item Details</h3>
                    
                    <div class="mb-3">
                        <label for="pname" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="pname" name="pname" placeholder="Enter item name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cid" class="form-label">Category</label>
                            <select class="form-select" id="cid" name="cid" required>
                                <option value="" selected disabled>Select category</option>
                                <option value="1">Vegetarian</option>
                                <option value="2">Non-Vegetarian</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Enter description" required>
                        </div>
                    </div>
                </div>
                
                <!-- Pricing Information -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-tags me-2"></i>Pricing</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullPrice" class="form-label">Full Size Price (₹)</label>
                            <input type="number" class="form-control" id="fullPrice" name="fullPrice" placeholder="Enter price" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fullQuantity" class="form-label">Full Size Quantity</label>
                            <input type="number" class="form-control" id="fullQuantity" name="fullQuantity" placeholder="Enter quantity" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="halfPrice" class="form-label">Half Size Price (₹)</label>
                            <input type="number" class="form-control" id="halfPrice" name="halfPrice" placeholder="Enter price" required>
                        </div>
                        <div class="col-md-6">
                            <label for="halfQuantity" class="form-label">Half Size Quantity</label>
                            <input type="number" class="form-control" id="halfQuantity" name="halfQuantity" placeholder="Enter quantity" required>
                        </div>
                    </div>
                </div>
                
                <!-- Image Upload -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-camera me-2"></i>Item Image</h3>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <small class="text-muted">Recommended: 800x600px JPG/PNG</small>
                    </div>
                    <img id="imagePreview" src="#" alt="Preview" class="image-preview">
                </div>
                
                <!-- Form Buttons -->
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-plus-circle me-2"></i>Add Item
                    </button>
                    <button type="reset" class="btn btn-reset">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">© <?php echo date('Y'); ?> Hotel Aditya. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(event) {
            const output = document.getElementById('imagePreview');
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    output.src = e.target.result;
                    output.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        });

        // Form submission with SweetAlert
        document.getElementById('menuForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            submitBtn.disabled = true;
            
            fetch('insert.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#d4af37',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reset form after success
                        document.getElementById('menuForm').reset();
                        document.getElementById('imagePreview').style.display = 'none';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#d4af37'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while adding the item',
                    icon: 'error',
                    confirmButtonColor: '#d4af37'
                });
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>