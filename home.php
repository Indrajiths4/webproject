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

// Handle ratings
if (isset($_POST['rate_item'])) {
    $conn = mysqli_connect("localhost", "root", "", "webproject");
    $itemid = $_POST['itemid'];
    $rating = $_POST['rating'];
    // Get current rating data
    $result = mysqli_query($conn, "SELECT ratingcount, ratingavg, userid FROM items WHERE itemid = $itemid");
    $item = mysqli_fetch_assoc($result);
    // Calculate new rating for the item
    $newCount = $item['ratingcount'] + 1;
    $newAvg = (($item['ratingavg'] * $item['ratingcount']) + $rating) / $newCount;
    
    // Update the items table
    mysqli_query($conn, "UPDATE items SET ratingcount = $newCount, ratingavg = $newAvg WHERE itemid = $itemid");
    
    // Get all items for this user and calculate their new average score
    $userid = $item['userid'];
    $userItemsQuery = mysqli_query($conn, "SELECT ratingavg FROM items WHERE userid = '$userid'");
    
    $totalRating = 0;
    $itemCount = 0;
    
    while ($userItem = mysqli_fetch_assoc($userItemsQuery)) {
        $totalRating += $userItem['ratingavg'];
        $itemCount++;
    }
    
    // Calculate new user score (average of all their items' ratings)
    $newUserScore = $itemCount > 0 ? $totalRating / $itemCount : 0;
    
    // Update the user's score in the users table
    mysqli_query($conn, "UPDATE users SET userscore = $newUserScore WHERE password = '$userid'");
    
    mysqli_close($conn);
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Share</title>
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

        .item-description {
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

        .useless-score {
            font-weight: bold;
            color: #ff6b6b;
            margin-left: 1.5rem;
        }

        .rating-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

        .rating-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            justify-content: flex-end;
        }

        .rating-select {
            padding: 0.3rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
        }

        .rate-btn {
            background: #764ba2;
            color: white;
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .rate-btn:hover {
            background: #667eea;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .items-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="homego.php" class="logo">Trash<span style="color: #974de1;">Talk</span></a>
            <div class="nav-links">
                <a href="insert_item.php">Add Item</a>
                <a href="update_item.php">Update Item</a>
                <a href="delete_item.php">Delete Item</a>
                <a href="leader_board.php">Leaderboard</a>
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
        <div class="items-grid">
            <?php
            $conn = mysqli_connect("localhost", "root", "", "webproject");
            $result = mysqli_query($conn, "SELECT * FROM food WHERE userid!='$_SESSION[password]' ORDER BY ratingavg DESC");

            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="item-card">
                    <h3 class="item-name"><?php echo $row['itemname']; ?></h3>
                    <p class="item-description"><?php echo $row['itemdescription']; ?></p>
                    <div class="item-footer">
                        <span>Posted by: <?php echo $row['user']; ?></span>
                        <span class="useless-score">
                            Useless Score: <?php echo number_format($row['ratingavg'], 1); ?>/10
                            (<?php echo $row['ratingcount']; ?> ratings)
                        </span>
                    </div>
                    <div class="rating-section">
                        <form action="" method="POST" class="rating-form">
                            <input type="hidden" name="itemid" value="<?php echo $row['itemid']; ?>">
                            <select name="rating" class="rating-select">
                                <?php for ($i = 1; $i <= 10; $i++) { ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="rate_item" class="rate-btn">Rate</button>
                        </form>
                    </div>
                </div>
                <?php
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
    else if($_SESSION['action']=="leaderboard") {
        include "leaderboard.php";
    }
    ?>
</body>
</html>