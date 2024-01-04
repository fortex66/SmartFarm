<?php

    /*  // AJAX
        웹페이지의 전체 페이지를 새로 고치지 않고, 페이지의 일부분만을 서버에서 가지고 와서 웹페이지 화면을 동적으로 변경하는 방식입니다.

        // 사용 이유
        동적인 기능 즉 움직이는 기능을 갖춘 웹페이지를 서비스하기 위해선 Ajax가 필수라서 Ajax를 활용하여 다음 페이지로 넘어가지 않고도 원하는 모습을 바로 확인할 수 있기 때문에 사용한다.

        // 코드 설명
        클라이언트로부터 AJAX 요청을 받아, 요청된 farmID에 해당하는 화분 정보를 데이터베이스에서 조회하고, 그 결과를 JSON 형식으로 클라이언트에게 응답합니다. 

    */


    // HTTP 요청 메소드 확인
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        http_response_code(405); // Method Not Allowed
        die('Invalid request method');
    }

    // 데이터베이스 연결 
    require '../includes/db_connect.php';
    
    /* 
        file_get_contents : 클라이언트로부터 전송된 JSON 형식의 데이터를 받는 데 사용 
        json_decode : JSON 형식의 문자열을 PHP 배열로 변환하고 true는 JSON 객체를 연관 배열로 변환하도록 지시한다.
    */
    $data = json_decode(file_get_contents('php://input'), true); 


    // farmID 유효성 검사 ( 데이터에 farmID가 있는지 검사 || 숫자인지 검사 )
    if (!isset($data['farmID']) || !is_numeric($data['farmID'])) {
        http_response_code(400); // Bad Request
        die('Invalid farmID');
    }

    $farmID = $data['farmID'];

    // 농장 ID에 해당하는 화분 데이터 조회
    $query = "SELECT * FROM pot WHERE Farm_farmID = ?"; 
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $farmID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 고장난 장비가 있는 화분의 potID를 가져오는 쿼리
    $failedQuery = "
    SELECT p.potID
    FROM `management` m
    JOIN `pot` p ON m.Pot_potID = p.potID
    WHERE m.deviceStatus = 0 AND p.Farm_farmID = ?
    ORDER BY p.potID ASC";
    $failedStmt = $con->prepare($failedQuery);
    $failedStmt->bind_param("s", $farmID);
    $failedStmt->execute();
    $failedResult = $failedStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // 중복된 'potID' 제거
    $failedPots = array_unique(array_map(function($pot) { return $pot['potID']; }, $failedResult));
    
    // 결과 PHP 배열을 JSON 형식의 문자열로 변환
    echo json_encode(['pots' => $result, 'failedPots' => $failedPots]);




    
    // 데이터베이스 연결 종료
    $stmt->close();
    $con->close();
?>
