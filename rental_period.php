<?php
session_start();

// ログイン確認
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "<script>alert('ログインしてください。'); window.location.href = 'login.html';</script>";
    exit;
}

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

// カメラIDを取得
$camera_id = isset($_GET["camera_id"]) ? intval($_GET["camera_id"]) : null;

if (!$camera_id) {
    echo "<script>alert('カメラが選択されていません。'); window.location.href = 'cameras.php';</script>";
    exit;
}

// カメラ情報を取得
$sql = "SELECT name, price_per_night FROM cameras WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$camera_id]);
$camera = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$camera) {
    echo "<script>alert('指定されたカメラは存在しません。'); window.location.href = 'cameras.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レンタル期間選択</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // レンタル料金を計算して画面に反映する関数
        function calculatePrice() {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            const pricePerNight = <?php echo $camera['price_per_night']; ?>;

            // 日付の差分を計算
            if (startDate && endDate && endDate > startDate) {
                const nights = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)); // ミリ秒を日に変換
                const totalPrice = nights * pricePerNight;

                // 金額を画面に表示
                document.getElementById('price_display').innerText = `合計金額: ¥${totalPrice.toLocaleString()}`;
            } else {
                document.getElementById('price_display').innerText = "正しい日付を選択してください。";
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>レンタル期間選択</h1>
    </header>
    <main>
        <h2>カメラ: <?php echo htmlspecialchars($camera["name"]); ?></h2>
        <p>一泊あたりの値段: ¥<?php echo htmlspecialchars(number_format($camera["price_per_night"], 2)); ?></p>

        <form action="rental_process.php" method="POST">
            <input type="hidden" name="camera_id" value="<?php echo htmlspecialchars($camera_id); ?>">

            <label for="start_date">レンタル開始日</label>
            <input type="date" id="start_date" name="start_date" onchange="calculatePrice()" required>

            <label for="end_date">レンタル終了日</label>
            <input type="date" id="end_date" name="end_date" onchange="calculatePrice()" required>

            <p id="price_display">合計金額: ¥0</p>

            <button type="submit" class="button">レンタルを申し込む</button>
        </form>
    </main>
</body>
</html>
