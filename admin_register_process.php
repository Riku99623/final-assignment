<?php
// デバッグ用エラーメッセージの表示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // パスワードのハッシュ化
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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

    // 管理者として登録
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$username, $email, $hashed_password]);
        echo "<script>alert('管理者登録が完了しました。'); window.location.href = 'login.html';</script>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // 重複するメールアドレス
            echo "<script>alert('このメールアドレスは既に登録されています。'); window.history.back();</script>";
        } else {
            echo "登録エラー: " . $e->getMessage();
        }
    }
}
?>
