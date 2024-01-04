<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel="stylesheet" href="../style2.css">
    <script src="script.js"></script>
    <style>
        .box {
            position: absolute;
            left : 100px;
            width: 1000px;
            top: 140px;
            height: auto;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            text-align: left;
        }
        .box p {
            display: flex;
            margin: 0;
            padding: 3px 0;
            justify-content: space-between; /* p 요소 사이에 여백을 넣어 정렬합니다. */
        }
        .box2 {
            position: absolute;
            left : 100px;
            width: 1200px;
            top: 450px;
            height: auto;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .box2 p {
            display: flex;
            margin: 0;
            padding: 3px 0;
            justify-content: space-between; /* p 요소 사이에 여백을 넣어 정렬합니다. */
        }
        .crop {
            flex: 1;
        }
        .quantity {
            flex: 1;
        }
        </style>
    </head>
<body>
    <header>
        
        <?php 
            session_start();
            include '../includes/userNavbar.php'; 
        ?>
    </header>
    <h2 style="position: absolute; left: 120px; top:100px;">기본정보</h2>
    <div class="box">
    <?php
            // 데이터베이스 연결 설정
            require '../includes/db_connect.php';
            if (isset($_SESSION['userID'])) {
                $userID = $_SESSION['userID']; 
            $sql = "SELECT * FROM user WHERE userID = '$userID'"; // 쿼리 내용은 실제 테이블과 필드명에 맞게 수정해야 합니다.
            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                if ($row = $result->fetch_assoc()) {
                    $sql2 = "SELECT * FROM `address` WHERE User_userID = '$userID' and addressName ='집'";
                    $result2 = $con->query($sql2); 
                    if ($result2->num_rows > 0) {
                        if ($row2 = $result2->fetch_assoc()) {
                            echo "<p style='font-weight: bold;'><span class='crop'>아이디</span> <span class='quantity'>" . $row["userID"] . "</span><span class='crop'>이름</span> <span class='quantity'>" . $row["name"] . "</span></p>";
                            echo "<br>";
                            echo "<p style='font-weight: bold;'><span class='crop'>생년월일</span> <span class='quantity'>" . $row["birthDate"] . "</span><span class='crop'>주소</span> <span class='quantity'>" . $row2["address"] . "</span></p>";
                            echo "<br>";
                            echo "<p style='font-weight: bold;'><span class='crop'>전화번호</span> <span class='quantity'>" . $row["phoneNumber"] . "</span><span class='crop'>상세주소</span> <span class='quantity'>" . $row2["addressDetail"] . "</span></p>";
                            echo "<br>";
                            echo "<p style='font-weight: bold;'><span class='crop'>이메일</span> <span class='quantity'>" . $row["email"] . "</span><span class='crop'>우편번호</span> <span class='quantity'>" . $row2["zipCode"] . "</span></p>";
                        }
                }
            }
            }
            }
    ?>
     </div>
     <h2 style="position: absolute; left: 120px; top:400px;">구매목록</h2>
     <div class="box2">
    <?php
            require '../includes/db_connect.php';
            if (isset($_SESSION['userID'])) {
                $userID = $_SESSION['userID']; 
            $sql3 = "SELECT O.orderID, O.orderState, O.orderDate, OD.amount, C.cropName, C.price 
            FROM `order` O
            JOIN orderdetail OD ON O.orderID = OD.Order_orderID
            JOIN crop C ON OD.Crop_cropID = C.cropID
            WHERE O.User_userID = '$userID'
            ORDER BY orderID DESC "; // 쿼리 내용은 실제 테이블과 필드명에 맞게 수정해야 합니다.
            $result3 = $con->query($sql3);
            echo "<p style='font-weight: bold;'><span class='crop'>주문번호</span><span class='crop'>주문상태</span><span class='crop'>주문날짜</span></span><span class='crop'>수량</span></span><span class='crop'>작물</span></span><span class='crop'>가격</span></p>";
            echo "<br>";
            if ($result3->num_rows > 0) {
                while ($row3 = $result3->fetch_assoc()) {
                            echo "<p style='font-weight: bold;'> <span class='quantity'>" . $row3["orderID"] . "</span><span class='quantity'>" . $row3["orderState"] . "</span> <span class='quantity'>" . $row3["orderDate"] . "</span> <span class='quantity'>" . $row3["amount"] . "</span> <span class='quantity'>" . $row3["cropName"] . "</span> <span class='quantity'>" . $row3["amount"]*$row3["price"] . "</span></p>";
                            echo "<br>";
            }
            }
            }
    ?>
     </div>
    </body>
</html>