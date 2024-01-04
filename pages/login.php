<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('../assets/images/enter2.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: rgb(230 230 230 / 90%);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 550px;
            height: 350px;
            text-align: center;
        }


        .login-form h2 {
            margin-bottom: 40px;
            color : green;
            font-size : 30px;
        }

        .input-group {
            margin-bottom: 35px;
            display:flex;
            justify-content: space-between;
            align-items: center;
        }

        .input-group input {
            width: 80%;
            padding: 10px;
            border: none;
         
            border-radius: 4px;
            box-sizing: border-box; /* 입력란의 패딩이 너비에 포함되도록 */

        }

        .input-group button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            
        }

        .input-group button:hover {
            background-color: #45a049;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 0.9em;
        }

        a:hover {
            text-decoration: underline;
        }


    </style>
</head>
<body>
    <div class="login-container">
        <form action="login.php" method="post" class="login-form">
            <h2>FarmLink</h2>
            <div class="input-group">
                <label for="userID">아이디</label>
                <input type="text" id="userID" name="userID" required>
            </div>
            <div class="input-group">
                <label for="pw">비밀번호</label>
                <input type="password" id="pw" name="pw" required>
            </div>
            <div class="input-group">
                <button type="submit">로그인</button>
            </div>
            <a href="#">비밀번호 찾기</a>
        </form>
    </div>
</body>
</html>

<?php
// Start session
session_start();

// 데이터베이스 연결을 위한 파일을 include 합니다.
require '../includes/db_connect.php'; 

// post 방식
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST['userID'];
    $password = $_POST['pw'];

    // 유저가 존재하는지 확인하기
    $query = "SELECT * FROM user WHERE userID = ? LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // 해시를 사용하여 저장되어 있는 데이터베이스의 비밀번호와 비교
    if ($user && hash_equals($user['pw'], hash('sha256', $password))) {
        // 유저가 가진 농장
        $query = "SELECT * FROM farm WHERE User_userID = ? LIMIT 1";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // 유저가 농장을 가지고 있다면, farmerMain.php으로 이동
            $_SESSION['userID'] = $userID;
            header("Location: farmerMain.php");
            exit();
        } else {
            // 유저가 농장이 없다면, userMain.php으로 이동
            $_SESSION['userID'] = $userID;
            header("Location: userMain.php");
            exit();
        }
    } else {
        // Login 실패시
        echo "Invalid userID or password";
    }
}
?>