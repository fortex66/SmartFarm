<!-- 사용하는 SQL문 Line26: $delete_cart_sql = "DELETE FROM `mydb`.`cart` WHERE `User_userID` = '$user_id' AND `Crop_cropID` = $crop_id;";  -->    


<?php
session_start();

$user_id =$_SESSION['userID']; 
$crop_id = $_POST['deleteButton']; // 삭제할 작물 ID

require '../includes/db_connect.php';

// 작물을 장바구니에서 삭제하는 SQL 쿼리
$delete_cart_sql = "DELETE FROM `mydb`.`cart` WHERE `User_userID` = '$user_id' AND `Crop_cropID` = $crop_id;";
$con->query($delete_cart_sql);

// 삭제 후 세션 초기화
unset($_SESSION['currentQuantity']);
unset($_SESSION['productCheckbox']);

$con->close();

// 삭제 후 장바구니 페이지로 리다이렉트
header("Location: MainCart.php");  // 이 부분은 실제 장바구니 페이지의 파일명으로 변경해주세요.
exit();
?>
