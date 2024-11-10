<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $camera_id = $_POST["camera_id"];
    $user_id = $_POST["user_id"]; // ユーザーID
    $rental_days = $_POST["rental_days"];

    // データベース接続（例としてPDOを使用）
    $dsn = 'mysql:host=localhost;dbname=camera_rental;charset=utf8';
    $username = 'root';
    $password = '';
    try {
        $pdo = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        echo "データベース接続エラー: " . $e->getMessage();
        exit;
    }

    // レンタルリクエストをデータベースに挿入
    $sql = "INSERT INTO rental_requests (camera_id, user_id, rental_days) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$camera_id, $user_id, $rental_days]);

    echo "レンタルリクエストが送信されました。";
} else {
    echo "無効なリクエストです。";
}
?>
