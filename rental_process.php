<?php
session_start();

// ログイン確認
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "<script>alert('レンタルするにはログインが必要です。'); window.location.href = 'login.html';</script>";
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

// レンタル情報の取得
$user_id = $_SESSION["user_id"];
$camera_id = $_POST["camera_id"];
$start_date = $_POST["start_date"];
$end_date = $_POST["end_date"];

// 入力チェック
if (empty($start_date) || empty($end_date) || strtotime($start_date) > strtotime($end_date)) {
    echo "<script>alert('正しいレンタル期間を入力してください。'); window.history.back();</script>";
    exit;
}

// トランザクション開始
$pdo->beginTransaction();

try {
    // レンタル情報を保存
    $sql_rental = "INSERT INTO rentals (user_id, camera_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'active')";
    $stmt_rental = $pdo->prepare($sql_rental);
    $stmt_rental->execute([$user_id, $camera_id, $start_date, $end_date]);

    // カメラステータスを更新
    $sql_update_camera = "UPDATE cameras SET status = 'rented' WHERE id = ?";
    $stmt_update_camera = $pdo->prepare($sql_update_camera);
    $stmt_update_camera->execute([$camera_id]);

    // コミット
    $pdo->commit();

    echo "<script>alert('レンタルが申し込まれました。'); window.location.href = 'mypage.php';</script>";
} catch (PDOException $e) {
    // ロールバック
    $pdo->rollBack();
    echo "レンタル申請エラー: " . $e->getMessage();
}
?>
