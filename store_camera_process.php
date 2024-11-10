<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $image_url = $_POST["image_url"];

    // データベース接続
    $dsn = 'mysql:host=localhost;dbname=camera_rental;charset=utf8';
    $username = 'root';
    $password = '';
    try {
        $pdo = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        echo "データベース接続エラー: " . $e->getMessage();
        exit;
    }

    // カメラ情報の挿入
    $sql = "INSERT INTO cameras (name, description, image_url, status) VALUES (?, ?, ?, 'stored')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $description, $image_url]);

    // 保管申し込み完了メッセージとリダイレクト
    echo "<script>alert('カメラの保管が完了しました。'); window.location.href = 'cameras.html';</script>";
}
?>
