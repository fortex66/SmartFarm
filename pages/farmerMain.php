<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel=stylesheet href='../style.css' >
</head>
<body>
    <header>
        <?php include '../includes/farmerNavbar.php'; ?>
    </header>
    <div class="Maintitle">
        <h1> 알림 </h1>
    </div>
    <div id="alerts">
        <?php
            // 데이터 베이스에 연결하는 파일을 require 합니다. (중복을 피하고 재사용을 위해서)
            require '../includes/db_connect.php';

            $userName = $_SESSION['userID'];

            // user 테이블에서 userID를 검색
            $userIDQuery = "SELECT userID FROM user WHERE userID = ?";
            $stmt = $con->prepare($userIDQuery);
            $stmt->bind_param("s", $userName);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                echo "일치하는 사용자가 없습니다.";
            }
            $user = $result->fetch_assoc();
            

            /* userID를 사용하여 해당 유저의 농장에 대한 주문 정보를 조회합니다. */
            if ($user) {
                    $userID = $user['userID'];

                    // userID를 사용하여 해당 유저의 농장에 대한 관리 장비 정보를 조회하는 SQL 쿼리문
                    $managementQuery = "SELECT f.farmID, p.potID, m.deviceName, m.deviceStatus, m.timestamp
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
                            $statusClass = $management['deviceStatus'] == 0 ? 'status-failure' : 'status-normal';
                            
                            // 알림 출력 부분 (고장난 장비 알림)
                            echo '<p>' . $timestamp->format('m-d H:i') . '&nbsp;&nbsp;';
                            echo $management['farmID'] . "번 농장 &nbsp;&nbsp;";
                            echo $management['potID'] . "번 화분 &nbsp;&nbsp;";
                            echo $management['deviceName'] . '&nbsp;&nbsp;';
                            echo '<span class="' . $statusClass . '">' . ($management['deviceStatus'] == 0 ? '고장' : '정상') . '</span></p>';
                            
                        }
                    } else {
                        echo '<p>' . "문제가 있는 장비가 없습니다." . '</p>';
                    }
                    
                    $managementStmt->close();
            
                } else {
                    echo "SQL 문에 오류가 있습니다: " . $con->error;
                }
        
                    
                // userID를 사용하여 해당 유저의 농장에 들어온 주문 정보를 조회하는 SQL 쿼리문
                $ordersQuery = " SELECT 
                    o.orderDate, 
                    c.cropName, 
                    SUM(od.amount) AS amount  -- 집계 함수 사용
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
                        $orderDate = new DateTime($order['orderDate']); // 시간의 출력 형태를 바꿈
                        echo $orderDate->format('m-d H:i') ."&nbsp&nbsp&nbsp&nbsp";
                        echo $order['cropName'] . "&nbsp&nbsp&nbsp&nbsp";
                        echo $order['amount'] . " Kg 주문 입고<br>";
                    }
        
                    $ordersStmt->close();

                } else {
                    echo "SQL 문에 오류가 있습니다: " . $con->error;
                }


            } else {
                echo "해당 사용자의 농장 정보를 찾을 수 없습니다.";
            }

            
        ?>
    </div>

    <div class="Maintitle">
        <h1> 농장 관리 </h1>
    </div>

    <div class="farm-management">
        <div class="farm-list">
            <?php

                // 농장 정보 조회 쿼리
                $farmQuery = "SELECT farmID, address, addressDetail FROM farm WHERE User_userID = ?";
                $farmStmt = $con->prepare($farmQuery);
                $farmStmt->bind_param("s", $userID); // 위에서 사용하였던 userID를 사용한다.
                $farmStmt->execute();
                $farmResult = $farmStmt->get_result();
                $firstItem = true; // 첫 번째 항목인지 식별하는 플래그

                if ($farmResult->num_rows > 0) {
                    // 결과가 있는 경우, 각 농장 정보를 목록으로 표시
                    while ($farm = $farmResult->fetch_assoc()) {
                        $style = $firstItem ? ' style="background-color: #A0522D;"' : ''; // 첫 번째 항목에만 스타일 적용
                        echo '<div class="farm-item" data-id="' . $farm['farmID'] . '" onclick="showPots(' . $farm['farmID'] . ')">';
                        echo '<h2>'. $farm['farmID'] . '번 농장</h2>';
                        echo '<p>' . $farm['address'] ;
                        echo $farm['addressDetail'] . '</p>';
                        echo '</div>';
                        $firstItem = false; // 첫 번째 항목 후에는 false로 설정
                    }
                } else {
                    echo '소유한 농장이 없습니다.';
                }

                // 쿼리 종료
                $farmStmt->close();

            ?>
        </div>

        <div class="pot">
            <div id="pot-gallery">

            </div>
        </div>
    </div>
    <div>
        <?php 
            // 데이터베이스 연결 종료 및 쿼리 종료
            $stmt->close();
            $con->close();
        ?>
    </div>


    <footer>
        <!-- 푸터 콘텐츠 -->
    </footer>
    
   
    <script >
        function showPots(farmID) {
            // 선택된 farm-item의 색상 변경
            // 선택된 농장의 정보를 가져오기
            const selectedFarmItem = document.querySelector(`.farm-item[data-id='${farmID}']`);
            const farmName = selectedFarmItem.querySelector('h2').textContent;
            const farmAddress = selectedFarmItem.querySelector('p').textContent;

            // 세션 스토리지에 농장 이름과 주소 저장
            sessionStorage.setItem('selectedFarmName', farmName);
            sessionStorage.setItem('selectedFarmAddress', farmAddress);

            const farmItems = document.querySelectorAll('.farm-item');

            farmItems.forEach(item => {
                if (item === selectedFarmItem) {
                    item.style.backgroundColor = '#A0522D';  // 선택된 항목의 색상을 갈색으로 변경
                    item.style.color = 'white';
                    item.style.borderRadius = '20px';
                } else {
                    item.style.color = 'initial';  // 다른 항목은 기본 색상으로 변경
                    item.style.backgroundColor = 'initial';
                }
            });

            // AJAX 요청을 통해 서버에 farmID 전달 및 데이터 요청
            fetch('http://localhost/FarmLink/includes/ajax_fmain.php', {
                method: 'POST',
                body: JSON.stringify({ farmID: farmID }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const pots = data.pots; 
                let failedPots = data.failedPots;
                

                // 객체를 배열로 변환 (필요한 경우)
                if (!Array.isArray(failedPots)) {
                    failedPots = Object.values(failedPots);
                }
                console.log("Received data:", data); // 서버로부터 받은 데이터 확인
                updatePotGallery(pots, failedPots); // 데이터를 사용하여 pot-gallery 업데이트
            })
            
            .catch(error => console.error('Error:', error));
        }


        function updatePotGallery(pots, failedPots) {
            const gallery = document.getElementById('pot-gallery');
            gallery.innerHTML = ''; // 기존 내용을 비우고 새로운 내용으로 채움
            pots.forEach(pot => {
                console.log(`Pot ID: ${pot.potID}, Failed Pots: ${failedPots}`);
                const img = document.createElement('img');
                img.setAttribute('data-pot-id', pot.potID);
                img.className = 'pot-item';
                img.src = 'http://localhost/FarmLink/assets/images/plants.png';
                img.alt = 'Pot Image';
                img.style.width = '150px'; 
                img.style.padding = '5px'; 
                img.style.margin = '45px';

                // 고장난 장비가 있는 경우, 테두리 색상을 빨간색으로 설정
                if (failedPots.includes(Number(pot.potID))) {
                    img.style.border = '5px solid red';
                } else {
                    img.style.border = '1px solid #ddd'; 
                }

                gallery.appendChild(img);
                
            });
            addPotClickEvents(); // 화분 갤러리 업데이트 후 클릭 이벤트 추가
        }

        document.addEventListener('DOMContentLoaded', function() {
            showPots(1); // 페이지 로딩 완료 후 첫 번째 농장의 화분을 보여주고, 첫 번째 농장 항목의 색상을 갈색으로 변경
        });


        // 화분 클릭 이벤트 추가
        function addPotClickEvents() {
            const pots = document.querySelectorAll('.pot-item'); // .pot-item은 각 화분을 나타내는 클래스
            pots.forEach(pot => {
                pot.addEventListener('click', function() {
                    const potID = this.getAttribute('data-pot-id');
                    sessionStorage.setItem('selectedPotID', potID);
                    window.location.href = 'http://localhost/FarmLink/pages/farmDetail.php';
                    
                });
            });
        }

    </script>
</body>
</html>
