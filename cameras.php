<?php
// データベース接続
$dsn = 'mysql:host=localhost;dbname=camera;charset=utf8';
$db_user = 'root';
$db_pass = '';
try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit;
}

// カメラ情報取得
$sql = "SELECT * FROM cameras WHERE status = 'available'";
$stmt = $pdo->query($sql);
$cameras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カメラ一覧</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSSを直接埋め込む場合 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        main {
            padding: 1rem;
        }
        ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        li {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 1rem;
            padding: 1rem;
            width: calc(33% - 2rem); /* カラム幅 */
            box-sizing: border-box;
            text-align: center;
        }
        li img {
            max-width: 100%; /* 幅を親要素に収める */
            max-height: 200px; /* 高さ制限 */
            object-fit: contain; /* アスペクト比を保ちながら収める */
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        @media (max-width: 768px) {
            li {
                width: calc(50% - 2rem); /* スマホ画面での幅調整 */
            }
        }
        @media (max-width: 480px) {
            li {
                width: calc(100% - 2rem); /* 1カラム表示 */
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>カメラ一覧</h1>
        <nav>
            <a href="index.php">ホーム</a>
            <a href="mypage.php">マイページ</a>
        </nav>
    </header>
    <main>
        <ul>
            <?php foreach ($cameras as $camera): ?>
                <li>
                    <h2><?php echo htmlspecialchars($camera["name"]); ?></h2>
                    <img src="<?php echo htmlspecialchars($camera["image_path"]); ?>" alt="カメラ画像">
                    <p><?php echo htmlspecialchars($camera["specs"]); ?></p>
                    <p>一泊あたりの値段: ¥<?php echo htmlspecialchars(number_format($camera["price_per_night"], 2)); ?></p>
                    <a href="rental_period.php?camera_id=<?php echo $camera['id']; ?>" class="button">レンタルする</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>
