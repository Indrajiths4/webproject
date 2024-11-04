<?php


// Connect to database
$conn = mysqli_connect("localhost", "root", "", "project");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get users ordered by their score
$query = "SELECT username, userscore 
          FROM users 
          ORDER BY userscore DESC";

$result = mysqli_query($conn, $query);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Useless Items Leaderboard</title>
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes glow {
            0% { box-shadow: 0 0 5px #974de1; }
            50% { box-shadow: 0 0 20px #974de1; }
            100% { box-shadow: 0 0 5px #974de1; }
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px;
        }

        .header h1 {
            font-size: 2.5em;
            color: #974de1;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
            text-shadow: 0 0 10px rgba(151, 77, 225, 0.5);
        }

        .trophy-icon {
            font-size: 3em;
            margin-bottom: 10px;
            color: #ffd700;
        }

        .leaderboard-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .leaderboard-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            transition: transform 0.3s ease;
            animation: slideIn 0.5s ease forwards;
        }

        .leaderboard-item:hover {
            transform: translateX(10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .rank {
            font-size: 1.5em;
            font-weight: bold;
            width: 50px;
            text-align: center;
            color: black;
        }

        .rank-1 { 
            background: linear-gradient(45deg, rgba(255,215,0,0.15) 0%, rgba(255,215,0,0.05) 100%);
        }
        .rank-1 .rank { color: #ffd700; }
        .rank-1 .medal { content: "👑"; }

        .rank-2 {
            background: linear-gradient(45deg, rgba(192,192,192,0.15) 0%, rgba(192,192,192,0.05) 100%);
        }
        .rank-2 .rank { color: #c0c0c0; }
        .rank-2 .medal { content: "🥈"; }

        .rank-3 {
            background: linear-gradient(45deg, rgba(205,127,50,0.15) 0%, rgba(205,127,50,0.05) 100%);
        }
        .rank-3 .rank { color: #cd7f32; }
        .rank-3 .medal { content: "🥉"; }

        .username {
            flex-grow: 1;
            margin-left: 20px;
            font-size: 1.2em;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            color: black;
        }

        .medal {
            font-size: 1.4em;
        }

        .score {
            font-size: 1.3em;
            font-weight: bold;
            color: #974de1;
            min-width: 100px;
            text-align: right;
        }

        .top-3 {
            animation: glow 2s infinite;
        }

        .score-details {
            font-size: 0.8em;
            color: #974de1;
            margin-top: 5px;
        }

        .current-user {
            border: 2px solid #974de1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="trophy-icon">🏆</div>
            <h1>Leaderboard</h1>
        </div>

        <div class="leaderboard-container">
            <?php 
            $rank = 1;
            while ($row = mysqli_fetch_assoc($result)): 
                $isCurrentUser = isset($_SESSION['username']) && $_SESSION['username'] === $row['username'];
                $rankClass = $rank <= 3 ? "rank-{$rank} top-3" : "";
            ?>
                <div class="leaderboard-item <?php echo $rankClass; ?> <?php echo $isCurrentUser ? 'current-user' : ''; ?>" 
                     style="animation-delay: <?php echo $rank * 0.1; ?>s">
                    <div class="rank">#<?php echo $rank; ?></div>
                    <div class="username">
                        <?php echo htmlspecialchars($row['username']); ?>
                        <?php if($rank === 1): ?>
                            <span class="medal">👑</span>
                        <?php elseif($rank === 2): ?>
                            <span class="medal">🥈</span>
                        <?php elseif($rank === 3): ?>
                            <span class="medal">🥉</span>
                        <?php endif; ?>
                    </div>
                    <div class="score">
                        <?php echo number_format($row['userscore'], 2); ?>
                        <div class="score-details">points</div>
                    </div>
                </div>
            <?php 
                $rank++;
            endwhile; 
            ?>
        </div>
    </div>
</body>
</html>