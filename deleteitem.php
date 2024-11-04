<?php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// First get the userid from users table based on session credentials
include "config.php";
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = mysqli_real_escape_string($conn, $_SESSION['username']);
$password = mysqli_real_escape_string($conn, $_SESSION['password']);

$user_query = "SELECT userid FROM users WHERE username = '$username' AND userpassword = '$password'";
$user_result = mysqli_query($conn, $user_query);

if (!$user_result || mysqli_num_rows($user_result) == 0) {
    header("Location: login.php");
    exit();
}

$user_row = mysqli_fetch_assoc($user_result);
$userid = $user_row['userid'];

// Handle delete request
if (isset($_POST['delete_food'])) {
    $foodid = (int)$_POST['foodid'];
    
    // Only allow deletion if the food item belongs to the user
    $query = "DELETE FROM food WHERE foodid = $foodid AND userid = $userid";
    
    if (mysqli_query($conn, $query)) {
        $success_message = "Food item deleted successfully!";
    } else {
        $error_message = "Error deleting food item: " . mysqli_error($conn);
    }
}

// Fetch user's food items
$query = "SELECT * FROM food WHERE userid = $userid ORDER BY foodid DESC";
$result = mysqli_query($conn, $query);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Food Items</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .success-message {
            color: green;
            background-color: #e8f5e9;
        }

        .error-message {
            color: red;
            background-color: #ffebee;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .item-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .item-name {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .item-meta {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .no-items {
            text-align: center;
            padding: 40px;
            color: #666;
            grid-column: 1 / -1;
        }

        .expiry-warning {
            color: #dc3545;
            font-weight: bold;
            margin-top: 5px;
        }

        .expiry-near {
            color: #ffc107;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Food Items</h2>
        
        <?php if ($success_message): ?>
            <div class="message success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="items-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($food = mysqli_fetch_assoc($result)): ?>
                    <div class="item-card">
                        <div class="item-name"><?php echo htmlspecialchars($food['foodname']); ?></div>
                        <div class="item-meta">
                            Quantity: <?php echo htmlspecialchars($food['quantity']); ?><br>
                            Expiry Date: <?php echo date('d-m-Y', strtotime($food['expirydate'])); ?>
                            
                            <?php
                            $expiry_date = strtotime($food['expirydate']);
                            $today = strtotime('today');
                            $days_until_expiry = floor(($expiry_date - $today) / (60 * 60 * 24));
                            
                            if ($days_until_expiry < 0): ?>
                                <div class="expiry-warning">Expired!</div>
                            <?php elseif ($days_until_expiry <= 7): ?>
                                <div class="expiry-near">Expires in <?php echo $days_until_expiry; ?> days</div>
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this food item?');">
                            <input type="hidden" name="foodid" value="<?php echo $food['foodid']; ?>">
                            <button type="submit" name="delete_food" class="delete-btn">Delete Food Item</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items">
                    <h3>No food items found</h3>
                    <p>You haven't added any food items yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>