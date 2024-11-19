


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者登録</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>管理者登録</h1>
    </header>
    <main>
        <form id="admin-register-form" action="admin_register_process.php" method="POST">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" required>

            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" required>

            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="button">登録</button>
        </form>
    </main>
</body>
</html>
