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

// POSTデータを取得
$rental_id = $_POST["rental_id"];
$camera_id = $_POST["camera_id"];

// トランザクション開始
$pdo->beginTransaction();

try {
    // レンタル情報を更新
    $sql_update_rental = "UPDATE rentals SET status = 'completed' WHERE id = ?";
    $stmt_update_rental = $pdo->prepare($sql_update_rental);
    $stmt_update_rental->execute([$rental_id]);

    // カメラステータスを更新
    $sql_update_camera = "UPDATE cameras SET status = 'available' WHERE id = ?";
    $stmt_update_camera = $pdo->prepare($sql_update_camera);
    $stmt_update_camera->execute([$camera_id]);

    // コミット
    $pdo->commit();

    echo "<script>alert('カメラを返却しました。'); window.location.href = 'mypage.php';</script>";
} catch (PDOException $e) {
    // ロールバック
    $pdo->rollBack();
    echo "<script>alert('返却処理中にエラーが発生しました。'); window.history.back();</script>";
}
?>
