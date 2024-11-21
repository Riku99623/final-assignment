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

// POSTデータの取得
$user_id = $_POST["user_id"];
$notification_id = $_POST["notification_id"];
$camera_name = $_POST["camera_name"];
$camera_specs = $_POST["camera_specs"];

// ファイルアップロード処理
if (isset($_FILES["camera_image"]) && $_FILES["camera_image"]["error"] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES["camera_image"]["tmp_name"];
    $file_name = basename($_FILES["camera_image"]["name"]);
    $upload_dir = __DIR__ . "/uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_path = $upload_dir . uniqid() . "_" . $file_name;

    if (move_uploaded_file($file_tmp, $file_path)) {
        $relative_path = "uploads/" . basename($file_path);

        // カメラ情報を登録し、通知を更新
        $sql = "UPDATE cameras SET name = ?, specs = ?, image_path = ?,status='available', is_registered = 1 WHERE user_id = ? AND name = ?";
        $stmt = $pdo->prepare($sql);

        $sql_notification = "UPDATE notifications SET status = 'processed' WHERE id = ?";
        $stmt_notification = $pdo->prepare($sql_notification);

        try {
            $pdo->beginTransaction();
            $stmt->execute([$camera_name, $camera_specs, $relative_path, $user_id, $camera_name]);
            $stmt_notification->execute([$notification_id]);
            $pdo->commit();

            echo "<script>alert('カメラが登録されました。'); window.location.href = 'admin_notifications.php';</script>";
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "カメラ登録エラー: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('画像アップロードに失敗しました。'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('画像ファイルが正しくアップロードされていません。'); window.history.back();</script>";
}
?>
