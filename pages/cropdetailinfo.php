<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel="stylesheet" href="../style2.css">
    
    <style>
        .collapsed {
            display: none;
        }
        .expanded {
            display: block;
        }
        .image-wrapper {
    display: flex;
    justify-content: center; /* 수평 가운데 정렬 */
    margin-top: 700px;

}
     .image-container {
            width: 400px; /* 이미지가 보여질 영역의 너비 */
            height: 300px; /* 이미지가 보여질 영역의 높이 */
            overflow: hidden; /* 이미지 영역 밖의 내용 숨김 */
            position: relative;
        }
        .image-container img {
            position: absolute;
            top: -100px; /* 이미지가 보여질 위치 조정 (원하는 만큼 조절) */
            left: -100px; /* 이미지가 보여질 위치 조정 (원하는 만큼 조절) */
        }
    </style>
</head>
<body>
    <header>
        <?php include '../includes/userNavbar.php'; ?>
    </header>
    <div class="container">
        <?php
        session_start();

        // 데이터베이스 연결 설정
        require '../includes/db_connect.php';
// 사용자 로그인이 성공했을 때, 해당 사용자의 userID를 세션 변수에 저장


        if (isset($_GET['cropID'])) {
            $cropID = $_GET['cropID'];
            // 데이터베이스에서 정보 가져오기
            $sql = "SELECT * FROM crop WHERE cropID = $cropID"; // 'crop'은 테이블 이름, 'id'는 해당 열 이름에 맞게 변경
            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // 여기서 $row를 사용하여 해당 정보를 출력하거나 처리할 수 있습니다.
                    $harvestPeriod = $row['harvsestPeriod'];
                    $deliveryPeriod = 3;
                    // 현재 날짜에서 작물 재배 기간을 더한 날짜를 계산
                    $endDate = date('Y-m-d', strtotime("+" . ($harvestPeriod + $deliveryPeriod) . " days"));
                    echo '<p style="position: absolute; top: 300px; left: 800px;">상품명 :' . $row['cropName'] . '(kg) <br> 가격 : ' . $row['price'] . '(원) <br>작물 재배 기간 : ' . $row['harvsestPeriod'] . '(일) <br> 예상 배송 날짜 : ' . $endDate . '</p>';
                    // 기타 필요한 정보들을 출력하거나 처리할 수 있습니다.
                    $imagePath = $row["pictureURL"];
                    $imageDPath = $row["picturedetailURL"];
                    echo '<img style="position: absolute; top: 200px; left: 300px;" src="' . $row["pictureURL"] . '" alt="이미지" width="400" height="400">';
                }
            } else {
                echo "해당 ID에 대한 정보를 찾을 수 없습니다.";
            }
        }
            ?>
        <div class="image-wrapper">
        <?php
        $sql2 = "SELECT picturedetailURL FROM crop WHERE cropID = $cropID";
        $result2 = $con->query($sql2);

        if ($result2->num_rows > 0) {
            $row2 = $result2->fetch_assoc();
            $imagePath = $row2['picturedetailURL']; // 가져온 이미지 URL

            // 이미지를 출력하는 부분 (기본적으로 펼쳐진 상태)
        echo '<img class="collapsed" src="' . $imagePath . '" alt="Your Image">';

        } else {
            echo "이미지가 없습니다.";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['cart'])) {
                if (isset($_GET['cropID'])) {
                    $cropID = $_GET['cropID']; // 현재 페이지의 cropID
                    if (isset($_SESSION['userID'])) {
                        $userID = $_SESSION['userID']; // 세션에서 현재 유저 ID 가져오기
                        // 이미 장바구니에 해당 crop이 있는지 확인
                        $checkQuery = "SELECT * FROM cart WHERE Crop_cropID = $cropID AND User_userID = '$userID'";
                        $checkResult = $con->query($checkQuery);
        
                        if ($checkResult->num_rows > 0) {
                            // 이미 장바구니에 해당 상품이 있는 경우
                            echo '<script>alert("이미 장바구니에 있는 상품입니다.");</script>';
                        } else {
                            
                             $countFromJS = $_POST['count'];
                            // 장바구니에 추가하는 쿼리
                            $addToCartQuery = "INSERT INTO cart (quantity, User_userID, Crop_cropID) VALUES ('$countFromJS', '$userID', '$cropID')";
                            if ($con->query($addToCartQuery) === TRUE) {
                                echo '<script>alert("장바구니에 추가되었습니다.");</script>';
                            } else {
                                echo '<script>alert("장바구니 추가에 실패했습니다. 오류: ' . mysqli_error($con) .'")</script>';

                            }
                    }
                    } else {
                        // 세션에 userID가 없는 경우 (로그인 되지 않은 상태)
                        echo '<script>alert("로그인이 필요합니다.");</script>';
                    }
                }
            }
        }
        ?>    
            <button id="toggleButton" style="position: absolute; top: 650px; left: 600px; width: 300px; height: 50px; cursor: pointer; background-color: #FFA500; border: 2px solid orange; font-weight: bold; " type="submit">상품정보 펼쳐보기</button>
        </div>
    </div>
    <form method="post" action="">
        <input type="hidden" id="countInput" name="count" value="1">
        <button name="cart"  style="position: absolute; top: 400px; left: 800px; width: 200px; height: 30px; cursor: pointer; border: 2px solid black; font-weight: bold; border-radius: 5px;" type="submit" onclick="updateCount(); placeOrder('장바구니에 담겼습니다.', '장바구니에 담는데 실패하였습니다');">장바구니</button>
    </form>

    <script>
          document.addEventListener("DOMContentLoaded", function() {
        const image = document.querySelector('.collapsed');
        const toggleButton = document.getElementById('toggleButton');

        toggleButton.addEventListener('click', function() {
            image.classList.toggle('expanded');
            if (image.classList.contains('expanded')) {
                toggleButton.textContent = '상품정보 접기';
            } else {
                toggleButton.textContent = '상품정보 펼쳐보기';
            }
        });
    });

        function placeOrder(successMessage, failureMessage) {
            var form = document.getElementById('orderForm');
            var formData = new FormData(form);
            formData.append('count', count); 

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        alert(successMessage);
                    } else {
                        alert(failureMessage);
                    }
                }
            };

            xhr.open('POST', 'cropdetailinfo.php', true);
            xhr.send(formData);
        }

        let count = 1; // 초기 값

    document.addEventListener("DOMContentLoaded", function() {
        const decreaseButton = document.getElementById('decreaseButton');
        const increaseButton = document.getElementById('increaseButton');
        const valueElement = document.getElementById('value');
        const countInput = document.getElementById('countInput');

        decreaseButton.addEventListener('click', function() {
            if (count > 1) {
                count--;
                valueElement.textContent = count + 'kg';
                countInput.value = count; // count 값을 변경하여 form으로 전달
            }
        });

        increaseButton.addEventListener('click', function() {
            count++;
            valueElement.textContent = count + 'kg';
            countInput.value = count; // count 값을 변경하여 form으로 전달
        });
    });
    </script>
</body>
</html>