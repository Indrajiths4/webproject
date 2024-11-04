<div class="container">
    <div class="items-grid">
        <?php
        $conn = mysqli_connect("localhost", "root", "", "webproject");

        // Fetch all food items where expirydate is not past
        $result = mysqli_query($conn, "SELECT * FROM food WHERE expirydate >= CURDATE() ORDER BY foodid DESC");

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
                    <div class="item-actions">
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="foodid" value="<?php echo $row['foodid']; ?>">
                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
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
