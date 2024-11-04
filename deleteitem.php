<?php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle delete request
if (isset($_POST['delete_item'])) {
    $conn = mysqli_connect("localhost", "root", "", "webproject");
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $itemid = (int)$_POST['itemid'];
    $username = $_SESSION['username'];
    
    // Only allow deletion if the item belongs to the user
    $query = "DELETE FROM items WHERE itemid = $itemid AND user = '$username'";
    
    if (mysqli_query($conn, $query)) {
        $success_message = "Item deleted successfully!";
    } else {
        $error_message = "Error deleting item: " . mysqli_error($conn);
    }
    
    mysqli_close($conn);
}

// Fetch user's items
$conn = mysqli_connect("localhost", "root", "", "webproject");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$username = $_SESSION['username'];
$query = "SELECT * FROM items WHERE user = '$username' ORDER BY itemid DESC";
$result = mysqli_query($conn, $query);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Items</title>
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

        .item-description {
            color: #666;
            margin-bottom: 15px;
        }

        .item-meta {
            color: #888;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .no-items {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Items</h2>
        
        <?php if ($success_message): ?>
            <div class="message success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="items-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($result)): ?>
                    <div class="item-card">
                        <div class="item-name"><?php echo htmlspecialchars($item['itemname']); ?></div>
                        <div class="item-description"><?php echo htmlspecialchars($item['itemdescription']); ?></div>
                        <div class="item-meta">
                            Age: <?php echo htmlspecialchars($item['yearold']); ?> years<br>
                            Rating: <?php echo $item['ratingavg']; ?> (<?php echo $item['ratingcount']; ?> ratings)
                        </div>
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this item?');">
                            <input type="hidden" name="itemid" value="<?php echo $item['itemid']; ?>">
                            <button type="submit" name="delete_item" class="delete-btn">Delete Item</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items">
                    <h3>No items found</h3>
                    <p>You haven't added any items yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>