<?php include "Cartdb.php"; ?>
<!DOCTYPE html>
<html lang="ko">
<head>
<!-- ajax server생성  -->    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta charset="UTF-8">
    <title>장바구니</title>
    <link rel="stylesheet" href="../style4.css">
    <style>
        /* 여기에 스타일 추가 */
    </style>
    
 <!-- 총주문금액? Event.js=script5 외부 스크립트 파일을 불러옵니다 -->
 <script src="../script5.js"></script>

</head>
<body>
    <header>
<?php include '../includes/userNavbar.php'; ?>
</header>
<!-- 페이지 제목 표시  -->    
    <h1>장바구니</h1>

<!-- 작물의 정보_foreach반복문 crops배열 반복하며 각 요소값을임시로 crop에 할당  -->       
<?php 
$totalPrice = 0; 
if (!empty($crops)) { // $crops 배열이 비어있지 않으면 아래 내용을 수행
    foreach ($crops as $crop): ?>
    <!-- 각 작물에 대한 주문 정보를 표시하는 컨테이너 -->
        <div class="order-info">
            <div>
                <!-- 수정해야함 작물별로 고유한 체크박스 id할당 주고 나머지 함수 다 연결 -->
                <input type="checkbox" id="productCheckbox<?php echo $crop['cropID']; ?>" onclick="updateTotalPrice(<?php echo $crop['cropID']; ?>)">
                <label for="productCheckbox<?php echo $crop['cropID']; ?>"></label>
            </div>
            <!-- 작물별 Image끌어오기 crop작물에서   -->
            <img src="<?php echo $crop["pictureURL"]; ?>" alt="<?php echo $crop["pictureURL"]; ?>">
            <div>
                <!-- 작물별 상품명, 작물 재배 기간을 출력   -->
                <?php if (isset($crop["cropName"])): ?>
                    <p id="cropName">상품명: <?php echo $crop["cropName"]; ?></p>     
                <?php endif; ?>                
                <p id="harvestPeriod">작물 재배 기간: <?php echo $crop["harvsestPeriod"]; ?>(일)</p>
            </div>
            <div class="counter">
                <!-- post로 전달 action은 현재페이지로 설정 -->
                <form method="post" action="">
                    <!-- 수량 증감을 위한 수량관리버튼들이 담긴 div -->
                    <div id="quantityButtons<?php echo $crop['cropID']; ?>" style="display: flex; align-items: center;">
                        <!-- 서버에 form을 submit제출  div -->
                        <button type="submit" class="quantity-button" style="margin: 0; padding: 5px; cursor: pointer; margin-right: 5px; height: 30px;" name="decreaseButton<?php echo $crop['cropID']; ?>">-</button>
                        <div id="numberDisplay<?php echo $crop['cropID']; ?>"><?php echo $currentQuantity[$crop['cropID']]; ?>kg</div>
                        <button type="submit" class="quantity-button" style="margin: 0; padding: 5px; cursor: pointer; margin-left: 5px; height: 30px;" name="increaseButton<?php echo $crop['cropID']; ?>">+</button>
                </form>
            </div>        
            
            <form method="post" action="delete_Crop.php">        
                <!-- 삭제버튼 누를 시 db에서 삭제하기   div -->
                <div id="quantityButtons<?php echo $crop['cropID']; ?>" style="display: flex; align-items: center;">   
                    <button type="submit" class="delete-button" style="font-size: 15px; font-weight: bold; margin-left: 70px; width: 70px; height: 40px;" name="deleteButton" value="<?php echo $crop['cropID']; ?>">목록에서 삭제</button>
                </div>
                <!-- 현재 수량을 서버로 전송하기 위한 숨겨진 입력 필드입니다.   div -->
                <input type="hidden" id="currentQuantity<?php echo $crop['cropID']; ?>" name="currentQuantity<?php echo $crop['cropID']; ?>" value="<?php echo $currentQuantity; ?>">
            </form>
        </div>

        <!-- 작물의 가격을 받아와서 가격 표기  div -->
        <div id="priceDisplay<?php echo $crop["cropID"]; ?>">가격: <?php echo $crop["price"]; ?>(원)</div>
    </div>
    <?php endforeach; ?>
    <!-- 주문하기 버튼 및 총 주문 금액 표시 부분 -->
    <div class="order-button-container" id="orderButtonContainer" style="display: flex; justify-content: flex-end; align-items: center;">
    <?php
    if (isset($_POST['productCheckbox' . $crop['cropID']]) && $_POST['productCheckbox' . $crop['cropID']] == 'on') {
        $totalPrice += $crop["price"] * $currentQuantity;
    }
     ?>

    <!-- 총 주문 금액을 표시하는 부분 -->
    <p style="font-size: 20px;">
        총주문금액: <span id="totalPriceNumber"><?php echo number_format($totalPrice); ?></span>(원)&nbsp;&nbsp;
    </p>

    <!-- 주문을 처리하는 폼 -->
    <!-- 주문을 처리하는 폼 -->
<form action="delivery.php" method="POST">
    <!-- 주문한 상품의 ID와 수량을 전달하기 위한 입력 필드 -->
    <?php foreach ($crops as $crop): ?>
        <input type="hidden" name="cropIDs[]" value="<?php echo $crop['cropID']; ?>">
        <input type="hidden" name="quantities[]" value="<?php echo $currentQuantity[$crop['cropID']]; ?>">
    <?php endforeach; ?>
    <!-- 주문하기 버튼 -->
    <button type="submit" class="order-button" style="font-weight: bold;">주문하기</button>
</form>




</div>
<?php } // if문 종료 ?>
</body>
</html>
