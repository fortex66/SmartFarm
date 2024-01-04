document.addEventListener('DOMContentLoaded', function() {
    var tblMonth = document.getElementById('tbl-month');
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth() + 1; // January is 0, so we add 1
    var monthThis = document.getElementById('month-this');
    var prevBtn = document.getElementById('month-prev');
    var nextBtn = document.getElementById('month-next');
    var calculatedDate = new Date('<?php echo $date; ?>');
    
    drawMonth(year, month, calculatedDate);
   
    
    document.getElementById('tbl-month').addEventListener('click', function(e) {
        var cells = document.querySelectorAll('#tbl-month td');
        cells.forEach(function(cell) {
          cell.classList.remove('selected');
        });
      
        if (e.target.tagName === 'TD' && !e.target.classList.contains('disabled-date')) {
          e.target.classList.add('selected');
          console.log("Cell clicked!");
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
    var year = 2023;
    var month = 11;
    
    drawMonth(year, month, calculatedDate);

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
        
        if (currentDate.getTime() >= calculatedDate.getTime()) {
          cell.classList.add('selectable'); // 선택 가능한 날짜에 'selectable' 클래스 추가
      } else {
          cell.classList.add('disabled-date'); // 선택 불가능한 날짜에 'disabled-date' 클래스 추가
      }
      if (currentDate.getDay() === 0) { // 일요일인 경우
          cell.classList.add('sun');
      } else if (currentDate.getDay() === 6) { // 토요일인 경우
          cell.classList.add('sat');
      }
        
        row.appendChild(cell);
        day++;
      }

      tblMonth.appendChild(row);
    }
  });