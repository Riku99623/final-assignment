<?php
session_start();

// ログイン確認
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "<script>alert('ログインしてください。'); window.location.href = 'login.html';</script>";
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
$camera_name = $_POST["camera_name"];
$storage_start_date = $_POST["storage_start_date"];
$storage_end_date = $_POST["storage_end_date"];
$user_id = $_SESSION["user_id"];

// 入力検証
if (empty($camera_name) || empty($storage_start_date) || empty($storage_end_date)) {
    echo "<script>alert('すべての項目を入力してください。'); window.history.back();</script>";
    exit;
}

if (strtotime($storage_start_date) > strtotime($storage_end_date)) {
    echo "<script>alert('保管終了日は保管開始日以降の日付を選択してください。'); window.history.back();</script>";
    exit;
}

// データベースに保管情報を挿入
$sql = "INSERT INTO cameras (user_id, name, storage_start_date, storage_end_date, status) 
               VALUES (?, ?, ?, ?, 'stored')";
$stmt = $pdo->prepare($sql);

$sql_notification = "INSERT INTO notifications (user_id, camera_name) VALUES (?, ?)";
$stmt_notification = $pdo->prepare($sql_notification);

try {
    $pdo->beginTransaction();
    $stmt->execute([$user_id, $camera_name, $storage_start_date, $storage_end_date]);
    $stmt_notification->execute([$user_id, $camera_name]);
    $pdo->commit();
    echo "<script>alert('カメラの保管申請が完了しました。管理者が登録するまでお待ちください。'); window.location.href = 'mypage.php';</script>";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "保管申請エラー: " . $e->getMessage();
}
?>
