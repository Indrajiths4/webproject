<?php


if (!isset($_SESSION['username'])) {
    include "login.php";
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "webproject");

// Get the userid of the current doner
$username = $_SESSION['username'];
$password = $_SESSION['password'];
$user_query = mysqli_query($conn, "SELECT userid, usertype FROM users WHERE username='$username' AND userpassword='$password'");
$user_data = mysqli_fetch_assoc($user_query);
$userid = $user_data['userid'];
$usertype = $user_data['usertype'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bought Items - Food Share</title>
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
<body>
    

    <div class="container">
        <h2 style="margin-bottom: 1.5rem;">Bought Items - NGO: <?php echo $username; ?></h2>
        <div class="items-grid">
            <?php
            // Get all the food items that have been bought by NGOs and Organic Farmers for the current doner
            $order_query = mysqli_query($conn, "
                                                SELECT *
                                                FROM orders 
                                                WHERE userid = '$userid' AND usertype = '$usertype'
                                            ");

            // Display the bought items
            if (mysqli_num_rows($order_query) > 0) {
                echo '<div class="items-grid">';
                while ($ngo_row = mysqli_fetch_assoc($order_query)) {
                    ?>
                    <div class="item-card">
                        <h3 class="item-name"><?php echo $ngo_row['foodname']; ?></h3>
                        <div class="item-details">
                            <p>Quantity: <?php echo $ngo_row['quantity']; ?></p>
                            <p>Expiry Date: <?php echo $ngo_row['expirydate']; ?></p>
                            <p>Sold by Doner: <?php echo $ngo_row['donorid']; ?></p>
                        </div>
                        <div class="item-footer">
                            <span>Food ID: <?php echo $ngo_row['foodid']; ?></span>
                        </div>
                    </div>
                    <?php
                }
                
                echo '</div>';
            } else {
                echo '<div class="no-items">No items have been bought yet.</div>';
            }

            mysqli_close($conn);
            ?>
        </div>
    </div>
</body>
</html>