<?php

$conn = mysqli_connect("localhost", "root", "", "webproject");

// Get current user's details from session
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Get userid
$user_query = mysqli_query($conn, "SELECT userid, usertype FROM users WHERE username='$username' AND userpassword='$password'");
$user_data = mysqli_fetch_assoc($user_query);

$userid = $user_data['userid'];
$usertype = $user_data['usertype'];

// Handle Buy All
if(isset($_POST['buyall'])) {
    // Get all cart items for this user
    $cart_items = mysqli_query($conn, "SELECT * FROM cart WHERE userid='$userid' AND usertype='$usertype'");
    
    while($item = mysqli_fetch_assoc($cart_items)) {
        // Insert each item into orders table
        $insert = mysqli_query($conn, "INSERT INTO orders (userid, usertype, foodid, foodname, quantity, donorid) 
                                     VALUES ('$userid', '$usertype', '{$item['foodid']}', 
                                             '{$item['foodname']}', '{$item['quantity']}', 
                                             '{$item['donorid']}')");
        
        if($insert) {
            // Delete from food table
            mysqli_query($conn, "DELETE FROM food WHERE foodid='{$item['foodid']}'");
        }
    }
    
    // Clear all items from cart for this user
    mysqli_query($conn, "DELETE FROM cart WHERE userid='$userid' AND usertype='$usertype'");
    echo "<script>alert('All items purchased successfully!');</script>";
}

// Handle Delete
if(isset($_POST['delete'])) {
    $cartid = $_POST['cartid'];
    mysqli_query($conn, "DELETE FROM cart WHERE cartid='$cartid'");
    echo "<script>alert('Item removed from cart!');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>NGO Cart</title>
    <style>
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .cart-table th, .cart-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .cart-table th {
            background-color: #f4f4f4;
        }
        .delete-btn {
            padding: 8px 15px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .buy-all-btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .buy-all-btn:hover {
            background-color: #218838;
        }
        .empty-cart {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Your Cart</h2>
            <?php
            // Only show Buy All button if cart has items
            $cart_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM cart WHERE userid='$userid' AND usertype='$usertype'"));
            if($cart_count > 0) {
                ?>
                <form method="POST" onsubmit="return confirm('Are you sure you want to purchase all items?');">
                    <button type="submit" name="buyall" class="buy-all-btn">Buy All Items</button>
                </form>
                <?php
            }
            ?>
        </div>
        
        <?php
        // Get cart items
        $cart_query = mysqli_query($conn, "SELECT c.*, u.username as donor_name, f.expirydate 
                                         FROM cart c 
                                         JOIN users u ON c.donorid = u.userid 
                                         JOIN food f ON c.foodid = f.foodid
                                         WHERE c.userid='$userid' AND c.usertype='$usertype'
                                         ORDER BY c.cartid DESC");
        
        if(mysqli_num_rows($cart_query) > 0) {
            ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Cart ID</th>
                        <th>Food Name</th>
                        <th>Quantity</th>
                        <th>Donor Name</th>
                        <th>Expiry Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($item = mysqli_fetch_assoc($cart_query)) {
                        ?>
                        <tr>
                            <td><?php echo $item['cartid']; ?></td>
                            <td><?php echo $item['foodname']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo $item['donor_name']; ?></td>
                            <td><?php echo $item['expirydate']; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="cartid" value="<?php echo $item['cartid']; ?>">
                                    <button type="submit" name="delete" class="delete-btn" 
                                            onclick="return confirm('Are you sure you want to remove this item?')">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<div class="empty-cart">Your cart is empty.</div>';
        }
        ?>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>