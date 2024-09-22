<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
};

if (isset($_POST['add_to_cart'])) {
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];
   $journey_date = $_POST['journey_date'];  // Capture journey date

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if (mysqli_num_rows($check_cart_numbers) > 0) {
      $message[] = 'already added to cart!';
   } else {
      mysqli_query($conn, "INSERT INTO cart(user_id, name, price, quantity, image, journey_date) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image', '$journey_date')") or die('query failed');
      $message[] = 'product added to cart!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search Page with Filters</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>Search Page</h3>
      <p><a href="home.php">home</a> / search</p>
   </div>

   <!-- Filter section -->
   <section class="search-form">
      <form action="" method="post">
         <!-- Search Keyword -->
         <input type="text" name="search" placeholder="Search products..." class="box">

         <!-- Price Range Filter -->
         <div>
            <label for="min_price" style="font-size: 1.5rem;">Min Price:</label>
            <input type="number" name="min_price" placeholder="0" min="0" value="<?php echo isset($_POST['min_price']) ? $_POST['min_price'] : 0; ?>" style="border: 1px solid #ccc; padding: 5px; font-size: 14px; border-radius: 5px; width: 150px;">
         </div>
         <div>
            <label for="max_price" style="font-size: 1.5rem;">Max Price:</label>
            <input type="number" name="max_price" placeholder="1000" min="0" value="<?php echo isset($_POST['max_price']) ? $_POST['max_price'] : 1000; ?>" style="border: 1px solid #ccc; padding: 5px; font-size: 14px; border-radius: 5px; width: 150px;">
         </div>

         <!-- Boat Type Filter -->
         <div>
            <label for="boat_type" style="font-size: 1.5rem;">Boat Type:</label>
            <select name="boat_type" style="border: 1px solid #ccc; padding: 5px; font-size: 14px; border-radius: 5px; width: 160px;">
               <option value="">Select Boat Type</option>
               <option value="Cruise Ship" <?php echo (isset($_POST['boat_type']) && $_POST['boat_type'] === 'Cruise Ship') ? 'selected' : ''; ?>>Cruise Ship</option>
               <option value="Launch" <?php echo (isset($_POST['boat_type']) && $_POST['boat_type'] === 'Launch') ? 'selected' : ''; ?>>Launch</option>
               <option value="House Boat" <?php echo (isset($_POST['boat_type']) && $_POST['boat_type'] === 'House Boat') ? 'selected' : ''; ?>>House Boat</option>
            </select>
         </div>

         <!-- Submit Button -->
         <input type="submit" name="submit" value="Search" class="btn">
      </form>
   </section>

   <section class="products" style="padding-top: 0;">
      <div class="box-container">
         <?php
         if (isset($_POST['submit'])) {
            $search_item = mysqli_real_escape_string($conn, $_POST['search']);
            $min_price = isset($_POST['min_price']) ? (float) $_POST['min_price'] : 0;
            $max_price = isset($_POST['max_price']) ? (float) $_POST['max_price'] : 1000;
            $boat_type = isset($_POST['boat_type']) ? mysqli_real_escape_string($conn, $_POST['boat_type']) : '';

            // Build the SQL query based on filters
            $query = "SELECT * FROM products WHERE name LIKE '%$search_item%' AND price BETWEEN $min_price AND $max_price";
            if ($boat_type !== '') {
               $query .= " AND boat_type = '$boat_type'";
            }

            $select_products = mysqli_query($conn, $query) or die('query failed');

            if (mysqli_num_rows($select_products) > 0) {
               while ($fetch_product = mysqli_fetch_assoc($select_products)) {
         ?>
                  <form action="" method="post" class="box">
                     <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="image">
                     <div class="name"><?php echo $fetch_product['boat_type'] . " - " . $fetch_product['name']; ?></div> <!-- Display Boat Type and Name -->
                     <div class="price">$<?php echo $fetch_product['price']; ?>/-</div>

                     <!-- Display Journey Date -->
                     <label for="journey_date" style="font-size: 1.5rem;">Journey Date:</label>
                     <input type="date" name="journey_date" required style="border: 1px solid #ccc; padding: 5px; font-size: 14px; border-radius: 5px; width: 150px;">
                     <input type="number" class="qty" name="product_quantity" min="1" value="1">
                     <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                     <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                     <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                     <input type="submit" class="btn" value="Add to Cart" name="add_to_cart">
                  </form>

         <?php
               }
            } else {
               echo '<p class="empty">No result found within this price range!</p>';
            }
         } else {
            echo '<p class="empty">Search something!</p>';
         }
         ?>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <!-- Custom JS file link -->
   <script src="js/script.js"></script>

</body>

</html>
