<?php
// 세션 시작
session_start();

// 로그인을 아직 구현하지 않아서 임시로 사용자 이름을 세션에 할당해야한다.
// 실제 사이트에서는 로그인 후에 이 값을 설정해야 한다.

// 사용자가 로그인했다고 가정하고, 세션에서 사용자 이름을 가져옵니다.
$userName = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'user1';
?>

<div class="navbar">
    <div class="logo">
    <a onclick="location.href='./farmerMain.php'"><img src="../assets/images/logo.png" alt="FarmLink 로고"/></a>
    </div>
    <ul class="menu">
        <li><a href="http://localhost/FarmLink/pages/farmerMain.php">농장관리</a></li>
        <li><a href="http://localhost/FarmLink/pages/farmOrderInfo.php">주문관리</a></li>
        <li><a href="http://localhost/FarmLink/pages/farmRemind.php">알림모음</a></li>
        <li><a href="#account">내 정보</a></li>
    </ul>
    <div class="user-info">
        <a href="#account" onclick="location.href='./login.php'">로그아웃</a>
    </div>
</div>

<script>
    // PHP에서 가져온 사용자 이름을 JavaScript 변수에 할당합니다.
    var currentUserName = <?php echo json_encode($userName); ?>;
</script>