<?php

    /* 데이터베이스 연결하는 파일입니다. */

    // 데이터 베이스 연결 설정
    $host = "localhost";
    $username = "root"; // 데이터베이스 사용자 이름
    $password = "*******"; // 데이터베이스 비밀번호
    $dbname = "*****"; // 데이터베이스 이름

    // 데이터베이스 연결
    $con = new mysqli($host, $username, $password, $dbname);

    // 연결 오류 확인
    if ($con->connect_error) {
        die("연결 실패: " . $con->connect_error);
    }

?>
