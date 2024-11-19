<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カメラ登録</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>管理者専用カメラ登録</h1>
        <nav>
            <a href="index.php">ホーム</a>
            <a href="logout.php">ログアウト</a>
        </nav>
    </header>
    <main>
        <form action="admin_camera_register_process.php" method="POST" enctype="multipart/form-data">
            <label for="camera_name">カメラ名</label>
            <input type="text" id="camera_name" name="camera_name" required>

            <label for="camera_image">カメラの写真</label>
            <input type="file" id="camera_image" name="camera_image" accept="image/*" required>

            <label for="camera_specs">性能</label>
            <textarea id="camera_specs" name="camera_specs" required></textarea>

            <label for="price_per_night">一泊あたりの値段 (円)</label>
            <input type="number" id="price_per_night" name="price_per_night" min="0" step="0.01" required>

            <button type="submit" class="button">登録</button>
        </form>
    </main>
</body>
</html>
