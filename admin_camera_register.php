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

// 通知情報の取得
$notification_id = isset($_GET["notification_id"]) ? intval($_GET["notification_id"]) : null;
if (!$notification_id) {
    echo "<script>alert('通知情報が見つかりません。'); window.location.href = 'admin_notifications.php';</script>";
    exit;
}

// 通知に関連するユーザーとカメラ情報を取得
$sql_notification = "
    SELECT n.id AS notification_id, n.camera_name, u.id AS user_id, u.username
    FROM notifications n
    JOIN users u ON n.user_id = u.id
    WHERE n.id = ? AND n.status = 'pending'";
$stmt = $pdo->prepare($sql_notification);
$stmt->execute([$notification_id]);
$notification = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$notification) {
    echo "<script>alert('有効な通知が見つかりません。'); window.location.href = 'admin_notifications.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カメラ登録</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>カメラ登録</h1>
        <nav>
            <a href="admin_notifications.php">通知一覧</a>
            <a href="logout.php">ログアウト</a>
        </nav>
    </header>
    <main>
        <h2>保管申請されたカメラを登録</h2>
        <p><strong>申請者:</strong> <?php echo htmlspecialchars($notification['username']); ?></p>
        <p><strong>カメラ名:</strong> <?php echo htmlspecialchars($notification['camera_name']); ?></p>

        <form action="admin_camera_register_process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($notification['user_id']); ?>">
            <input type="hidden" name="notification_id" value="<?php echo htmlspecialchars($notification['notification_id']); ?>">

            <label for="camera_name">カメラ名</label>
            <input type="text" id="camera_name" name="camera_name" value="<?php echo htmlspecialchars($notification['camera_name']); ?>" required>

            <label for="camera_specs">性能</label>
            <textarea id="camera_specs" name="camera_specs" required></textarea>

            <label for="camera_image">カメラ画像</label>
            <input type="file" id="camera_image" name="camera_image" accept="image/*" required>

            <label for="price_per_night">一泊あたりの値段 (円)</label>
            <input type="number" id="price_per_night" name="price_per_night" min="0" step="0.01" required>

            <button type="submit" class="button">カメラを登録する</button>
        </form>
    </main>
</body>
</html>
