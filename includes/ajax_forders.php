<?php
require '../includes/db_connect.php';

// AJAX 요청으로부터 받은 데이터
$userName = $_POST['userName'];
$tab = $_POST['tab'];

// 들어온 주문에 대한 쿼리 정의
$queryAll = "
    SELECT 
        o.orderID,
        o.orderState,
        o.orderDate,
        o.User_userID,
        GROUP_CONCAT(DISTINCT f.farmID) AS 'FarmIDs',
        GROUP_CONCAT(DISTINCT p.potID) AS 'PotIDs',
        GROUP_CONCAT(DISTINCT CONCAT(c.cropName, ': ', od.amount)) AS 'CropNamesAndAmounts',
        SUM(c.price * od.amount) AS 'TotalPrice'
    FROM 
        `mydb`.`order` o
        JOIN `mydb`.`pot_has_order` ohp ON o.orderID = ohp.Order_orderID
        JOIN `mydb`.`pot` p ON ohp.Pot_potID = p.potID
        JOIN `mydb`.`farm` f ON p.Farm_farmID = f.farmID
        JOIN `mydb`.`crop` c ON p.Crop_cropID = c.cropID
        JOIN `mydb`.`orderdetail` od ON o.orderID = od.Order_orderID AND c.cropID = od.Crop_cropID
    WHERE 
        f.User_userID = ? AND o.orderState IN (0, 1)
    GROUP BY 
        o.orderID
    ORDER BY 
        o.orderDate DESC;

";
// 완료된 주문에 대한 쿼리 정의
$queryCompleted = "
    SELECT 
        o.orderID,
        o.orderState,
        o.orderDate,
        o.User_userID,
        GROUP_CONCAT(DISTINCT f.farmID) AS 'FarmIDs',
        GROUP_CONCAT(DISTINCT p.potID) AS 'PotIDs',
        GROUP_CONCAT(DISTINCT CONCAT(c.cropName, ': ', od.amount)) AS 'CropNamesAndAmounts',
        SUM(c.price * od.amount) AS 'TotalPrice'
    FROM 
        `mydb`.`order` o
        JOIN `mydb`.`pot_has_order` ohp ON o.orderID = ohp.Order_orderID
        JOIN `mydb`.`pot` p ON ohp.Pot_potID = p.potID
        JOIN `mydb`.`farm` f ON p.Farm_farmID = f.farmID
        JOIN `mydb`.`crop` c ON p.Crop_cropID = c.cropID
        JOIN `mydb`.`orderdetail` od ON o.orderID = od.Order_orderID AND c.cropID = od.Crop_cropID
    WHERE 
        f.User_userID = ? AND o.orderState NOT IN (0, 1)
    GROUP BY 
        o.orderID
    ORDER BY 
        o.orderDate DESC;
";

// 쿼리 선택
if ($tab === 'all') {
    $query = $queryAll;
} elseif ($tab === 'completed') {
    $query = $queryCompleted;
} else {
    echo json_encode(['error' => 'Unknown tab']);
    exit;
}

// 쿼리 실행
if ($stmt = $con->prepare($query)) {
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $result = $stmt->get_result();

    // 데이터를 HTML 형식으로 변환
    $output = '';// 주문 헤더 출력
    echo "<div class='order-header'>";
        echo "<div>주문번호</div>";
        echo "<div>날짜</div>";
        echo "<div>시간</div>";
        echo "<div>주문자명</div>";
        echo "<div>농장번호</div>";
        echo "<div>화분번호</div>";
        echo "<div>작물명 및 수량</div>";
        echo "<div>총 가격</div>";
        echo "<div>주문상태</div>";
    echo "</div>";
    
    // 주문 항목 출력
    while ($row = $result->fetch_assoc()) {
        // 날짜와 시간을 분리
        $dateTime = new DateTime($row['orderDate']);
        $date = $dateTime->format('Y-m-d'); // 날짜 포맷
        $time = $dateTime->format('H:i:s'); // 시간 포맷
    
        echo "<div class='order-item'>";
        echo "<div>" . htmlspecialchars($row['orderID']) . "</div>";
        echo "<div>" . htmlspecialchars($date) . "</div>"; // 날짜
        echo "<div>" . htmlspecialchars($time) . "</div>"; // 시간
        echo "<div>" . htmlspecialchars($row['User_userID']) . "</div>";
        echo "<div>" . htmlspecialchars($row['FarmIDs']) . "</div>"; // 농장번호
        echo "<div>" . htmlspecialchars($row['PotIDs']) . "</div>"; // 화분번호
        echo "<div>" . htmlspecialchars($row['CropNamesAndAmounts']) . "</div>"; // 작물명 및 수량
        echo "<div>" . htmlspecialchars($row['TotalPrice']) . "</div>"; // 총 가격
        if ($row['orderState'] == '1') {
            echo "<div onclick='changeOrderState(" . htmlspecialchars($row['orderID']) . ", 0)' style='color: red; cursor: pointer;' >취소하기</div>";
        } else if ($row['orderState'] == '2'){
            echo "<div style='color: green;'>출고됨</div>";
        } else {
            echo "<div>취소됨</div>";
        }
        echo "</div>";
    }
    

    echo $output;
    $stmt->close();
} else {
    echo "쿼리 실행에 실패했습니다: " . $con->error;
}

$con->close();


?>