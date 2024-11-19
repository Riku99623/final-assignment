<?php
session_start();

// デバッグ用のエラーメッセージ表示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

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

    // ユーザー情報の取得
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // 認証成功
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"]; // ユーザーのロールを保存
        $_SESSION["loggedin"] = true;

        echo "<script>alert('ログインに成功しました。'); window.location.href = 'index.php';</script>";
    } else {
        // 認証失敗
        echo "<script>alert('メールアドレスまたはパスワードが正しくありません。'); window.history.back();</script>";
    }
}
?>
