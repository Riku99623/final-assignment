<?php
session_start();

// ログインしていない場合はログインページにリダイレクト
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// データベース接続情報
$dsn = 'mysql:host=localhost;dbname=camera_rental;charset=utf8';
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
$sql_rentals = "SELECT * FROM rentals WHERE user_id = ? AND status = 'active'";
$stmt_rentals = $pdo->prepare($sql_rentals);
$stmt_rentals->execute([$user_id]);
$active_rentals = $stmt_rentals->fetchAll(PDO::FETCH_ASSOC);

// 保管中のカメラ情報を取得
$sql_storage = "SELECT * FROM cameras WHERE user_id = ? AND status = 'stored'";
$stmt_storage = $pdo->prepare($sql_storage);
$stmt_storage->execute([$user_id]);
$stored_cameras = $stmt_storage->fetchAll(PDO::FETCH_ASSOC);

// レンタル履歴を取得
$sql_history = "SELECT * FROM rentals WHERE user_id = ? AND status = 'completed'";
$stmt_history = $pdo->prepare($sql_history);
$stmt_history->execute([$user_id]);
$rental_history = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

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
</head>
<body>
    <header>
        <h1>📋 マイページ</h1>
        <nav>
            <a href="index.html">ホーム</a>
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

        <!-- レンタル中のカメラ -->
        <section>
            <h2>📷 レンタル中のカメラ</h2>
            <?php if (count($active_rentals) > 0): ?>
                <ul>
                    <?php foreach ($active_rentals as $rental): ?>
                        <li><?php echo htmlspecialchars($rental["camera_name"]); ?> - 返却予定日: <?php echo htmlspecialchars($rental["return_date"]); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>現在レンタル中のカメラはありません。</p>
            <?php endif; ?>
        </section>

        <!-- 保管中のカメラ -->
        <section>
            <h2>🛠️ 保管中のカメラ</h2>
            <?php if (count($stored_cameras) > 0): ?>
                <ul>
                    <?php foreach ($stored_cameras as $camera): ?>
                        <li><?php echo htmlspecialchars($camera["name"]); ?> - 保管開始日: <?php echo htmlspecialchars($camera["created_at"]); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>現在保管中のカメラはありません。</p>
            <?php endif; ?>
        </section>

        <!-- レンタル履歴 -->
        <section>
            <h2>📜 レンタル履歴</h2>
            <?php if (count($rental_history) > 0): ?>
                <ul>
                    <?php foreach ($rental_history as $history): ?>
                        <li><?php echo htmlspecialchars($history["camera_name"]); ?> - 利用期間: <?php echo htmlspecialchars($history["start_date"]); ?> 〜 <?php echo htmlspecialchars($history["end_date"]); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>レンタル履歴はまだありません。</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
