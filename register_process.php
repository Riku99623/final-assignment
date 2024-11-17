<?php
// デバッグ用のエラーメッセージ表示
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
    $dsn = 'mysql:host=127.0.0.1;dbname=camera;charset=utf8';
    $db_user = 'root';   // データベースユーザー名
    $db_pass = '';       // データベースパスワード

    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
    } catch (PDOException $e) {
        echo "データベース接続エラー: " . $e->getMessage();
        exit;
    }

    // ユーザー情報を挿入
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$username, $email, $hashed_password]);
        echo "<script>alert('会員登録が完了しました。ログインページに移動します。'); window.location.href = 'login.html';</script>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<script>alert('このメールアドレスは既に登録されています。'); window.history.back();</script>";
        } else {
            echo "登録エラー: " . $e->getMessage();
        }
    }
}
?>
