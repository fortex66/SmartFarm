<html lang="kr">
<head>
    <meta charset="utf-8">
    <title>FarmLink-farmInfo</title>
    <link rel="stylesheet" href="../farmInfoStyle.css">
    <link rel="stylesheet" href="../style2.css">
</head>
<header>
    <?php include '../includes/userNavbar.php'; ?>
</header>
<body>
    <div id="map" style="width:1700px;height:800px;margin: auto; display: block;">
        
    </div>

    <p class="farmlist" style="width: 100%; color: black; font-size: 40px; font-family: Inter; font-weight: 400; word-wrap: break-word; padding-top: 50px; padding-left: 110px;">농장 위치</p>
    <div class="farmlist_fromdb">
        
    <?php
        // 데이터베이스 연결 정보
        require '../includes/db_connect.php';

        // 농장 번호와 주소를 가져오는 쿼리
        $sql = "SELECT farmID, `address`, addressDetail FROM farm";
        $result = $con->query($sql);

        // 결과 출력
        if ($result->num_rows > 0) {
            echo "<div style='display: flex; justify-content: space-around; margin-bottom : 30px;'>";
            $count = 0;
            while ($row = $result->fetch_assoc()) {
                echo "<div style='width: 30%; text-align: center;'>";
                echo "<p style='width: 100%; color: black; font-size: 30px; font-family: Inter; font-weight: 400; word-wrap: break-word; padding-top: 30px;'>" . $row["farmID"] . "번 농장<br></p>";
                echo "<p style='color: black; font-size: 20px; font-family: Inter; font-weight: 400; word-wrap: break-word;'>주소: " . $row["address"] . "<br></p>";
                echo "<p style='color: black; font-size: 20px; font-family: Inter; font-weight: 400; word-wrap: break-word;'>상세주소: " . $row["addressDetail"] . "<br></p>";
                echo "</div>";
                $count++;
                if ($count % 4 == 0) {
                    echo "</div><div style='display: flex; justify-content: space-around;'>";
                }
            }
            echo "</div>";
        } else {
            echo "농장이 없습니다.";
        }

        // 연결 종료
        $con->close();
    ?>

    </div>

</body>
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=dc0b68a90e143b639342c9d50cd6ac2f"></script>
    <script type="text/javascript">
    var container = document.getElementById('map'); //지도를 담을 영역의 DOM 레퍼런스
    var options = { //지도를 생성할 때 필요한 기본 옵션
        center: new kakao.maps.LatLng(36.8, 127), //지도의 중심좌표.
        level: 13 //지도의 레벨(확대, 축소 정도)
    };

    var map = new kakao.maps.Map(container, options); //지도 생성 및 객체 리턴

</script>

 
</html>