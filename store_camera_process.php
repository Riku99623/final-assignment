<?php
session_start();

// ログイン状態の確認
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.html");
    exit;
}

// データベース接続情報
$dsn = 'mysql:host=localhost;dbname=camera;charset=utf8';
$db_user = 'root';
$db_pass = '';
try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit;
}

// ユーザーIDを取得
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $camera_name = $_POST["camera_name"];
    $description = $_POST["description"];
    $image_url = $_POST["image_url"];

    // 入力値の検証
    if (empty($camera_name) || empty($description)) {
        echo "<script>alert('すべての必須項目を入力してください。'); window.history.back();</script>";
        exit;
    }

    // カメラ情報をデータベースに保存
    $sql = "INSERT INTO cameras (user_id, name, status, created_at) VALUES (?, ?, 'stored', NOW())";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$user_id, $camera_name]);

        echo "<script>alert('カメラが正常に保管されました。'); window.location.href = 'mypage.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('カメラの保管中にエラーが発生しました。'); window.history.back();</script>";
    }
} else {
    // POST以外のリクエストがあった場合
    header("location: store_camera.html");
    exit;
}
?>
