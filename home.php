<?php
session_start();
if (!isset($_SESSION['username'])) {
    include "login.php";
    exit();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('location: home.php');
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Food Items - Food Share</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2d3436;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            color: #2d3436;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            transition: background 0.3s ease;
        }

        .nav-links a:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #2d3436;
        }

        .logout-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: #ff4c4c;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .item-card {
            background: #f5f5f5;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .item-card:hover {
            transform: translateY(-5px);
        }

        .item-name {
            font-size: 1.4rem;
            color: #2d3436;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .item-details {
            color: #636e72;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 0.9rem;
        }

        .no-items {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-size: 1.2rem;
        }
    </style>
    
</head>
<?php if($_SESSION['role']=="doner") { ?> 
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="homego.php" class="logo">Food<span style="color: #974de1;">Share</span></a>
            <div class="nav-links">
                <a href="insert_item.php">Add Item</a>
                <a href="update_item.php">Update Item</a>
                <a href="delete_item.php">Delete Item</a>
                <a href="bought_item.php">Donated items</a>
            </div>
            <div class="user-section">
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                <form action="" method="POST" style="margin: 0;">
                    <button type="submit" name="logout" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <?php
    if($_SESSION['action']=="additem") {
        include "additem.php";
    }
    else if($_SESSION['action']=="home"){
    ?>
    <div class="container">
        <h2 style="margin-bottom: 1.5rem;">My Food Items</h2>
        <div class="items-grid">
            <?php
            $conn = mysqli_connect("localhost", "root", "", "webproject");
            
            // First get the userid from users table using the session username and password
            $username = $_SESSION['username'];
            $password = $_SESSION['password'];
            $user_query = mysqli_query($conn, "SELECT userid FROM users WHERE username='$username' AND userpassword='$password'");
            $user_data = mysqli_fetch_assoc($user_query);
            if ($user_data) {
                $userid = $user_data['userid'];
                // Now get all food items for this user
                $result = mysqli_query($conn, "SELECT * FROM food WHERE userid='$userid' ORDER BY foodid DESC");
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
                                <span>Food ID: <?php echo $row['foodid']; ?></span>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="no-items">You haven\'t added any food items yet.</div>';
                }
            } else {
                echo '<div class="no-items">Error: User not found.</div>';
            }
            mysqli_close($conn);
            ?>
        </div>
    </div>
    <?php 
    }
    else if($_SESSION['action']=="deleteitem") {
        include "deleteitem.php";
    }
    else if($_SESSION['action']=="updateitem") {
        include "updateitem.php";
    }
    else if($_SESSION['action']=="boughtitem") {
        include "boughtitem.php";
    }
    ?>
</body>


<?php } 

else if($_SESSION['role']=="ngo") {  ?>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="homego.php" class="logo">Food<span style="color: #974de1;">Share</span></a>
            <div class="nav-links">
                <a href="ngo_food.php">Food Items</a>
                <a href="ngo_cart.php">Cart</a>
                <a href="ngo_orders.php">Orders</a>
            </div>
            <div class="user-section">
                <span>Welcome, <?php echo $_SESSION['username']; ?> NGO</span>
                <form action="" method="POST" style="margin: 0;">
                    <button type="submit" name="logout" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <?php
    if($_SESSION['action']=="home") {
        include "ngofood.php";
    }
    else if($_SESSION['action']=="ngocart"){
        include "ngocart.php";
    }
    else if($_SESSION['action']=="ngoorders") {
        include "ngoorders.php";
    }
    ?>

</body>
<?php } 
else if($_SESSION['role']=="org") {  ?>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="homego.php" class="logo">Food<span style="color: #974de1;">Share</span></a>
            <div class="nav-links">
                <a href="ngo_food.php">Expired Food Items</a>
                <a href="ngo_cart.php">Cart</a>
                <a href="ngo_orders.php">Orders</a>
            </div>
            <div class="user-section">
                <span>Welcome, <?php echo $_SESSION['username']; ?> Organic Farmer</span>
                <form action="" method="POST" style="margin: 0;">
                    <button type="submit" name="logout" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <?php
    if($_SESSION['action']=="home") {
        include "ngofood.php";
    }
    else if($_SESSION['action']=="ngocart"){
        include "ngocart.php";
    }
    else if($_SESSION['action']=="ngoorders") {
        include "ngoorders.php";
    }
    ?>

</body>
<?php } ?>
</html>