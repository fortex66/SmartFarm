<?php
require '../includes/db_connect.php';

// 가격에 따라 데이터베이스에서 정보 가져오기 (가격 기준으로 내림차순 정렬)
$sql = "SELECT * FROM crop ORDER BY price DESC";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // 각 항목을 출력하는 부분
        echo '<div class="item">';
        $imagePath = $row["pictureURL"];
        echo '<a href="cropdetailinfo.php?cropID=' . $row["cropID"] . '">';
        echo '<img src="' . $row["pictureURL"] . '" alt="이미지" width="200" height="200" >'; 
        echo '</a>'; // 이미지에 대한 링크 종료 
        echo '<h5>' . $row["cropName"] . '</h5>';
        // 다른 필드 정보들 추가
        echo '<p>' . $row["price"] . '원</p>';
        echo '</div>';
    }
} else {
    echo "데이터를 찾을 수 없습니다.";
}

// 데이터베이스 연결 닫기
$con->close();
?>