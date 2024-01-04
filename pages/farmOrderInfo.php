<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <?php include '../includes/farmerNavbar.php'; ?>
    </header>
    <div class="Maintitle">
        <h1>주문 관리</h1>
    </div>

    <div class="tabs">
        <div class="tab active" onclick="showContent('all')">들어온 주문</div>
        <div class="tab" onclick="showContent('completed')">완료된 주문</div>
    </div>

    <!-- 탭 컨텐츠 -->
    <div id="all" class="content active">
        <!-- 들어온 주문 내용 -->
    </div>
    <div id="completed" class="content">
        <!-- 완료된 주문 내용 -->
    </div>

    <script>
        function fetchOrders(tab) {
            fetch('../includes/ajax_forders.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'userName=' + encodeURIComponent(currentUserName) + '&tab=' + tab
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.text();
            })
            .then(html => {
                document.getElementById(tab).innerHTML = html;
            })
            .catch(error => {
                console.error('Fetch error: ', error);
            });
        }

        function changeOrderState(orderID, newState) {
            fetch('../includes/ajax_forderstate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'orderID=' + orderID + '&newState=' + newState
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 성공적으로 상태가 변경되면 UI 업데이트
                    alert('주문을 취소하였습니다.');
                    location.reload(); // 페이지 새로고침하여 변경된 내용 반영
                } else {
                    // 오류 처리
                    alert('상태 변경에 실패했습니다: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }


        function showContent(tab) {
            var contents = document.getElementsByClassName('content');
            var tabs = document.getElementsByClassName('tab');
            
            // 모든 컨텐츠와 탭의 기본 상태로 리셋
            for (var i = 0; i < contents.length; i++) {
                contents[i].style.display = 'none';
            }
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].style.backgroundColor = '#f2f2f2'; // 활성화된 탭의 기본 배경색
            }

            // 선택된 탭과 관련된 컨텐츠를 활성화하고 배경색 변경
            var activeContent = document.getElementById(tab);
            var activeTab = document.querySelectorAll('.tab')[tab === 'all' ? 0 : 1]; // 'all'이면 첫 번째 탭, 그렇지 않으면 두 번째 탭 선택

            if (activeContent) {
                activeContent.style.display = 'block';
            }
            if (activeTab) {
                activeTab.style.backgroundColor = '#C9C9C9'; // 활성화된 탭의 배경색 변경
            }

            fetchOrders(tab);
        }

        // 페이지 로드 시 '전체' 탭을 활성화
        window.onload = function() {
            showContent('all');
        };



    </script>

    <footer>
        <!-- 푸터 정보 -->
    </footer>
</body>
</html>
