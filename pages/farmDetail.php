<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FarmLink</title>
    <link rel=stylesheet href='../style.css' >

</head>
<body>
    <header>
        <?php include '../includes/farmerNavbar.php'; ?>
    </header>

    <div class="farm-detail-container">
        <div class="farm-info">
            <!-- 빈 HTML 요소 추가 -->
            <h1 id="farmName"></h1>
            <p id="farmAddress"></p>
            <h2 id="potID"> </h2>

        </div>
        <div class="container">
            <div class="pot-list">
                <!-- 각 화분에 대한 정보를 표시하는 카드 -->

                <!-- 추가 화분 카드... -->
            </div>

            <!-- 오른쪽 상세 정보 -->
            <div class="pot-details">
                <!-- 선택된 화분에 대한 상세 정보 -->

            </div>
        </div>
        <!-- 나머지 내용... -->
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const farmName = sessionStorage.getItem('selectedFarmName');
            const farmAddress = sessionStorage.getItem('selectedFarmAddress');
             // 이전 페이지에서 선택된 화분 ID를 가져옵니다.
            const selectedPotID = sessionStorage.getItem('selectedPotID');

             // 만약 이전 페이지에서 화분 ID가 선택되었다면, 바로 해당 화분을 하이라이트
            if (selectedPotID) {
                highlightSelectedPot(selectedPotID);
            }

            const slicingfarm = farmName ? farmName.charAt(0) : '';

            const firstLetter = parseInt(slicingfarm);

            document.getElementById('farmName').textContent = farmName || '농장 이름 미확인';
            document.getElementById('farmAddress').textContent = farmAddress || '주소 미확인';

            console.log(typeof(firstLetter))

            if (firstLetter) {
                fetch('http://localhost/FarmLink/includes/ajax_fdetail.php', {
                    method: 'POST',
                    body: JSON.stringify({ firstLetter: firstLetter }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    updatePotList(data); // 화분 목록 업데이트 함수 호출
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    // 에러 처리 로직 (예: 사용자에게 오류 메시지 표시)
                });
            }

            // 만약 이전 페이지에서 화분 ID가 선택되었다면, 바로 해당 화분의 정보를 가져옵니다.
            if (selectedPotID) {
                fetchPotInfo(selectedPotID);
            }

            // 클릭 이벤트를 발생 시켜서 fetchPotInfo 함수에 클릭된 potID를 넘긴다.
            const potListDiv = document.querySelector('.pot-list');
            potListDiv.addEventListener('click', function(event) {
                const potDiv = event.target;
                if (potDiv.className === 'pot-names') {
                    const potID = potDiv.getAttribute('data-pot-id');
                    fetchPotInfo(potID);
                }
            });

            
        });


        function fetchPotInfo(potID) {
            fetch('http://localhost/FarmLink/includes/ajax_potInfo.php', {
                method: 'POST',
                body: JSON.stringify({ potID: potID }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // 콘솔에서 데이터 형식 확인
                displayPotInfo(data); // 받은 화분 정보를 표시하는 함수 호출
                highlightSelectedPot(potID); // 여기에서 하이라이트 함수를 호출
                
            })
            .catch(error => console.error('Error:', error));
        }


        function displayPotInfo(potDataArray) {
            // 이미 존재하는 pot-info div를 가져오거나 새로 만듭니다.
            const potInfoDiv = document.getElementById('pot-info') || createPotInfoDiv();
            // 기존 내용을 초기화합니다.
            potInfoDiv.innerHTML = '';

            // 장비 상태에 대한 헤더 생성
            const header1 = document.createElement('h2');
            header1.className = 'pot-detail-header';
            header1.textContent = '장비 상태';
            // 장비 상태 헤더를 potInfoDiv에 추가합니다.
            potInfoDiv.appendChild(header1);

            const poteqNavDiv = document.createElement('div');
            poteqNavDiv.className = 'pot-equipment-navi';

            const eqnavheader1 = document.createElement('h3');
            eqnavheader1.className = 'nav-equipment-header1';
            eqnavheader1.textContent = '장비명';

            const eqnavheader2 = document.createElement('h3');
            eqnavheader2.className = 'nav-equipment-header2';
            eqnavheader2.textContent = '장비 상태';


            poteqNavDiv.appendChild(eqnavheader1);
            poteqNavDiv.appendChild(eqnavheader2);

            
            potInfoDiv.appendChild(poteqNavDiv);


            // potDataArray에 있는 각 정보에 대해 div를 만들고 스타일을 적용합니다.
            potDataArray.equipment.forEach(item  => {

                // 장비 상세 정보를 담을 div를 생성하고 클래스를 적용합니다.
                const potDetailDiv = document.createElement('div');
                potDetailDiv.className = 'pot-detail';

                // 장비 상세 정보를 담을 p 태그를 만들고 내용을 추가합니다.
                const equipmentName = document.createElement('p');
                equipmentName.className = 'pot-device-name';
                equipmentName.textContent = `${item .deviceName}`;

                const equipmentStatus = document.createElement('p');
                equipmentStatus.className = 'pot-device-status';
                equipmentStatus.textContent = item.deviceStatus === 1 ? '정상' : '고장';
                equipmentStatus.classList.add(item.deviceStatus === 1 ? 'status-normal' : 'status-fault');

                // 각 상세 정보 p 태그를 상세 정보 div에 추가합니다.
                potDetailDiv.appendChild(equipmentName);
                potDetailDiv.appendChild(equipmentStatus);

                // 완성된 상세 정보 div를 potInfoDiv에 추가합니다.
                potInfoDiv.appendChild(potDetailDiv);
            });



            // 작물 정보 표시
            if (potDataArray.crop && potDataArray.crop.length > 0) {

                // 장비 상태에 대한 헤더 생성
                const header2 = document.createElement('h2');
                header2.className = 'crop-detail-header';
                header2.textContent = '작물 정보';
                // 장비 상태 헤더를 potInfoDiv에 추가합니다.
                potInfoDiv.appendChild(header2);

                const potCropNavDiv = document.createElement('div');
                potCropNavDiv.className = 'pot-crop-navi';

                const navheader1 = document.createElement('h3');
                navheader1.className = 'nav-crop-header1';
                navheader1.textContent = '작물명';

                const navheader2 = document.createElement('h3');
                navheader2.className = 'nav-crop-header2';
                navheader2.textContent = '수확 예상 날짜';

                const navheader3 = document.createElement('h3');
                navheader3.className = 'nav-crop-header3';
                navheader3.textContent = '진행률';


                potCropNavDiv.appendChild(navheader1);
                potCropNavDiv.appendChild(navheader2);
                potCropNavDiv.appendChild(navheader3);

                
                potInfoDiv.appendChild(potCropNavDiv);


                // 오늘 날짜를 가져옵니다.
                const today = new Date();
                potDataArray.crop.forEach(cropInfo => { // 배열의 각 요소에 대해 반복
                    const startDate = new Date(cropInfo.latestOrderDate);
                    const harvestDate = new Date(startDate);
                    harvestDate.setDate(harvestDate.getDate() + cropInfo.harvestPeriod);

                    const harvestDateString = harvestDate.toISOString().split('T')[0];
                    const progressTime = today - startDate;
                    const totalTime = harvestDate - startDate;
                    let progressPercent = (progressTime / totalTime) * 100;
                    progressPercent = Math.min(Math.max(progressPercent, 0), 100);

                    const potCropDiv = document.createElement('div');
                    potCropDiv.className = 'pot-crop-info';

                    const cropName = document.createElement('p');
                    cropName.className = 'pot-crop-name';
                    cropName.textContent = `${cropInfo.cropName}`;

                    // const harvestPeriod = document.createElement('p');
                    // harvestPeriod.className = 'pot-harvest-period';
                    // harvestPeriod.textContent = `${cropInfo.latestOrderDate}`;

                    const harvestDateElement = document.createElement('p');
                    harvestDateElement.className = 'pot-harvest-date';
                    harvestDateElement.textContent = `${harvestDateString}`;

                    const progressElement = document.createElement('p');
                    progressElement.className = 'pot-progress';
                    progressElement.textContent = `${progressPercent.toFixed(2)}%`;

                    potCropDiv.appendChild(cropName);
                    //potCropDiv.appendChild(harvestPeriod);
                    potCropDiv.appendChild(harvestDateElement);
                    potCropDiv.appendChild(progressElement);

                    potInfoDiv.appendChild(potCropDiv);
                });
            }

            // potInfoDiv를 적절한 위치에 삽입합니다.
            // 예를 들어, .pot-details div가 이미 페이지에 존재한다고 가정하면 다음과 같습니다.
            const potDetailsDiv = document.querySelector('.pot-details');
            potDetailsDiv.appendChild(potInfoDiv);
            }

            function createPotInfoDiv() {
            const div = document.createElement('div');
            div.id = 'pot-info';
            return div;
            }


        function updatePotList(data) {
            const potListDiv = document.querySelector('.pot-list');
            potListDiv.innerHTML = ''; // 기존 내용 초기화

            data.forEach(pot => {
                const potDiv = document.createElement('div');
                potDiv.className = 'pot-names';
                potDiv.setAttribute('data-pot-id', pot.potID);
                potDiv.textContent = `${pot.potID}번 화분`;
                potListDiv.appendChild(potDiv);
            });
            // 업데이트가 완료된 후에 하이라이트 함수를 호출합니다.
            // 이전 페이지에서 선택된 화분이 있을 경우에만 하이라이트합니다.
            const selectedPotID = sessionStorage.getItem('selectedPotID');
            if (selectedPotID) {
                highlightSelectedPot(selectedPotID);
            }
        }

        function highlightSelectedPot(potID) {
            // 모든 화분의 하이라이트를 제거합니다.
            const pots = document.querySelectorAll('.pot-names');
            pots.forEach(pot => {
                pot.classList.remove('selected-pot');
            });

            // 선택된 화분에만 하이라이트 클래스를 추가합니다.
            const selectedPot = document.querySelector(`[data-pot-id='${potID}']`);
            if (selectedPot) {
                selectedPot.classList.add('selected-pot');
            }
        }
    </script>


</body>
</html>
