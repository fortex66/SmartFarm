<!--  update_quantitiy.php 파일  -->   

<?php
// 세션 시작
session_start();

// 클라이언트에서 전송한 수량 정보를 받습니다.
if (isset($_POST['currentQuantity'])) {
    $currentQuantity = $_POST['currentQuantity'];

    // 세션에 수량을 저장합니다.
    $_SESSION['currentQuantity'] = $currentQuantity;

    // 성공적으로 수량을 업데이트했다는 응답을 보냅니다.
    echo "Success: Quantity updated to $currentQuantity";
} else {
    // 수량 정보가 전송되지 않았다는 오류 메시지를 보냅니다.
    echo "Error: No quantity data received";
}
?>