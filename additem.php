<?php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database
    //$conn = mysqli_connect("localhost", "root", "", "webproject");
    include "config.php";
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Get the current user's ID from the users table
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    
    $user_query = "SELECT userid FROM users WHERE username = '$username' AND userpassword = '$password'";
    $user_result = mysqli_query($conn, $user_query);
    
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_result);
        $userid = $user_row['userid'];
        
        // Get form data
        $foodname = $_POST['foodname'];
        $quantity = (int)$_POST['quantity'];
        
        // SQL statement to insert data
        $query = "INSERT INTO food (foodname, quantity, userid) 
                  VALUES ('$foodname', $quantity, $userid)";
        
        // Execute the query
        if (mysqli_query($conn, $query)) {
            $success_message = "Food item added successfully!";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Error: Unable to verify user credentials.";
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food Item</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .submit-btn {
            background-color: #974de1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .success-message {
            color: green;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #e8f5e9;
            border-radius: 4px;
        }

        .error-message {
            color: red;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #ffebee;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Food Item</h2>
        
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="foodname">Food Name (max 50 characters):</label>
                <input type="text" id="foodname" name="foodname" maxlength="50" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" min="1" max="100" required>
            </div>

            <button type="submit" class="submit-btn">Add Food Item</button>
        </form>
    </div>
</body>
</html>