<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // データベース接続
    $dsn = 'mysql:host=localhost;dbname=camera_rental;charset=utf8';
    $db_user = 'root';
    $db_pass = '';
    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
    } catch (PDOException $e) {
        echo "データベース接続エラー: " . $e->getMessage();
        exit;
    }

    // ユーザーの認証
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        echo "ログイン成功！";
    } else {
        echo "ユーザー名またはパスワードが間違っています。";
    }
}
?>
