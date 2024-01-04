<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel="stylesheet" href="../style.css">
    
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 50px;
        }
        .item {
            width: calc(20% - 20px);
            padding: 10px;
            box-sizing: border-box;
            text-align: center;
        }
        #sortText {
            text-align: right;
            width: 100%;
        }
        #sortDescending {
            text-align: right;
            cursor: pointer;
            display: inline-block; /* 요소들이 인라인 블록으로 배치되도록 설정 */
            margin: 10px 0; /* 버튼 간격 조정 */
            margin-left: 10px; /* 왼쪽 여백을 10px로 설정 */
            margin-right: 10px; /* 오른쪽 여백을 10px로 설정 */
        }
        #sortAscending {
            text-align: right; /* 텍스트를 오른쪽으로 정렬합니다. */
            cursor: pointer; /* 커서를 변경하여 마우스 호버 시 사용자에게 클릭 가능함을 보여줍니다. */
            display: inline-block;
            margin: 10px 0;
            margin-left: 10px; /* 왼쪽 여백을 10px로 설정 */
            margin-right: 10px; /* 오른쪽 여백을 10px로 설정 */
        }
        #manyordering {
            text-align: right; /* 텍스트를 오른쪽으로 정렬합니다. */
            cursor: pointer; /* 커서를 변경하여 마우스 호버 시 사용자에게 클릭 가능함을 보여줍니다. */
            display: inline-block;
            margin: 10px 0;
            margin-left: 10px; /* 왼쪽 여백을 10px로 설정 */
            margin-right: 100px; /* 오른쪽 여백을 10px로 설정 */
        }
        h1 {
        position: relative; /* 상대 위치 설정 */
        top: 20px; /* 위쪽으로 20px 이동 */
        margin-left: 150px; /* 왼쪽으로 50px 이동 */
    }
    </style>
    <script>
        // 높은 가격순으로 정렬
        function sortByPriceDescending() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector('.container').innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "sort_by_dsc.php", true);
            xhttp.send();
        }
    </script>
    <script>
        // 판매량 순으로 정렬
        function manyorderdescending() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector('.container').innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "manyorder.php", true);
            xhttp.send();
        }
    </script>
    <script>
        // 낮은 가격순으로 정렬
        function sortByPriceAscending() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector('.container').innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "sort_by_asc.php", true);
            xhttp.send();
        }
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("sortAscending").addEventListener("click", function() {
                sortByPriceAscending(); // 버튼 클릭 시 낮은 가격순으로 정렬 함수 호출
            });
            document.getElementById("sortDescending").addEventListener("click", function() {
                sortByPriceDescending(); // 버튼 클릭시 높은 가격순으로 정렬 함수 호출
            });
            document.getElementById("manyordering").addEventListener("click", function() {
                manyorderdescending(); // 버튼 클릭시 판매량순으로 정렬 함수 호출
            });
        });
    </script>
</head>
<body>
    <header>
        <?php include '../includes/userNavbar.php'; ?>
        <link rel="stylesheet" href="../style2.css">
    </header>
    <h1>농산물</h1>
    <div id="sortText">
        <p id="sortDescending">높은 가격순</p>
        <p id="sortAscending">낮은 가격순</p>
        <p id="manyordering">판매량순</p>
    </div>
    <div class="container ">
            <?php
            // 데이터베이스 연결 설정
            
            require '../includes/db_connect.php';

            // 데이터베이스에서 정보 가져오기
            $sql = "SELECT * FROM crop"; // your_table에는 데이터를 가져올 테이블 이름을 입력하세요
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
        </div>
    </div>
</body>
</html>