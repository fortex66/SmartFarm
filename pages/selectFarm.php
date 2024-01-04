<html lang="kr">
<head>
    <meta charset="utf-8">
    <title>FarmLink-selectFarm</title>
    <link rel="stylesheet" href="../selectFarm.css">
    <link rel="stylesheet" href="../style2.css">
</head>
<body>
<header>
    <?php include '../includes/userNavbar.php'; ?>
</header>

<p class="farmlist" style="width: 100%; color: black; font-size: 40px; font-family: Inter; font-weight: 400; word-wrap: break-word; padding-top: 50px; margin-left: 30px; margin-bottom : 30px;">농장 목록</p>
<div class="farmlist_fromdb" style='background-color:#d7d7d7; border-radius : 10px;'>
        
    <?php
        // 데이터베이스 연결 정보
        require '../includes/db_connect.php';

        // 농장 번호와 주소를 가져오는 쿼리
        $sql = "SELECT farmID, address, addressDetail FROM farm";
        $result = $con->query($sql);

        // 결과 출력
        if ($result->num_rows > 0) {
            echo "<div style='display: flex; justify-content: space-around; margin-bottom : 30px; '>";
            $count = 0;
            while ($row = $result->fetch_assoc()) {
                echo "<div style='width: 30%; text-align: center;  border-radius:20px; cursor:pointer;' onclick='saveFarmInfoToSessionStorage(" . $row["farmID"] . ", \"" . $row["address"] . "\", \"" . $row["addressDetail"] . "\", this)'>";
                echo "<p style='width: 100%; color: black; font-size: 30px; font-family: Inter; font-weight: 400; word-wrap: break-word; margin-top: 10px;'>" . $row["farmID"] . "번 농장<br></p>";
                echo "<p style='color: black; font-size: 20px; font-family: Inter; font-weight: 400; word-wrap: break-word;'>주소: " . $row["address"] . "<br></p>";
                echo "<p style='color: black; font-size: 20px; font-family: Inter; font-weight: 400; word-wrap: break-word;'>상세주소: " . $row["addressDetail"] . "<br></p>";
                echo "</div>";
                $count++;
                if ($count % 4 == 0) {
                    echo "</div><div style='display: flex; justify-content: space-around;margin-bottom : 30px;'>";
                }
            }
            echo "</div>";
        } else {
            echo "농장이 없습니다.";
        }

        // 연결 종료
        $con->close();
    ?>
    <form action="payment.php">
        <div class="button_wrap">
            <button id="orderButton">다음</button>
        </div>
    </form>
</div>

</body>
<script>
    function saveFarmInfoToSessionStorage(farmID, address, addressDetail, element) {
        // 이전에 선택된 농장의 스타일 초기화
        var previouslySelected = document.getElementsByClassName('selectedFarm');
        var previouslySelectedArray = Array.from(previouslySelected); // HTMLCollection을 배열로 변환
        previouslySelectedArray.forEach(function(prevElement) {
            if(prevElement) { // 요소가 undefined가 아닌 경우에만 스타일 변경
                prevElement.classList.remove('selectedFarm');
                prevElement.style.backgroundColor = '#d7d7d7'; // 원래 배경색으로 변경
            }
        });

        // 선택된 농장에 클래스 추가 및 배경색 변경
        element.classList.add('selectedFarm');
        element.style.backgroundColor = 'rgb(135 151 122)'; // 새 배경색으로 변경

        // 세션 스토리지에 정보 저장
        sessionStorage.setItem('selectedFarmID', farmID);
        sessionStorage.setItem('selectedFarmAddress', address);
        sessionStorage.setItem('selectedFarmAddressDetail', addressDetail);
    }


</script>
</html>
