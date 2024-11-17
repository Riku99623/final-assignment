<?php
session_start();
$_SESSION = array(); // セッション変数をすべて削除

// セッションを破棄
session_destroy();

// ログインページにリダイレクト
header("location: login.html");
exit;
?>
