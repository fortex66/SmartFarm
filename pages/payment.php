<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel="stylesheet" href="../style2.css">
    <style>
        .box {
            position: absolute;
            left : 500px;
            width: 500px;
            top: 150px;
            height: auto;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .box2 {
            position: absolute;
            left : 500px;
            width: 500px;
            top: 400px;
            height: auto;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .box p {
            display: flex;
            margin: 0;
            padding: 3px 0;
            justify-content: space-between; /* p 요소 사이에 여백을 넣어 정렬합니다. */
        }
        .box2 p {
            display: flex;
            padding: 3px 0;
            justify-content: space-between;
        }
        #Date{
            margin : 0px 0px 0px 85px;
        }
        #farmAddress{
            margin-left : 0px;
  
        }
        #farmAddressDetail{
            margin-left : 245px;
  
        }
        #speratebox{
            margin-top:20px;
        }
        #Address {
            margin-left : 130px;
        }
        #DetailAddress {
            margin-left : 208px;
        }
        #ZipCode {
            margin-left : 208px;
        }

        .crop {
            flex: 1;
        }
        .quantity {
            flex: 1;
        }
        .price {
            flex: 1;
        }
        </style>
        </head>
<body>
    <header>
        <?php include '../includes/userNavbar.php'; ?>
    </header>
    <h1 style="position: absolute; left: 50px; top:100px;" >결제</h1>
    <div class="box">
    <?php
            session_start();
            // 데이터베이스 연결 설정
            require '../includes/db_connect.php';
            date_default_timezone_set('Asia/Seoul');
            if (isset($_SESSION['userID'])) {
                $userID = $_SESSION['userID']; 
            $sql = "SELECT * FROM cart WHERE User_userID = '$userID'"; // 쿼리 내용은 실제 테이블과 필드명에 맞게 수정해야 합니다.
            $result = $con->query($sql);
            echo "<p style='font-weight: bold;'><span class='crop'>작물</span><span class='quantity'>수량</span><span class='price'>가격</span></p>";
            echo "<br>";
            // 결과 출력
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $quantities[] = $row["quantity"];
                    $crops[] = $row['Crop_cropID'];
                    $sql2 = "SELECT cropName, price FROM crop WHERE cropID = {$row['Crop_cropID']}"; // 'crop'은 테이블 이름, 'id'는 해당 열 이름에 맞게 변경
                    $result2 = $con->query($sql2);
                    if ($result2->num_rows > 0) {
                        if ($row2 = $result2->fetch_assoc()) {
                            $cropNames[] = $row2["cropName"];
                            $prices[]=$row2["price"];
                    }
                    else{
                        echo "데이터가 없습니다.";
                    }
                } else {
                    echo "해당 ID에 대한 정보를 찾을 수 없습니다.";
                }
                }
            }
             else {
                echo "데이터가 없습니다.";
             }
            $sum=0;
            for ($i = 0; $i < count($crops); $i++) {
                $total = $prices[$i] * $quantities[$i];
                $sum =$sum+$total;
                echo "<p><span class='crop'>{$cropNames[$i]}(kg)</span><span class='quantity'>{$quantities[$i]}</span><span class='price'>{$total}</span></p>";
                echo "<br>";
            }
        }
        

            $con->close(); // 데이터베이스 연결 닫기
        ?>
        </div>
        <div class="box2">
    <?php
    
    require '../includes/db_connect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
        if (isset($_SESSION['userID'])) {
            $userID = $_SESSION['userID'];
            $currentDateTime = date('Y-m-d H:i:s');
    
            $Address = isset($_POST['Address']) ? $_POST['Address'] : '대구 용산동 롯데케슬';
            $DetailAddress = isset($_POST['DetailAddress']) ? $_POST['DetailAddress'] : '102동 505호';
            $ZipCode = isset($_POST['ZipCode']) ? intval($_POST['ZipCode']) : 0;
            $farmAddress = isset($_POST['farmAddress']) ? $_POST['farmAddress'] : '대구 용상2동';
            
            $addToOrderQuery = "INSERT INTO `order` (orderState, orderDate, address, addressDetail, zipCode, User_userID) VALUES (1,'$currentDateTime', '$Address', '$DetailAddress', '$ZipCode', '$userID')";  
            if ($con->query($addToOrderQuery) === TRUE) {
                    $last_id = $con->insert_id;
                    $sql3 = "SELECT * FROM cart WHERE User_userID = '$userID'"; // 쿼리 내용은 실제 테이블과 필드명에 맞게 수정해야 합니다.
                    $result3 = $con->query($sql3);
                    while ($row3 = $result3->fetch_assoc()) {
                        $addToOrderDetailQuery = "INSERT INTO orderdetail (amount, ORDER_orderID, CROP_cropID) VALUES ({$row3['quantity']}, $last_id, {$row3['Crop_cropID']})";
                        $result4 = $con->query($addToOrderDetailQuery);
                        $sql4 = "SELECT potID FROM mydb.pot 
                            WHERE Crop_cropID IS NULL AND Farm_farmID IN 
                                (SELECT farmID FROM mydb.farm WHERE address = '{$farmAddress}')
                            ORDER BY potID ASC LIMIT 1";
                        $result4 = $con->query($sql4);

                        if ($result4->num_rows > 0) {
                            $row4 = $result4->fetch_assoc();
                            $potIDToUpdate = $row4['potID'];

                            $updatePotQuery = "UPDATE mydb.pot 
                                            SET Crop_cropID = {$row3['Crop_cropID']} 
                                            WHERE potID = $potIDToUpdate";
                        }
                        $con->query($updatePotQuery);
                        $sql5="SELECT potID FROM mydb.pot WHERE Crop_cropID = '{$row3['Crop_cropID']}' AND Farm_farmID IN (SELECT farmID FROM mydb.farm WHERE address = '{$farmAddress}')";
                        $result6 = $con->query($sql5);
                        if ($result6->num_rows > 0) {
                            while ($row6 = $result6->fetch_assoc()) {
                                $potID = $row6['potID']; // 가져온 potID 값
                                echo $potID;
                                $sql6="INSERT INTO pot_has_order (order_orderID, pot_potID) VALUES ($last_id, $potID)";
                                $result7 = $con->query($sql6);
                            }
                        } else {
                            // 결과가 없는 경우 처리
                        
                        }
                    }
                    
                    // 구매가 완료되면 장바구니에서 비워야하므로
                    $deleteCartQuery = "DELETE FROM cart WHERE User_userID = '$userID'";
                    if ($con->query($deleteCartQuery)) {
                        // 장바구니 삭제 성공후 상단에 알림에서 확인 누르면 myinfo에서 구매목록 확인
                        echo "<script>
                            alert('구매가 완료되었습니다.');
                            window.location.href = 'myinfo.php';
                        </script>";
                        exit;
                    } else {
                        // 장바구니 삭제 실패
                        echo '<script>alert("장바구니 비우기에 실패했습니다.");</script>';
                    }
                } else {
                    echo '<script>alert("구매가 실패했습니다. 오류: ' . mysqli_error($con) .'")</script>';

                }
        }
    }

            $con->close(); // 데이터베이스 연결 닫기
?>

        <p style='font-weight: bold;'><span class='crop'>배송 받을 날짜</span><span class='quantity'>농장 주소</span></p>
        <p><span id='Date'></span><span id='farmAddress'></span></p><span id='farmAddressDetail'></span>
        <br>
        <p id='speratebox' style='font-weight: bold;'><span class='price'>배송지</span></p>
        <p id='Address'></p>
        <p id='DetailAddress'></p>
        <p id='ZipCode'></p>

        </div>
        <p style='font-weight: bold; position: absolute; top: 700px; left: 770px;'>배송비: 2500원</p>
        <p style='font-weight: bold; position: absolute; top: 700px; left: 970px;'>총 주문금액: <?php echo $sum; ?>원</p>
    <form method="post" action="">
        <input type="hidden" id="Date" name="Date" value=""/>
        <input type="hidden" id="cropID" name="cropID" value=""/>
        <input type="hidden" id="inputfarmAddress" name="farmAddress" value=""/>
        <input type="hidden" id="farmAddressDetail" name="farmAddressDetail" value=""/>
        <input type="hidden" id="inputAddress" name="Address" value=""/>
        <input type="hidden" id="inputDetailAddress" name="DetailAddress" value=""/>
        <input type="hidden" id="inputZipCode" name="ZipCode" value=""/>
        <button name="cart"  style="position: absolute; top: 690px; left: 1200px; width: 130px; height: 40px; cursor: pointer; background-color: #FFA500; border: 2px solid orange; font-weight: bold; border-radius: 5px;" type="submit">구매하기</button>
    </form>
    </body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('inputAddress').value = sessionStorage.getItem('selectedAddress');
        document.getElementById('inputDetailAddress').value = sessionStorage.getItem('selectedDetailAddress');
        document.getElementById('inputZipCode').value = sessionStorage.getItem('selectedZipCode');
        document.getElementById('inputfarmAddress').value = sessionStorage.getItem('selectedFarmAddress');
    });
        // 세션 스토리지에서 데이터 가져오기
        // 농장 주소 세션에서 가져오기
        //var selectedFarmID = sessionStorage.getItem('selectedFarmID');
        var selectedFarmAddress = sessionStorage.getItem('selectedFarmAddress');
        var selectedFarmAddressDetail = sessionStorage.getItem('selectedFarmAddressDetail');
        // 예상 배송 날짜 가져오기
        var selectedDate = sessionStorage.getItem('selectedDate');

        // 유저의 배송지 세션에서 가져오기
        var selectedAddress = sessionStorage.getItem('selectedAddress');
        var selectedDetailAddress = sessionStorage.getItem('selectedDetailAddress');
        var selectedZipCode = sessionStorage.getItem('selectedZipCode');
        var selectedcropID = sessionStorage.getItem('selectedcropID');




        // 가져온 데이터를 화면에 표시
        // document.getElementById('farmID').textContent = selectedFarmID;
        document.getElementById('Date').textContent = selectedDate;
        document.getElementById('farmAddress').textContent = selectedFarmAddress;
        document.getElementById('farmAddressDetail').textContent = selectedFarmAddressDetail;

        document.getElementById('Address').textContent = selectedAddress;
        document.getElementById('DetailAddress').textContent = selectedDetailAddress;
        document.getElementById('ZipCode').textContent = selectedZipCode;

        document.getElementById('cropID').textContent = selectedcropID;





    </script>