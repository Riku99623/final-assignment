<?php
session_start();

// ログイン確認
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

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

// ログイン中のユーザーIDを取得
$user_id = $_SESSION["user_id"];

// レンタル中のカメラ情報を取得
$sql_rentals = "
    SELECT r.id AS rental_id, c.name AS camera_name, c.image_path, r.start_date, r.end_date, r.status
    FROM rentals r
    JOIN cameras c ON r.camera_id = c.id
    WHERE r.user_id = ? AND r.status = 'active'";
$stmt_rentals = $pdo->prepare($sql_rentals);
$stmt_rentals->execute([$user_id]);
$active_rentals = $stmt_rentals->fetchAll(PDO::FETCH_ASSOC);

// レンタル履歴を取得
$sql_history = "
    SELECT r.id AS rental_id, c.name AS camera_name, c.image_path, r.start_date, r.end_date, r.status
    FROM rentals r
    JOIN cameras c ON r.camera_id = c.id
    WHERE r.user_id = ? AND r.status = 'completed'";
$stmt_history = $pdo->prepare($sql_history);
$stmt_history->execute([$user_id]);
$rental_history = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

// 保管中のカメラリストを取得
$sql_storage = "
    SELECT name, image_path, specs, created_at
    FROM cameras
    WHERE user_id = ? AND status = 'stored'";
$stmt_storage = $pdo->prepare($sql_storage);
$stmt_storage->execute([$user_id]);
$stored_cameras = $stmt_storage->fetchAll(PDO::FETCH_ASSOC);

// ユーザープロフィール情報を取得
$sql_user = "SELECT username, email FROM users WHERE id = ?";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([$user_id]);
$user_info = $stmt_user->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* カメラリストのスタイル */
        .camera-list {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .camera-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 1rem;
            padding: 1rem;
            width: calc(33% - 2rem);
            box-sizing: border-box;
            text-align: center;
        }
        .camera-item img {
            max-width: 100%;
            max-height: 150px;
            object-fit: contain;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <h1>📋 マイページ</h1>
        <nav>
            <a href="index.php">ホーム</a>
            <a href="logout.php" class="button">ログアウト</a>
        </nav>
    </header>

    <main>
        <!-- ユーザープロフィール -->
        <section>
            <h2>👤 プロフィール情報</h2>
            <p><strong>ユーザー名:</strong> <?php echo htmlspecialchars($user_info["username"]); ?></p>
            <p><strong>メールアドレス:</strong> <?php echo htmlspecialchars($user_info["email"]); ?></p>
        </section>

        <!-- 保管中のカメラ -->
        <section>
            <h2>🛠️ 保管中のカメラ</h2>
            <?php if (count($stored_cameras) > 0): ?>
                <ul class="camera-list">
                    <?php foreach ($stored_cameras as $camera): ?>
                        <li class="camera-item">
                            <h3><?php echo htmlspecialchars($camera["name"]); ?></h3>
                            <img src="<?php echo htmlspecialchars($camera["image_path"]); ?>" alt="カメラ画像">
                            <p><?php echo htmlspecialchars($camera["specs"]); ?></p>
                            <p>登録日: <?php echo htmlspecialchars($camera["created_at"]); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>現在保管中のカメラはありません。</p>
            <?php endif; ?>
        </section>

        <!-- レンタル中のカメラ -->
        <section>
          <h2>📷 レンタル中のカメラ</h2>
          <?php if (count($active_rentals) > 0): ?>
            <ul>
             <?php foreach ($active_rentals as $rental): ?>
                <li>
                    <h3><?php echo htmlspecialchars($rental["camera_name"]); ?></h3>
                    <img src="<?php echo htmlspecialchars($rental["image_path"]); ?>" alt="カメラ画像" style="max-width: 150px; max-height: 150px;">
                    <p>レンタル期間: <?php echo htmlspecialchars($rental["start_date"]); ?> 〜 <?php echo htmlspecialchars($rental["end_date"]); ?></p>
                    <form action="return_camera.php" method="POST">
                        <input type="hidden" name="rental_id" value="<?php echo htmlspecialchars($rental["rental_id"]); ?>">
                        <button type="submit" class="button">返却する</button>
                    </form>
                </li>
              <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p>現在レンタル中のカメラはありません。</p>
          <?php endif; ?>
        </section>


        <!-- レンタル履歴 -->
        <section>
            <h2>📜 レンタル履歴</h2>
            <?php if (count($rental_history) > 0): ?>
                <ul class="camera-list">
                    <?php foreach ($rental_history as $history): ?>
                        <li class="camera-item">
                            <h3><?php echo htmlspecialchars($history["camera_name"]); ?></h3>
                            <img src="<?php echo htmlspecialchars($history["image_path"]); ?>" alt="カメラ画像">
                            <p>レンタル期間: <?php echo htmlspecialchars($history["start_date"]); ?> 〜 <?php echo htmlspecialchars($history["end_date"]); ?></p>
                            <p>ステータス: <?php echo htmlspecialchars($history["status"] === 'completed' ? '完了' : '不明'); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>レンタル履歴はまだありません。</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
