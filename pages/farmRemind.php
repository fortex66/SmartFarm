<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <?php include '../includes/farmerNavbar.php'; ?>
    </header>
    <div class="Maintitle">
        <h1>알림</h1>
    </div>
    <div id="alerts-to-do">
        <?php 
            require '../includes/db_connect.php';

            $userName = $_SESSION['userID']; // 임시 사용자 이름

            $userIDQuery = "SELECT userID FROM user WHERE userID = ?";
            $stmt = $con->prepare($userIDQuery);
            $stmt->bind_param("s", $userName);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                echo "일치하는 사용자가 없습니다.";
            } else {
                $user = $result->fetch_assoc();
            }

            // 장비 관리 정보 출력을 위한 함수
            function displayManagement($userID, $con, $mode) {
                $managementQuery = "
                    SELECT f.farmID, p.potID, m.deviceName, m.deviceStatus, m.timestamp
                    FROM `farm` f
                    JOIN `pot` p ON f.farmID = p.Farm_farmID
                    JOIN `management` m ON p.potID = m.Pot_potID
                    WHERE f.User_userID = ? AND m.deviceStatus = 0
                    ORDER BY m.timestamp DESC
                ";

                if ($managementStmt = $con->prepare($managementQuery)) {
                    $managementStmt->bind_param("s", $userID);
                    $managementStmt->execute();
                    $managementResult = $managementStmt->get_result();

                    if ($managementResult->num_rows > 0) {
                        while ($management = $managementResult->fetch_assoc()) {
                            $timestamp = new DateTime($management['timestamp']);
                            
                            echo '<p class="alarm-eqip">' . $timestamp->format('m-d H:i') . '&nbsp&nbsp;';
                            echo $management['farmID'] . "번 농장 &nbsp&nbsp&nbsp";
                            echo $management['potID'] . "번 화분 &nbsp&nbsp&nbsp";
                            echo $management['deviceName'] . '&nbsp;&nbsp;';
                            if ($mode == "alarm" && $management['deviceStatus'] == 0) {
                                echo '<span class="status-failure">고장</span>';
                            } else if ($mode == "to do" && $management['deviceStatus'] == 0) {
                                    echo '<span class="status-failure">점검 필요</span></p>';
                            } 
                            
                        }
                    } else {
                        echo '<p>' . "문제가 있는 장비가 없습니다." . '</p>';
                    }
                    $managementStmt->close();
                } else {
                    echo "SQL 문에 오류가 있습니다: " . $con->error;
                }
            }

            // 주문 정보 출력을 위한 함수
            function displayOrders($userID, $con, $mode) {
                $ordersQuery = "
                    SELECT 
                        o.orderDate, 
                        c.cropName, 
                        SUM(od.amount) AS amount
                    FROM 
                        `order` o
                        INNER JOIN `orderdetail` od ON o.orderID = od.Order_orderID
                        INNER JOIN `crop` c ON od.Crop_cropID = c.cropID
                        INNER JOIN `pot_has_order` ohp ON o.orderID = ohp.Order_orderID
                        INNER JOIN `pot` p ON ohp.Pot_potID = p.potID AND p.Crop_cropID = c.cropID
                        INNER JOIN `farm` f ON p.Farm_farmID = f.farmID AND f.User_userID = ?
                    WHERE 
                        o.orderState = 1
                    GROUP BY 
                        o.orderDate, c.cropName  
                    ORDER BY 
                        o.orderDate DESC;
                ";

                if ($ordersStmt = $con->prepare($ordersQuery)) {
                    $ordersStmt->bind_param("s", $userID);
                    $ordersStmt->execute();
                    $ordersResult = $ordersStmt->get_result();

                    while ($order = $ordersResult->fetch_assoc()) {
                        $orderDate = new DateTime($order['orderDate']);
                        echo '<p class="order-alarm">' . $orderDate->format('m-d H:i') ."&nbsp&nbsp&nbsp&nbsp";
                        echo $order['cropName'] . "&nbsp&nbsp&nbsp&nbsp";
                        if ($mode == "alarm") {
                            echo $order['amount'] . " Kg 주문 입고</p>";
                        } else if ($mode == "to do") {
                            // "파종" 텍스트에 초록색 스타일 적용
                            echo '<span class="status-planting">' . $order['amount'] . " Kg 파종</span></p>";
                        }
                        
                    }
                    $ordersStmt->close();
                } else {
                    echo "SQL 문에 오류가 있습니다: " . $con->error;
                }
            }

            // 알림 섹션에서 정보 출력
            if ($user) {
                $userID = $user['userID'];
                displayManagement($userID, $con, "alarm");
                displayOrders($userID, $con, "alarm");
            }
        ?>
    </div>

    <div class="Maintitle">
        <h1>할일</h1>
    </div>
    <div id="alerts-to-do">
        <?php 
            // 할일 섹션에서 정보 출력
            if ($user) {
                displayManagement($userID, $con,"to do");
                displayOrders($userID, $con,"to do");
            }
        ?>
    </div>

    <div>
        <?php 
            $stmt->close();
            $con->close();
        ?>
    </div>

    <footer>
        <!-- 푸터 정보 -->
    </footer>
</body>
</html>
