<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カメラ一覧</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>カメラ一覧</h1>
        <nav>
            <a href="index.php">ホーム</a>
            <a href="store_camera.html">カメラを保管する</a>
        </nav>
    </header>
    <main>
        <ul id="camera-list">
            <?php
            // データベース接続
            $dsn = 'mysql:host=localhost;dbname=camera_rental;charset=utf8';
            $username = 'root';
            $password = '';
            try {
                $pdo = new PDO($dsn, $username, $password);
            } catch (PDOException $e) {
                echo "データベース接続エラー: " . $e->getMessage();
                exit;
            }

            // 利用可能なカメラを取得
            $sql = "SELECT * FROM cameras WHERE status = 'available'";
            $stmt = $pdo->query($sql);
            $cameras = $stmt->fetchAll();

            // カメラ情報を表示
            foreach ($cameras as $camera) {
                echo "<li class='camera-card'>";
                echo "<h3>" . htmlspecialchars($camera['name']) . "</h3>";
                echo "<img src='" . htmlspecialchars($camera['image_url']) . "' alt='" . htmlspecialchars($camera['name']) . "'>";
                echo "<p>" . htmlspecialchars($camera['description']) . "</p>";
                echo "<a href='camera_detail.html?id=" . $camera['id'] . "' class='button'>詳細を見る</a>";
                echo "</li>";
            }
            ?>
        </ul>
    </main>
</body>
</html>
