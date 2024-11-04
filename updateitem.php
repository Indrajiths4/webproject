<?php
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: home.php");
    exit();
}

$success_message = '';
$error_message = '';

// First get the userid from users table based on session credentials
$conn = mysqli_connect("localhost", "root", "", "webproject");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = mysqli_real_escape_string($conn, $_SESSION['username']);
$password = mysqli_real_escape_string($conn, $_SESSION['password']);

$user_query = "SELECT userid FROM users WHERE username = '$username' AND userpassword = '$password'";
$user_result = mysqli_query($conn, $user_query);

if (!$user_result || mysqli_num_rows($user_result) == 0) {
    header("Location: home.php");
    exit();
}

$user_row = mysqli_fetch_assoc($user_result);
$userid = $user_row['userid'];

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_food'])) {
    $foodid = (int)$_POST['foodid'];
    $foodname = mysqli_real_escape_string($conn, $_POST['foodname']);
    $quantity = (int)$_POST['quantity'];
    
    // Only allow updating if the food item belongs to the user
    $query = "UPDATE food 
              SET foodname = '$foodname', 
                  quantity = $quantity 
              WHERE foodid = $foodid AND userid = $userid";
    
    if (mysqli_query($conn, $query)) {
        $success_message = "Food item updated successfully!";
    } else {
        $error_message = "Error updating food item: " . mysqli_error($conn);
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
    <title>Update Food Items</title>
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .update-btn {
            background-color: #974de1;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .update-btn:hover {
            background-color: #7e41bd;
        }

        .no-items {
            text-align: center;
            padding: 40px;
            color: #666;
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Food Items</h2>
        
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
                        <form method="POST" action="">
                            <input type="hidden" name="foodid" value="<?php echo $food['foodid']; ?>">
                            
                            <div class="form-group">
                                <label for="foodname_<?php echo $food['foodid']; ?>">Food Name:</label>
                                <input 
                                    type="text" 
                                    id="foodname_<?php echo $food['foodid']; ?>" 
                                    name="foodname" 
                                    value="<?php echo htmlspecialchars($food['foodname']); ?>"
                                    maxlength="50" 
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="quantity_<?php echo $food['foodid']; ?>">Quantity:</label>
                                <input 
                                    type="number" 
                                    id="quantity_<?php echo $food['foodid']; ?>" 
                                    name="quantity" 
                                    value="<?php echo htmlspecialchars($food['quantity']); ?>"
                                    min="0" 
                                    max="99999" 
                                    required
                                >
                            </div>

                            <button type="submit" name="update_food" class="update-btn">Update Food Item</button>
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