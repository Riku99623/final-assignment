<?php
// エラーメッセージの表示（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $postal_code = $_POST["postal_code"];
    $prefecture = $_POST["prefecture"];
    $city = $_POST["city"];
    $street = $_POST["street"];

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

    // ユーザー情報を挿入
    $sql = "INSERT INTO users (username, email, password, postal_code, prefecture, city, street) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$username, $email, $hashed_password, $postal_code, $prefecture, $city, $street]);
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
