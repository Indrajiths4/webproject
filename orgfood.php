<?php

$conn = mysqli_connect("localhost", "root", "", "webproject");

// Handle Add to Cart
if(isset($_POST['foodid'])) {
    // Get current user's details from session
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];

    // Get userid and usertype
    $user_query = mysqli_query($conn, "SELECT userid, usertype FROM users WHERE username='$username' AND userpassword='$password'");
    $user_data = mysqli_fetch_assoc($user_query);

    $userid = $user_data['userid'];
    $usertype = $user_data['usertype'];
    $foodid = $_POST['foodid'];
    
    // Get food information
    $food_query = mysqli_query($conn, "SELECT foodid, foodname, quantity, userid as donorid FROM food WHERE foodid='$foodid'");
    $food_data = mysqli_fetch_assoc($food_query);
    
    // Check if already in cart
    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE userid='$userid' AND foodid='$foodid'");
    
    if (mysqli_num_rows($check_cart) == 0) {
        // Add to cart
        $insert = mysqli_query($conn, "INSERT INTO cart (userid, usertype, foodid, foodname, quantity, donorid) 
                                     VALUES ('$userid', '$usertype', '{$food_data['foodid']}', 
                                             '{$food_data['foodname']}', '{$food_data['quantity']}', 
                                             '{$food_data['donorid']}')");
        
        if ($insert) {
            echo "<script>alert('Added to cart successfully!');</script>";
        } else {
            echo "<script>alert('Error adding to cart');</script>";
        }
    } else {
        echo "<script>alert('Item already in cart');</script>";
    }
}
?>

<div class="container">
    <div class="items-grid">
        <?php
        $conn = mysqli_connect("localhost", "root", "", "webproject");

        // Fetch all food items where expirydate is not past
        $result = mysqli_query($conn, "SELECT * FROM food WHERE expirydate < CURDATE() ORDER BY foodid DESC");

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="item-card">
                    <h3 class="item-name"><?php echo $row['foodname']; ?></h3>
                    <div class="item-details">
                        <p>Quantity: <?php echo $row['quantity']; ?></p>
                        <p>Expiry Date: <?php echo $row['expirydate']; ?></p>
                    </div>
                    <div class="item-footer">
                        <!-- <span>Food ID: <?php echo $row['foodid']; ?></span> -->
                    </div>
                    <form method="POST">
                        <input type="hidden" name="foodid" value="<?php echo $row['foodid']; ?>">
                        <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo '<div class="no-items">No non-expired food items found.</div>';
        }

        mysqli_close($conn);
        ?>
    </div>
</div>

<!-- CSS Styling -->
<style>
    .add-to-cart-btn {
        padding: 10px 20px;
        background-color: #28a745;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
    }
    .add-to-cart-btn:hover {
        background-color: #218838;
    }
</style>
