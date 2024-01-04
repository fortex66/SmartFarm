<?php
session_start(); // 세션 시작

// 세션에 저장된 주소와 상세주소 출력
echo "Address: " . $_SESSION["address"] . "<br>";
echo "Detail Address: " . $_SESSION["detailAddress"] . "<br>";
?>
