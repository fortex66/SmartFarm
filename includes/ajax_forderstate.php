<?php

require '../includes/db_connect.php';

$orderID = $_POST['orderID'];

$con->begin_transaction(); // 트랜잭션 시작

try {
    // orderState를 0으로 업데이트
    $stmt = $con->prepare("UPDATE `mydb`.`order` SET orderState = 0 WHERE orderID = ?");
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->close();

    // pot 테이블 업데이트
    $stmt = $con->prepare("UPDATE `mydb`.`pot` SET Crop_cropID = NULL WHERE potID IN (SELECT Pot_potID FROM `mydb`.`pot_has_order` WHERE Order_orderID = ?)");
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->close();

    // order_has_pot 테이블에서 삭제
    $stmt = $con->prepare("DELETE FROM `mydb`.`pot_has_order` WHERE Order_orderID = ?");
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->close();

    // orderdetail 테이블에서 삭제
    $stmt = $con->prepare("DELETE FROM `mydb`.`orderdetail` WHERE Order_orderID = ?");
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->close();

    $con->commit(); // 트랜잭션 커밋
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $con->rollback(); // 오류 발생 시 롤백
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$con->close();

?>