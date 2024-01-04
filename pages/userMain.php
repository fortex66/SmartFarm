<?php
require '../includes/db_connect.php';
?>

<html lang="kr">
<head>
    <meta charset="utf-8">
    <title>FarmLink-main</title>
    <link rel="stylesheet" href="../userMainStyle.css">
</head>
<body>

    <div class="navbar">
        <div class="logo">
            <a onclick="location.href='./userMain.php'"><img src="../assets/images/logo.png" alt="FarmLink 로고"/></a>
        </div>
        <ul class="menu">
            <li><a href="#farm-status" onclick="location.href='./cropInfo.php'">작물</a></li>
            <li><a href="#crop-market" onclick="location.href='./farmInfo.php'">농장</a></li>
            <li><a href="#orders" onclick="location.href='./MainCart.php'">장바구니</a></li>
            <li><a href="#account" onclick="location.href='./myInfo.php'">내 정보</a></li>
        </ul>
        <div class="user-info">
            <a href="#account" onclick="location.href='./login.php'">로그아웃</a>
        </div>
    </div>

    <div class="main-image">
        <div class="imagewrap">
            <img src="../assets/images/main_img.png" alt="FarmLink 로고"/>
            <div class="main_img_txt">
                <span>스마트팜<br>거래 플랫폼</span>
                <span>중개자없는 거래를 하고<br>효율적으로 스마트팜을 관리해보세요</span>
            </div>
        </div>
     </div>

    <div class="recommended_slide">
        <p>FarmLink<br>추천 작물</p>
        <div class="slidewrap">
            <ul class="slides">
                <li><a href="#carrot_order" onclick="location.href='./cropInfo.php'"><img src="../assets/images/carrot.png" style="width:1255px; height:748px;"/></a></li>
                <li><a href="#lettuce_order" onclick="location.href='./cropInfo.php'"><img src="../assets/images/lettuce.png" style="width:1255px; height:748px;"/></a></li>
                <li><a href="#tomato_order" onclick="location.href='./cropInfo.php'"><img src="../assets/images/tomato.png" style="width:1255px; height:748px;"/></a></li>
            </ul>
            <p class="controller">
                <span class="prev">&lang;</span>  
                <span class="next">&rang;</span>
            </p>
        </div>
        <script src="js_slide.js"></script>
    </div>

    <div class="most_ordered_crops">
        <p>최근 가장 많이 주문된 작물</p>
        <ul class="most_ordered_crop_img">
            <?php
                // 쿼리: 최근 1달간 가장 많이 주문된 작물 3개
                $query = "
                SELECT
                    c.cropName,
                    c.pictureURL,
                    SUM(od.amount) AS totalAmount
                FROM
                    orderdetail od
                JOIN
                    crop c ON od.Crop_cropID = c.cropID
                JOIN
                    `order` o ON od.Order_orderID = o.orderID
                WHERE
                    o.orderDate >= CURDATE() - INTERVAL 1 MONTH
                GROUP BY
                    c.cropID
                ORDER BY
                    totalAmount DESC
                LIMIT
                    3;
                ";

                $result = $con->query($query);
                // 결과 출력
                while ($row = $result->fetch_assoc()) {
                    echo "<li>";
                    echo "<a href='./cropInfo.php'>";
                    echo "<img src='{$row['pictureURL']}' alt='{$row['cropName']}' style='width:570px; height:380px;'>";
                    echo "</a>";
                    echo "</li>";
                }
            ?>
        </ul>
    </div>

    <div class="new_crops">
        <p>신규 작물</p>
        <ul class="new_crop_img">
            <li><a href="#carrot_order" onclick="location.href='./cropInfo.php'"><img src="../assets/images/carrot.png" style="width:570px; height:380px;"/></a></li>
            <li><a href="#lettuce_order" onclick="location.href='./cropInfo.php'"><img src="../assets/images/lettuce.png" style="width:570px; height:380px;"/></a></li>
            <li><a href="#tomato_order" onclick="location.href='./cropInfo.php'"><img src="../assets/images/tomato.png" style="width:570px; height:380px;"/></a></li>
        </ul>
    </div>

    <div class="bottom_bar">
        <div class="logo">
            <img src="../assets/images/logo.png" alt="FarmLink 로고"/>
        </div>
        <div class="companytxt">
            <p>경산캠퍼스 우) 38541 경상북도 경산시 대학로 280 영남대학교<br>대구캠퍼스 우) 42415 대구광역시 남구 현충로 170<br><br>TEL 053-810-2114 / FAX 053-810-2036<br>Copyright 2023 Yeungnam University. All Rights Reserved.</p>
        </div>
    </div>
    <?php
        // 데이터베이스 연결 종료
        $con->close();
    ?>
</body>
</html>