<?php
session_start();

// MySQL 데이터베이스 연결 정보
require '../includes/db_connect.php';

// 세션에서 사용자 ID 가져오기
$userID = $_SESSION['userID'];
$availableDate = "";
$deliveryDate = "";
$today = date('Y-m-d'); // 현재 날짜 가져오기
// 사용자가 주문한 작물 중 가장 긴 재배 기간 가져오기
$harvestPeriod = 0;
// order.php에서 하나의 작물만 주문하는 경우
if(isset($_POST['cropID'])) {
    $cropID = $_POST['cropID'];
    $stmt = mysqli_prepare($con, "SELECT harvsestPeriod FROM crop WHERE cropID = ?");
    mysqli_stmt_bind_param($stmt, "i", $cropID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $harvestPeriod = $row['harvsestPeriod'];
} 



// MainCart.php에서 여러 작물을 주문하는 경우
else if (isset($_POST['cropIDs']) && is_array($_POST['cropIDs'])) {
    $cropIDs = $_POST['cropIDs'];
    foreach ($cropIDs as $cropID){
        $stmt = mysqli_prepare($con, "SELECT harvsestPeriod FROM crop WHERE cropID = ?");
        mysqli_stmt_bind_param($stmt, "i", $cropID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $tempHarvestPeriod = $row['harvsestPeriod'];
        if ($tempHarvestPeriod > $harvestPeriod) {
            $harvestPeriod = $tempHarvestPeriod;
        }
    }
}

// 배송기간 설정
$deliveryPeriod = 3; // 예시로, 3일로 설정

// 현재 날짜에 재배기간 및 배송기간 더하기
$availableDate = date('Y-m-d', strtotime("+" . ($harvestPeriod + $deliveryPeriod) . " days"));

echo "<script>var deliveryDate = new Date('" . $availableDate . "');</script>";

// JavaScript 변수로 변환
// 결과를 JavaScript 변수로 변환


?>


<?php

$userID = $_SESSION['userID'];
$userQuery = "SELECT addressID, addressName, address, addressDetail, zipCode
FROM address
WHERE User_userID = ? ORDER BY addressID ";

$userStmt = $con->prepare($userQuery);
$userStmt->bind_param("s", $userID);
$userStmt->execute();
$userResult = $userStmt->get_result();

$addresses = array();
while ($row = $userResult->fetch_assoc()) {
    $addresses[] = $row;
}

$con->close();

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel="stylesheet" href="../style2.css">
    <link rel="stylesheet" href="../style3.css">
</head>
<body>
    <header>
        <?php include '../includes/userNavbar.php'; ?>
    </header>
<h2 class="mainttle">배송받을 날짜 선택</h2>
<div class="date-wrap">
  

      <div class="button_wrap">
        <button type="button" id="month-prev" class="month-move" data-ym="2022-04-01"> &lt; </button>
        <span id="month-this"></span>
        <button type="button" id="month-next" class="month-move" data-ym="2022-06-01"> &gt; </button>
      </div>

 
    <table id="tbl-month" class="date-month">
      <thead>
        <tr>
          <th>sun</th>
          <th>mon</th>
          <th>tue</th>
          <th>wed</th>
          <th>thu</th>
          <th>fri</th>
          <th>sat</th>
        </tr>
      </thead>

    </table>
</div>
<form action="selectFarm.php">
  <div class="button_wrap">
    <button id="orderButton">다음</button>
  </div>
</form>
<h2 style='margin-left:30px'>배송지</h2>
<div class="delivery-info">
    <select id="address-select">
        <?php foreach ($addresses as $address): ?>
            <option value="<?php echo htmlspecialchars(json_encode($address)); ?>">
                <?php echo htmlspecialchars($address['addressName']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="address"></label>
    <input type="text" id="address" name="address" placeholder="주소를 입력하세요">

    <label for="detail-address"></label>
    <input type="text" id="detail-address" name="detail-address" placeholder="상세주소를 입력하세요">
    
    <label for="zipCode"></label>
    <input type="text" id="zipCode" name="zipCode" placeholder="상세주소를 입력하세요">
</div>




</body>
</html>
<script src="../script4.js">


</script>