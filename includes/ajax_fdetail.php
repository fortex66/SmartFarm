<?php

// 농장ID로 화분의 ID를 전부다 가져오는 부분입니다.


    // HTTP 요청 메소드 확인
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        http_response_code(405); // Method Not Allowed
        die('Invalid request method');
    }

    // 데이터베이스 연결
    require '../includes/db_connect.php';

    // 클라이언트로부터 전송된 JSON 형식의 데이터를 받음
    $data = json_decode(file_get_contents('php://input'), true);

    // firstLetter 유효성 검사
    if (!isset($data['firstLetter']) || !is_numeric($data['firstLetter'])) {
        http_response_code(400); // Bad Request
        die('Invalid first letter');
    }

    $firstLetter = $data['firstLetter'] ;
    $query = "SELECT potID FROM pot WHERE Farm_farmID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $firstLetter);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 결과를 JSON 형식으로 클라이언트에 반환
    echo json_encode($result);

    // 데이터베이스 연결 종료
    $stmt->close();
    $con->close();
?>
