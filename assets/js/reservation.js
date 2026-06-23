/**
 * reservation.js
 * Calendário interativo de seleção de datas — public/request_reservation.php
 *
 * Espera que o PHP injete no HTML, antes deste script:
 *   <script>
 *     window.RESERVATION_CONFIG = {
 *       bookedDates: [...],   // array YYYY-MM-DD
 *       today: 'YYYY-MM-DD',
 *       dailyPrice: 300
 *     };
 *   </script>
 */
(function () {
    'use strict';

    var config      = window.RESERVATION_CONFIG || { bookedDates: [], today: '', dailyPrice: 300 };
    var DAILY_PRICE = config.dailyPrice || 300;
    var TODAY_STR   = config.today;
    var BOOKED      = new Set(config.bookedDates);

    var MESES     = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                     'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    var DIAS_SEM  = ['DOM','SEG','TER','QUA','QUI','SEX','SÁB'];

    var today     = new Date(TODAY_STR + 'T00:00:00');
    var viewYear  = today.getFullYear();
    var viewMonth = today.getMonth();

    var checkinDate  = null;
    var checkoutDate = null;

    // Elementos
    var calTitle        = document.getElementById('calTitle');
    var calGrid         = document.getElementById('calGrid');
    var btnPrev         = document.getElementById('calPrev');
    var btnNext         = document.getElementById('calNext');
    var btnClear        = document.getElementById('calClear');
    var inputCheckin    = document.getElementById('checkin');
    var inputCheckout   = document.getElementById('checkout');
    var displayCheckin  = document.getElementById('displayCheckin');
    var displayCheckout = document.getElementById('displayCheckout');
    var resPreview      = document.getElementById('resPreview');
    var resSubmit       = document.getElementById('resSubmit');

    if (!calTitle || !calGrid) return;

    function toYMD(d) {
        return d.getFullYear() + '-' +
               String(d.getMonth() + 1).padStart(2, '0') + '-' +
               String(d.getDate()).padStart(2, '0');
    }

    function toBR(d) {
        return String(d.getDate()).padStart(2, '0') + '/' +
               String(d.getMonth() + 1).padStart(2, '0') + '/' +
               d.getFullYear();
    }

    function isBefore(a, b) { return toYMD(a) < toYMD(b); }
    function isSame(a, b)   { return toYMD(a) === toYMD(b); }

    function rangeHasBooked(start, end) {
        var cur = new Date(start);
        while (isBefore(cur, end)) {
            if (BOOKED.has(toYMD(cur))) return true;
            cur.setDate(cur.getDate() + 1);
        }
        return false;
    }

    function applySelectionStyle(el, date) {
        if (!el.classList.contains('cal-cell--free')) return;
        el.classList.remove('cal-cell--checkin','cal-cell--checkout','cal-cell--range');

        if (checkinDate && isSame(date, checkinDate)) {
            el.classList.add('cal-cell--checkin');
        } else if (checkoutDate && isSame(date, checkoutDate)) {
            el.classList.add('cal-cell--checkout');
        } else if (checkinDate && checkoutDate &&
                   !isBefore(date, checkinDate) && isBefore(date, checkoutDate)) {
            el.classList.add('cal-cell--range');
        }
    }

    function renderCalendar() {
        calTitle.textContent = MESES[viewMonth] + ' ' + viewYear;
        calGrid.innerHTML = '';

        DIAS_SEM.forEach(function (d) {
            var el = document.createElement('div');
            el.className   = 'cal-weekday';
            el.textContent = d;
            calGrid.appendChild(el);
        });

        var firstDay    = new Date(viewYear, viewMonth, 1).getDay();
        var daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        for (var i = 0; i < firstDay; i++) {
            var empty = document.createElement('div');
            empty.className = 'cal-cell cal-cell--empty';
            calGrid.appendChild(empty);
        }

        for (var day = 1; day <= daysInMonth; day++) {
            (function (d) {
                var date  = new Date(viewYear, viewMonth, d);
                var ymd   = toYMD(date);
                var past  = ymd < TODAY_STR;
                var book  = BOOKED.has(ymd);

                var el  = document.createElement('div');
                el.className      = 'cal-cell';
                el.dataset.date   = ymd;

                var num = document.createElement('span');
                num.className   = 'cal-cell-num';
                num.textContent = d;
                el.appendChild(num);

                if (past) {
                    el.classList.add('cal-cell--past');
                } else if (book) {
                    el.classList.add('cal-cell--booked');
                } else {
                    el.classList.add('cal-cell--free');
                    var dot = document.createElement('span');
                    dot.className = 'cal-cell-dot';
                    el.appendChild(dot);
                    el.addEventListener('click', function () { onDayClick(date); });
                }

                applySelectionStyle(el, date);
                calGrid.appendChild(el);
            }(day));
        }
    }

    function onDayClick(date) {
        if (!checkinDate || (checkinDate && checkoutDate)) {
            checkinDate  = date;
            checkoutDate = null;
            updateUI();
            return;
        }
        if (isBefore(date, checkinDate) || isSame(date, checkinDate)) {
            checkinDate  = date;
            checkoutDate = null;
            updateUI();
            return;
        }
        if (rangeHasBooked(checkinDate, date)) {
            alert('Há datas reservadas dentro do período selecionado. Escolha outro intervalo.');
            return;
        }
        checkoutDate = date;
        updateUI();
    }

    function updateUI() {
        if (inputCheckin)  inputCheckin.value  = checkinDate  ? toYMD(checkinDate)  : '';
        if (inputCheckout) inputCheckout.value = checkoutDate ? toYMD(checkoutDate) : '';

        if (displayCheckin)  displayCheckin.textContent  = checkinDate  ? toBR(checkinDate)  : '—';
        if (displayCheckout) displayCheckout.textContent = checkoutDate ? toBR(checkoutDate) : '—';

        var checkinField  = document.getElementById('calCheckinDisplay');
        var checkoutField = document.getElementById('calCheckoutDisplay');
        if (checkinField)  checkinField.classList.toggle('cal-date-field--active',  !!checkinDate);
        if (checkoutField) checkoutField.classList.toggle('cal-date-field--active', !!checkoutDate);

        if (checkinDate && checkoutDate) {
            var days  = Math.round((checkoutDate - checkinDate) / 86400000);
            var total = days * DAILY_PRICE;
            var nightsEl = document.getElementById('previewNights');
            var totalEl  = document.getElementById('previewTotal');
            var boldEl   = document.getElementById('previewTotalBold');
            if (nightsEl) nightsEl.textContent = days + ' noite' + (days !== 1 ? 's' : '');
            if (totalEl)  totalEl.textContent  = 'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
            if (boldEl)   boldEl.textContent   = 'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
            if (resPreview) resPreview.style.display = 'block';
            if (resSubmit)  resSubmit.disabled = false;
        } else {
            if (resPreview) resPreview.style.display = 'none';
            if (resSubmit)  resSubmit.disabled = true;
        }

        renderCalendar();
    }

    btnPrev && btnPrev.addEventListener('click', function () {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        renderCalendar();
    });

    btnNext && btnNext.addEventListener('click', function () {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        renderCalendar();
    });

    btnClear && btnClear.addEventListener('click', function () {
        checkinDate  = null;
        checkoutDate = null;
        updateUI();
    });

    renderCalendar();
}());
