<?php
session_start();

// ログイン状態確認
$loggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$isAdmin = $loggedIn && isset($_SESSION["role"]) && $_SESSION["role"] === 'admin';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カメラレンタル＆保管サービス</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>📷 カメラレンタル＆保管サービス</h1>
        <nav>
            <a href="index.php">ホーム</a>
            <a href="cameras.php">カメラ一覧</a>
            <a href="mypage.php">マイページ</a>

            <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <!-- ログイン中：ユーザーネームとログアウトボタン -->
                <span class="username">ようこそ、<?php echo htmlspecialchars($_SESSION["username"]); ?> さん</span>
                <a href="logout.php" class="button">ログアウト</a>
            <?php else: ?>
                <!-- 未ログイン時：ログインと新規会員登録リンク -->
                <a href="login.html">ログイン</a>
                <a href="register.html" class="button">新規会員登録</a>
            <?php endif; ?>
            <?php if ($isAdmin): ?>
            <!-- 管理者専用ボタン -->
            <a href="admin_camera_register.php" class="button">カメラ登録</a>
        <?php endif; ?>
        </nav>
    </header>
    
    <main>
        <!-- サービス概要 -->
        <section class="intro">
            <h2>サービス概要 🌟</h2>
            <p>当サービスでは、<strong>「カメラの保管」</strong>と<strong>「保管カメラのレンタル」</strong>の2つの使い方を提供しています。使っていないカメラを預けるだけで収入を得たり、最新のカメラを手軽に借りられる便利なサービスです。</p>
        </section>

        <!-- カメラを保管したい方向けのセクション -->
        <section class="for-owners">
            <h2>カメラを保管したい方へ 🛠️</h2>
            <p>使わないカメラを他のユーザーとシェアして収益を得ましょう！</p>
            <ul>
                <li>📥 <strong>保管申請</strong>：簡単なフォームでカメラを登録。</li>
                <li>💰 <strong>収益化</strong>：レンタルされるたびに収入が得られます。</li>
            </ul>
            <button id="storeCameraButton" class="button">カメラを保管する</button>
        </section>

        <!-- カメラを借りたい方向けのセクション -->
        <section class="for-renters">
            <h2>カメラをレンタルしたい方へ 🎒</h2>
            <p>最新機種や高性能カメラを、必要なときに気軽にレンタルできます！</p>
            <ul>
                <li>🔍 <strong>カメラ検索</strong>：豊富な機種からぴったりのカメラを探せます。</li>
                <li>✉️ <strong>レンタル申請</strong>：ワンクリックでレンタル申請完了。</li>
            </ul>
            <a href="cameras.php" class="button">カメラ一覧を見る</a>
        </section>
        
        <!-- サービスのメリット -->
        <section class="benefits">
            <h2>サービスのメリット 🎯</h2>
            <ul>
                <li>✅ カメラを有効活用し、収入も得られる！</li>
                <li>✅ 購入よりお得に、好きなカメラを利用可能！</li>
                <li>✅ 簡単な手続きで、すぐに使える！</li>
            </ul>
        </section>
    </main>

    <!-- JavaScript: カメラ保管ボタンの動作を制御 -->
    <script>
        document.getElementById("storeCameraButton").addEventListener("click", function() {
            const isLoggedIn = <?php echo isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true ? 'true' : 'false'; ?>;
            
            if (isLoggedIn) {
                window.location.href = "store_camera.html";
            } else {
                alert("カメラを保管するにはログインが必要です。");
                window.location.href = "login.html";
            }
        });
    </script>
</body>
</html>
