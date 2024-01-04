<?php
// 클릭된 potID로 db에서 장비의 상태와 장비명, 작물명, 작물 성장 주기, 작물 크기를 조회한다.

// HTTP 요청 메소드 확인
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method Not Allowed
    die('Invalid request method');
}

// 데이터베이스 연결
require '../includes/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['potID'])) {
    http_response_code(400); // Bad Request
    die('Invalid potID');
}

$potID = $data['potID'];

// 첫 번째 쿼리 실행: 장비 상태와 장비명 조회
$query1 = "SELECT m.deviceStatus, m.deviceName 
           FROM mydb.management m 
           WHERE m.Pot_potID = ?";
$stmt1 = $con->prepare($query1);
$stmt1->bind_param("i", $potID);
$stmt1->execute();
$result1 = $stmt1->get_result();

$equipmentData = [];
while ($row = $result1->fetch_assoc()) {
    array_push($equipmentData, $row);
}


// 두 번째 쿼리 실행: 작물명, 작물 성장 주기, 작물 크기 조회
$query2 = "SELECT 
    p.size AS potSize,
    c.cropName,
    c.harvsestPeriod AS harvestPeriod,
    c.size AS cropSize,
    MAX(o.orderDate) AS latestOrderDate
    FROM 
    pot p
    JOIN crop c ON p.Crop_cropID = c.cropID
    JOIN pot_has_order ohp ON p.potID = ohp.Pot_potID
    JOIN `order` o ON ohp.Order_orderID = o.orderID
    WHERE 
    p.potID = ?
    GROUP BY 
    p.potID, c.cropID;
";
$stmt2 = $con->prepare($query2);
$stmt2->bind_param("i", $potID);
$stmt2->execute();
$result2 = $stmt2->get_result();

$cropData = [];
while ($row = $result2->fetch_assoc()) {
    array_push($cropData, $row);
}

// 모든 데이터를 한 JSON 객체로 인코딩하여 반환
$response = [
    'equipment' => $equipmentData,
    'crop' => $cropData
];

echo json_encode($response);

$stmt1->close();
$stmt2->close();
$con->close();
?>
