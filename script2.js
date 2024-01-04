document.addEventListener('DOMContentLoaded', function() {
    var date = new Date();
    drawMonth(date);
    var tblMonth = document.getElementById('tbl-month');
    if (tblMonth !== null) {
        tblMonth.innerHTML = ''; // Your code that modifies the table
    } else {
        console.error("The element with ID 'tbl-month' was not found.");
    }
  
    document.querySelectorAll('.month-move').forEach(function(button) {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        var ym = new Date(this.dataset.ym);
        if (!isNaN(ym.getTime())) {
          drawMonth(ym);
        }
      });
    });
  
    document.getElementById('tbl-month').addEventListener('click', function(e) {
      var cells = document.querySelectorAll('#tbl-month td');
      cells.forEach(function(cell) {
        cell.classList.remove('selected');
      });
  
      if (e.target.tagName === 'TD' && !e.target.classList.contains('disabled-date')) {
        e.target.classList.add('selected');
      }
    });
  });
  
  function prevMonth(date) {
    var target = new Date(date);
    target.setDate(1);
    target.setMonth(target.getMonth() - 1);
    return getYmd(target);
  }
  
  function nextMonth(date) {
    var target = new Date(date);
    target.setDate(1);
    target.setMonth(target.getMonth() + 1);
    return getYmd(target);
  }
  
  function getYmd(target) {
    var month = ('0' + (target.getMonth() + 1)).slice(-2);
    return [target.getFullYear(), month, '01'].join('-');
  }
  
  function drawMonth(date) {
    var tblMonth = document.getElementById('tbl-month'); // Get reference to the table
    tblMonth.innerHTML = '';
    var year = date.getFullYear();
    var month = date.getMonth();
    var weekdays = ['sun','mon','tue','wed','thu','fri','sat','sun'];
    
    var firstDay = new Date(year, month, 1).getDay();
    var lastDate = new Date(year, month + 1, 0).getDate();
    var headerRow = document.createElement('tr');
    for (var i = 0; i < 7; i++) {
      var dayHeader = document.createElement('th');
      dayHeader.textContent = weekdays[i];
      headerRow.appendChild(dayHeader);
    }
    tblMonth.appendChild(headerRow);
    var row = document.createElement('tr');
    var day = 1;
  
    // Fill the blank cells for the first week
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
  
      if (new Date(year, month, day).getDay() === 0) {
        cell.classList.add('sun');
      } else if (new Date(year, month, day).getDay() === 6) {
        cell.classList.add('sat');
      }
  
      if (new Date(year, month, day).toDateString() === new Date().toDateString()) {
        cell.classList.add('today');
      }

  
      row.appendChild(cell);
      day++;
    }
  
    var prevBtn = document.getElementById('month-prev');
    var nextBtn = document.getElementById('month-next');
  
    prevBtn.dataset.ym = prevMonth(date);
    nextBtn.dataset.ym = nextMonth(date);
  
    document.getElementById('month-this').textContent = year + '.' + ('0' + (month + 1)).slice(-2);
    tblMonth.appendChild(row);
  }
  

  