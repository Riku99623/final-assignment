document.addEventListener("DOMContentLoaded", () => {
    loadCameras();
    setupLoginStatus();
});

/* カメラのデータを取得してリストに表示 */
async function loadCameras() {
    try {
        const response = await fetch('/api/get_cameras.php');
        if (!response.ok) throw new Error("データの取得に失敗しました");

        const cameras = await response.json();
        const cameraList = document.querySelector("#camera-list");
        cameraList.innerHTML = cameras.map(camera => `
            <li class="camera-card">
                <h3>${camera.name}</h3>
                <img src="${camera.image_url}" alt="${camera.name}">
                <p>${camera.description}</p>
                <a href="camera_detail.html?id=${camera.id}" class="button">詳細を見る</a>
            </li>
        `).join('');
    } catch (error) {
        console.error(error);
        alert("カメラ一覧の読み込み中にエラーが発生しました。");
    }
}

/* レンタルリクエストを送信 */
async function submitRentalRequest(cameraId, rentalDays) {
    try {
        const response = await fetch('/api/rental_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ cameraId, rentalDays })
        });
        
        if (!response.ok) throw new Error("リクエストの送信に失敗しました");

        const result = await response.json();
        if (result.success) {
            alert("レンタルリクエストが正常に送信されました。");
        } else {
            alert("レンタルリクエストが失敗しました。もう一度お試しください。");
        }
    } catch (error) {
        console.error(error);
        alert("リクエスト送信中にエラーが発生しました。");
    }
}

/* ユーザーのログイン状態を確認し、ページを更新 */
function setupLoginStatus() {
    const user = JSON.parse(localStorage.getItem("user"));
    const loginLink = document.querySelector("#login-link");

    if (user && user.isLoggedIn) {
        loginLink.textContent = `${user.name}でログイン中`;
        loginLink.href = "#";
    } else {
        loginLink.textContent = "ログイン";
        loginLink.href = "login.html";
    }
}

/* ログインフォーム送信イベントのセットアップ */
const loginForm = document.querySelector("#login-form");
if (loginForm) {
    loginForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        
        const username = document.querySelector("#username").value;
        const password = document.querySelector("#password").value;

        try {
            const response = await fetch('/api/login_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });

            const result = await response.json();
            if (result.success) {
                localStorage.setItem("user", JSON.stringify({ name: username, isLoggedIn: true }));
                alert("ログインに成功しました。");
                window.location.href = "index.html";
            } else {
                alert("ログインに失敗しました。ユーザー名またはパスワードを確認してください。");
            }
        } catch (error) {
            console.error(error);
            alert("ログイン処理中にエラーが発生しました。");
        }
    });
}
