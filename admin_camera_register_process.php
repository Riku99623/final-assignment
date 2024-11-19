<?php
session_start();

// 管理者チェック
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    echo "<script>alert('アクセス権限がありません。'); window.location.href = 'index.html';</script>";
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

// カメラ登録処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $camera_name = $_POST["camera_name"];
    $camera_specs = $_POST["camera_specs"];
    $price_per_night = $_POST["price_per_night"];

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

            // データベースに登録
            $sql = "INSERT INTO cameras (name, image_path, specs, price_per_night, status) VALUES (?, ?, ?, ?, 'available')";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$camera_name, $relative_path, $camera_specs, $price_per_night]);
                echo "<script>alert('カメラが登録されました。'); window.location.href = 'admin_camera_register.php';</script>";
            } catch (PDOException $e) {
                echo "カメラ登録エラー: " . $e->getMessage();
            }
        } else {
            echo "<script>alert('ファイルのアップロードに失敗しました。'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('画像ファイルが正しくアップロードされていません。'); window.history.back();</script>";
    }
}
?>