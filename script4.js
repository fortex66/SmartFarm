document.addEventListener('DOMContentLoaded', function() {
  var today = new Date();
  var year = today.getFullYear();
  var month = today.getMonth() + 1; // January is 0, so we add 1

  var tblMonth = document.getElementById('tbl-month');
  var monthThis = document.getElementById('month-this');
  var prevBtn = document.getElementById('month-prev');
  var nextBtn = document.getElementById('month-next');

  document.getElementById('tbl-month').addEventListener('click', function(e) {
    var cells = document.querySelectorAll('#tbl-month td');
    cells.forEach(function(cell) {
      cell.classList.remove('selected');
    });

    if (e.target.tagName === 'TD' && !e.target.classList.contains('disabled-date')) {
      e.target.classList.add('selected');
      

      // 선택된 날짜를 세션에 저장
      var selectedDate = year + '-' + month + '-' + e.target.textContent;
      sessionStorage.setItem('selectedDate', selectedDate);

    }
  });

  prevBtn.addEventListener('click', function() {
    month--;
    if (month === 0) {
      month = 12;
      year--;
    }
    drawMonth(year, month);
  });

  nextBtn.addEventListener('click', function() {
    month++;
    if (month === 13) {
      month = 1;
      year++;
    }
    drawMonth(year, month);
  });

  drawMonth(year, month);

  function drawMonth(year, month) {
    tblMonth.innerHTML = '';
    var weekdays = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat']; // Array of weekdays

    // Create the header row with weekday names
    var headerRow = document.createElement('tr');
    for (var i = 0; i < 7; i++) {
      var dayHeader = document.createElement('th');
      dayHeader.textContent = weekdays[i];
      headerRow.appendChild(dayHeader);
    }
    tblMonth.appendChild(headerRow);

    var firstDay = new Date(year, month - 1, 1).getDay(); // Get the day of the week for the 1st of the month
    var lastDate = new Date(year, month, 0).getDate(); // Get the last day of the month

    monthThis.textContent = year + '.' + ('0' + month).slice(-2);

    var row = document.createElement('tr');
    var day = 1;

    for (var i = 0; i < firstDay; i++) {
      var cell = document.createElement('td');
      cell.textContent = '';
      row.appendChild(cell);
    }

    while (day <= lastDate) {
      if (row.children.length === 7) {
        tblMonth.appendChild(row);
        row = document.createElement('tr');
      }
      var cell = document.createElement('td');
      cell.textContent = day;
      var currentDate = new Date(year, month - 1, day);
      if (currentDate.getTime() >= deliveryDate.getTime()) {
        cell.classList.add('selectable'); // 선택 가능한 날짜에 'selectable' 클래스 추가
      } else {
        cell.classList.add('disabled-date'); // 선택 불가능한 날짜에 'disabled-date' 클래스 추가
      }
      
      row.appendChild(cell);
      day++;
    }

    tblMonth.appendChild(row);
  }
  // 주소 저장
  // document.getElementById('saveDetailAddress').addEventListener('click', function() {
  //   var address = document.getElementById('address').value;
  //   var detailAddress = document.getElementById('detail-address').value;

  //   // AJAX 요청
  //   $.ajax({
  //     url: 'update_address.php',
  //     method: 'POST',
  //     data: { address: encodeURIComponent(address), detailAddress: encodeURIComponent(detailAddress) },
  //     success: function(response) {
  //       alert(response);
  //     }
  //   });
  // });

}); 


window.onload = function() {
  var selectElement = document.getElementById('address-select');
  var selectedAddress = JSON.parse(selectElement.options[selectElement.selectedIndex].value);
  document.getElementById('address').value = selectedAddress.address;
  document.getElementById('detail-address').value = selectedAddress.addressDetail;
  document.getElementById('zipCode').value = selectedAddress.zipCode;
  sessionStorage.setItem('selectedAddressName', selectedAddress.addressName);
  sessionStorage.setItem('selectedAddress', selectedAddress.address);
  sessionStorage.setItem('selectedDetailAddress', selectedAddress.addressDetail);
  sessionStorage.setItem('selectedZipCode', selectedAddress.zipCode);
};

document.getElementById('address-select').addEventListener('change', function() {
  var selectedAddress = JSON.parse(this.value);
  document.getElementById('address').value = selectedAddress.address;
  document.getElementById('detail-address').value = selectedAddress.addressDetail;
  document.getElementById('zipCode').value = selectedAddress.zipCode;
  // 선택한 주소의 세부 정보를 세션 스토리지에 저장
  sessionStorage.setItem('selectedAddressName', selectedAddress.addressName);
  sessionStorage.setItem('selectedAddress', selectedAddress.address);
  sessionStorage.setItem('selectedDetailAddress', selectedAddress.addressDetail);
  sessionStorage.setItem('selectedZipCode', selectedAddress.zipCode);
});
