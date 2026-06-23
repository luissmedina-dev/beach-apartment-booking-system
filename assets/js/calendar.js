/**
 * calendar.js
 * Calendário de disponibilidade — public/availability.php
 *
 * Espera que o PHP injete no HTML, antes deste script:
 *   <script>
 *     window.CALENDAR_CONFIG = {
 *       bookedDates: [...],   // array YYYY-MM-DD vindas do PHP
 *       today: 'YYYY-MM-DD'  // date('Y-m-d') do servidor
 *     };
 *   </script>
 */
(function () {
    'use strict';

    const config = window.CALENDAR_CONFIG || { bookedDates: [], today: '' };
    const BOOKED = new Set(config.bookedDates);
    const TODAY  = config.today;

    const MESES  = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                    'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    const SEMANA = ['DOM','SEG','TER','QUA','QUI','SEX','SÁB'];

    const todayDate = new Date(TODAY + 'T00:00:00');
    let viewYear    = todayDate.getFullYear();
    let viewMonth   = todayDate.getMonth();

    const title   = document.getElementById('avTitle');
    const grid    = document.getElementById('avGrid');
    const btnPrev = document.getElementById('avPrev');
    const btnNext = document.getElementById('avNext');

    if (!title || !grid || !btnPrev || !btnNext) return; // página errada

    function toYMD(y, m, d) {
        return `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    }

    function render() {
        title.textContent = `${MESES[viewMonth]} ${viewYear}`;
        grid.innerHTML = '';

        SEMANA.forEach(function (d) {
            var el = document.createElement('div');
            el.className   = 'avail-weekday';
            el.textContent = d;
            grid.appendChild(el);
        });

        var firstDay    = new Date(viewYear, viewMonth, 1).getDay();
        var daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        for (var i = 0; i < firstDay; i++) {
            var empty = document.createElement('div');
            empty.className = 'avail-day avail-day--empty';
            grid.appendChild(empty);
        }

        for (var day = 1; day <= daysInMonth; day++) {
            var ymd    = toYMD(viewYear, viewMonth, day);
            var isPast = ymd < TODAY;
            var isBook = BOOKED.has(ymd);

            var el = document.createElement('div');
            el.className = 'avail-day';

            var num = document.createElement('span');
            num.className   = 'avail-day-num';
            num.textContent = day;
            el.appendChild(num);

            if (isPast) {
                el.classList.add('avail-day--past');
            } else if (isBook) {
                el.classList.add('avail-day--booked');
            } else {
                el.classList.add('avail-day--free');
                var dot = document.createElement('span');
                dot.className = 'avail-day-dot';
                el.appendChild(dot);
            }

            grid.appendChild(el);
        }
    }

    btnPrev.addEventListener('click', function () {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        render();
    });

    btnNext.addEventListener('click', function () {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        render();
    });

    render();
}());
