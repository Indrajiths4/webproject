<div class="container">
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