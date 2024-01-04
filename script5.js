function updateTotalPrice(cropID) {
    // 체크박스 엘리먼트 가져오기
    var checkbox = document.getElementById('productCheckbox' + cropID);

    // 수량 표시 엘리먼트 가져오기
    var quantityDisplay = document.getElementById('numberDisplay' + cropID);

    // 현재 수량 가져오기
    var currentQuantity = parseInt(quantityDisplay.innerText);

    // 가격 표시 엘리먼트 가져오기
    var priceDisplay = document.getElementById('priceDisplay' + cropID);

    // 총 주문 금액 표시 엘리먼트 가져오기
    var totalPriceDisplay = document.getElementById('totalPriceNumber');

    // 현재 총 주문 금액 가져오기
    var total = parseFloat(totalPriceDisplay.innerText.replace(',', ''));

    // 작물의 가격 가져오기
    var price = parseFloat(priceDisplay.innerText.replace('가격: ', '').replace('(원)', '').replace(',', ''));

    // 체크박스 상태에 따라 총 주문 금액 업데이트
    if (checkbox.checked) {
        total += currentQuantity * price;
    } else {
        total -= currentQuantity * price;
    }
    // 총 주문 금액 표시 업데이트
    totalPriceDisplay.innerText = number_format(total);
}

// 수량 버튼이 클릭되면 실행되는 함수
$(".quantity-button").click(function(e) {
    e.preventDefault();  // 기본 이벤트를 막습니다.
    
    // 버튼의 이름과 현재 수량을 가져옵니다.
    var name = $(this).attr('name');
    var cropID = name.substring(name.length - 1); // 작물의 ID를 가져옵니다.
    var quantity = parseInt($("#currentQuantity" + cropID).val());
    
    // 수량을 증가시키거나 감소시킵니다.
    if (name.indexOf('increaseButton') !== -1) {
        quantity++;
    } else if (name.indexOf('decreaseButton') !== -1 && quantity > 1) {
        quantity--;
    }
    
    // AJAX 요청을 보냅니다.
    $.post("update_quantity.php", {
        currentQuantity: quantity,
        cropID: cropID  // 작물의 ID를 서버로 보냅니다.
    }, function(data, status) {
        // 요청이 성공적으로 완료되면 수량을 업데이트하고 전체 가격을 업데이트합니다.
        $("#currentQuantity" + cropID).val(quantity);
        $("#numberDisplay" + cropID).text(quantity + "kg");
        updateTotalPrice(cropID); // 작물별로 전체 가격을 업데이트하는 함수 호출
    });
});
function getCheckedCropIDs() {
    var checkedCropIDs = [];
    $('input[type="checkbox"]').each(function() {
        if ($(this).is(":checked")) {
            var id = $(this).attr('id').replace('productCheckbox', '');
            checkedCropIDs.push(id);
        }
    });
    return checkedCropIDs;
}
function getLongestHarvestPeriod() {
    var longestHarvestPeriod = 0;
    var checkedCropIDs = getCheckedCropIDs();
    for (var i = 0; i < checkedCropIDs.length; i++) {
        var id = checkedCropIDs[i];
        var harvestPeriod = parseInt($('#harvestPeriod' + id).text().replace('작물 재배 기간: ', '').replace('(일)', ''));
        if (harvestPeriod > longestHarvestPeriod) {
            longestHarvestPeriod = harvestPeriod;
        }
    }
    return longestHarvestPeriod;
}

$(document).ready(function(){
    $('input[type="checkbox"]').click(function(){
        if($(this).is(":checked")){
            $(this).parent().parent().css("background-color", "#f0f0f0");
        }
        else if($(this).is(":not(:checked)")){
            $(this).parent().parent().css("background-color", "white");
        }
    });
});




function number_format(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}