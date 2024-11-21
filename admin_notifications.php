<?php
session_start();

// 管理者チェック
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== 'admin') {
    echo "<script>alert('アクセス権限がありません。'); window.location.href = 'index.php';</script>";
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

// 通知一覧を取得
$sql = "SELECT n.id AS notification_id, u.username, n.camera_name, n.created_at
        FROM notifications n
        JOIN users u ON n.user_id = u.id
        WHERE n.status = 'pending'";
$stmt = $pdo->query($sql);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者通知</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <header>
        <h1>管理者用通知</h1>
        <nav>
            <a href="index.php">ホーム</a>
            <a href="logout.php" class="button">ログアウト</a>
        </nav>
    </header>
    <main>
        <h2>保管申請通知</h2>
        <?php if (count($notifications) > 0): ?>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li>
                        <p><strong><?php echo htmlspecialchars($notification["username"]); ?></strong>さんが
                        <strong><?php echo htmlspecialchars($notification["camera_name"]); ?></strong>の保管を申請しました。</p>
                        <form action="admin_camera_register.php" method="GET">
                            <input type="hidden" name="notification_id" value="<?php echo htmlspecialchars($notification["notification_id"]); ?>">
                            <button type="submit" class="button">登録処理を開始</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>保管申請はありません。</p>
        <?php endif; ?>
    </main>
</body>
</html>
