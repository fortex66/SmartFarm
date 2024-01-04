<!--  Cartdb.php 파일  -->   


<!--  User_id가져오기  -->    
<!--  user의 cart 정보 + crop 정보를 가져오는 SQL(Line30) $sql = "SELECT c.*, cart.quantity
        FROM mydb.cart AS cart
        INNER JOIN mydb.crop AS c ON cart.Crop_cropID = c.cropID
        WHERE cart.User_userID = '$user_id'";
 sql문:  

// Cart 테이블 업데이트
$update_cart_sql = "UPDATE `mydb`.`cart` SET `quantity` = $currentQuantity WHERE `User_userID` = '$user_id';";

-->  

<?php
// 세션 시작
session_start();

require '../includes/db_connect.php';

// 사용자 ID 가져오기
$user_id = $_SESSION['userID'];


// 사용자의 장바구니 정보와 작물 정보를 가져오는 쿼리
$sql = "SELECT c.*, cart.quantity
        FROM mydb.cart AS cart
        INNER JOIN mydb.crop AS c ON cart.Crop_cropID = c.cropID
        WHERE cart.User_userID = '$user_id'";

$result = $con->query($sql);



$crop_id = isset($crop_id) ? $crop_id : ''; // $crop_id 변수 초기화
$crops = isset($crops) ? $crops : array();  // $crops 변수 초기화


// 삭제 버튼이 클릭되었는지 확인
if (isset($_POST['deleteButton'])) {
    $delete_crop_id = $_POST['deleteButton'];

    // $delete_crop_id가 정수인지 확인하여 SQL 인젝션 방지
    $delete_crop_id = (int)$delete_crop_id;

    if ($delete_crop_id > 0) {
        // SQL 인젝션 방지를 위해 prepared statement 사용
        $delete_cart_sql = "DELETE FROM `mydb`.`cart` WHERE `User_userID` = ? AND `Crop_cropID` = ?";
        $stmt = $con->prepare($delete_cart_sql);
        $stmt->bind_param("si", $user_id, $delete_crop_id);
        $stmt->execute();
        $stmt->close();

        // 삭제 후 리디렉션
        header("Location: MainCart.php");
        exit();
    } else {
        echo "잘못된 작물 ID";
    }
}


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $crops[] = $row;
    }
} else {
    echo "$crop_id";
}




// 현재 주문 수량 설정 또는 업데이트
$currentQuantity = isset($_POST['currentQuantity']) ? $_POST['currentQuantity'] : (isset($_SESSION['currentQuantity']) ? $_SESSION['currentQuantity'] : array());
if (!is_array($currentQuantity)) {
    $currentQuantity = array();
}

// 버튼 증감 처리 + -
foreach ($crops as $crop) {
    // 수량이 설정되지 않은 경우에는 초기값 0을 설정
    if (!isset($currentQuantity[$crop['cropID']])) {
        $currentQuantity[$crop['cropID']] = 1;
    }

    // decreaseButton 처리
    if (isset($_POST["decreaseButton".$crop['cropID']]) && $currentQuantity[$crop['cropID']] > 0) {
        $currentQuantity[$crop['cropID']]--;
    } 
    // increaseButton 처리
    elseif (isset($_POST['increaseButton'.$crop['cropID']])) {
        $currentQuantity[$crop['cropID']]++;
    }
}

// Cart 테이블 업데이트
foreach ($currentQuantity as $cropID => $quantity) {
    $update_cart_sql = "UPDATE `mydb`.`cart` SET `quantity` = $quantity WHERE `User_userID` = '$user_id' AND `Crop_cropID` = $cropID;";
    $con->query($update_cart_sql);
}

// 세션에 현재 수량 배열을 저장합니다.
$_SESSION['currentQuantity'] = $currentQuantity;

// 총 주문 금액 초기화
$totalPrice = 0;

foreach ($crops as $crop) {
    if (isset($_POST['productCheckbox' . $crop['cropID']]) && $_POST['productCheckbox' . $crop['cropID']] == 'on') {
        $totalPrice += $crop["price"] * $currentQuantity[$crop['cropID']];
    }
}


$con->close();
?>

