(function () {
    const DAYS = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];
    const DAYS_LONG = ['maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag', 'zondag'];
    const MONTHS = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
    const HOURS = [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
    const root = document.getElementById('renoCalendar');
    const titleEl = document.getElementById('renoCalTitle');
    const unscheduledEl = document.getElementById('renoCalUnscheduled');
    if (!root || !titleEl) return;

    const items = window.renoCalItems || [];
    const cats = window.renoCalCats || [];
    let view = 'month';
    let cursor = new Date();
    cursor.setHours(12, 0, 0, 0);

    function ymd(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    function parseDate(item) {
        return item.planned_date ? String(item.planned_date).slice(0, 10) : null;
    }
    function mondayOf(d) {
        const x = new Date(d);
        const day = (x.getDay() + 6) % 7;
        x.setDate(x.getDate() - day);
        x.setHours(12, 0, 0, 0);
        return x;
    }
    function itemsOn(dateStr) {
        return items.filter(function (it) { return parseDate(it) === dateStr; });
    }
    function catFor(name) {
        return cats.find(function (c) { return c.name === name; }) || null;
    }
    function escapeHtml(s) {
        return String(s || '').replace(/[&<>"']/g, function (c) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]);
        });
    }
    function colorClass(status) {
        const map = {
            planned: 'blue',
            quoted: 'yellow',
            in_progress: 'orange',
            done: 'green',
            skipped: 'red',
        };
        return map[status] || 'blue';
    }
    function chipHtml(item) {
        const payload = encodeURIComponent(JSON.stringify(item));
        const cat = item.room ? '<span class="cal-chip-cat">' + escapeHtml(item.room) + '</span> ' : '';
        return '<button type="button" class="cal-chip cal-color-' + colorClass(item.status) + '" data-payload="' + payload + '">' +
            cat + escapeHtml(item.title || 'Post') + '</button>';
    }
    function bindChip(btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const item = JSON.parse(decodeURIComponent(this.dataset.payload));
            if (e.target.classList.contains('cal-chip-cat')) {
                const cat = catFor(item.room);
                if (cat && window.renoEditCategory) {
                    window.renoEditCategory(cat.id, cat.name);
                    return;
                }
            }
            if (window.renoFillItem) window.renoFillItem(item);
        });
    }

    function openDate(dateStr) {
        const list = itemsOn(dateStr);
        if (list.length === 1) {
            window.renoFillItem(list[0]);
            return;
        }
        window.renoNewItem('', dateStr);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('itemModal')).show();
    }

    function render() {
        const y = cursor.getFullYear();
        const m = cursor.getMonth();
        if (view === 'agenda') {
            renderAgenda();
        } else if (view === 'year') {
            titleEl.textContent = String(y);
            let html = '<div class="cal-year">';
            for (let mi = 0; mi < 12; mi++) html += miniMonth(y, mi);
            html += '</div>';
            root.innerHTML = html;
        } else if (view === 'week') {
            renderWeek();
        } else if (view === 'day') {
            renderDay();
        } else {
            titleEl.textContent = MONTHS[m] + ' ' + y;
            const first = new Date(y, m, 1);
            const start = mondayOf(first);
            let html = '<div class="cal-month-head">' + DAYS.map(function (d) { return '<div>' + d + '</div>'; }).join('') + '</div><div class="cal-month">';
            for (let i = 0; i < 42; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                html += dayCell(d, false, d.getMonth() !== m);
            }
            html += '</div>';
            root.innerHTML = html;
        }
        bindGrid();
        renderUnscheduled();
        setViewButtons();
    }

    function dayCell(d, large, muted) {
        const key = ymd(d);
        const today = ymd(new Date()) === key;
        const list = itemsOn(key);
        let extra = '';
        if (large || view === 'month') {
            extra = list.slice(0, large ? 12 : 4).map(chipHtml).join('') +
                (!large && list.length > 4 ? '<span class="cal-more">+' + (list.length - 4) + '</span>' : '');
        }
        return '<div class="cal-day' + (muted ? ' muted' : '') + (today ? ' today' : '') + (list.length ? ' has-items' : '') + '" data-date="' + key + '">' +
            '<span class="cal-num">' + d.getDate() + '</span>' + extra + '</div>';
    }

    function miniMonth(year, month) {
        const first = new Date(year, month, 1);
        const start = mondayOf(first);
        let html = '<div class="cal-mini"><h3>' + MONTHS[month] + '</h3><div class="cal-mini-grid">';
        DAYS.forEach(function (d) { html += '<span class="cal-mini-dow">' + d.charAt(0) + '</span>'; });
        for (let i = 0; i < 42; i++) {
            const d = new Date(start);
            d.setDate(start.getDate() + i);
            const outside = d.getMonth() !== month;
            const key = ymd(d);
            const list = itemsOn(key);
            const cls = ['cal-mini-day'];
            if (outside) cls.push('muted');
            if (ymd(new Date()) === key) cls.push('today');
            if (list.length) cls.push('has-items', 'cal-color-' + colorClass(list[0].status));
            html += '<button type="button" class="' + cls.join(' ') + '" data-date="' + key + '" title="' + escapeHtml(list.map(function (it) { return it.title; }).join(', ')) + '">' +
                d.getDate() + (list.length > 1 ? '<i>' + list.length + '</i>' : '') + '</button>';
        }
        html += '</div></div>';
        return html;
    }

    function renderWeek() {
        const start = mondayOf(cursor);
        const end = new Date(start);
        end.setDate(start.getDate() + 6);
        titleEl.textContent = start.getDate() + ' ' + MONTHS[start.getMonth()] + ' – ' + end.getDate() + ' ' + MONTHS[end.getMonth()] + ' ' + end.getFullYear();
        let head = '<div class="cal-time-head"><div class="cal-gutter"></div>';
        let allDay = '<div class="cal-allday"><div class="cal-gutter">Hele dag</div>';
        for (let i = 0; i < 7; i++) {
            const d = new Date(start);
            d.setDate(start.getDate() + i);
            const key = ymd(d);
            const today = ymd(new Date()) === key;
            head += '<div class="' + (today ? 'today' : '') + '"><span>' + DAYS_LONG[i] + '</span><strong>' + d.getDate() + '</strong></div>';
            allDay += '<div class="cal-allday-col" data-date="' + key + '">' + itemsOn(key).map(chipHtml).join('') + '</div>';
        }
        head += '</div>';
        allDay += '</div>';
        let grid = '<div class="cal-time-grid">';
        HOURS.forEach(function (h) {
            grid += '<div class="cal-time-row"><div class="cal-gutter">' + String(h).padStart(2, '0') + ':00</div>';
            for (let i = 0; i < 7; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                grid += '<div class="cal-slot" data-date="' + ymd(d) + '"></div>';
            }
            grid += '</div>';
        });
        grid += '</div>';
        root.innerHTML = head + allDay + grid;
    }

    function renderDay() {
        const key = ymd(cursor);
        titleEl.textContent = DAYS_LONG[(cursor.getDay() + 6) % 7] + ' ' + cursor.getDate() + ' ' + MONTHS[cursor.getMonth()] + ' ' + cursor.getFullYear();
        let html = '<div class="cal-allday cal-allday-one"><div class="cal-gutter">Hele dag</div><div class="cal-allday-col" data-date="' + key + '">' +
            itemsOn(key).map(chipHtml).join('') + '</div></div><div class="cal-time-grid cal-time-grid-one">';
        HOURS.forEach(function (h) {
            html += '<div class="cal-time-row"><div class="cal-gutter">' + String(h).padStart(2, '0') + ':00</div><div class="cal-slot" data-date="' + key + '"></div></div>';
        });
        html += '</div>';
        root.innerHTML = html;
    }

    function renderAgenda() {
        titleEl.textContent = 'Agenda';
        const dated = items.filter(function (it) { return parseDate(it); })
            .slice()
            .sort(function (a, b) { return parseDate(a).localeCompare(parseDate(b)); });
        if (!dated.length) {
            root.innerHTML = '<p class="text-muted mb-0 p-3">Nog geen posten met een volledige datum. Kies bij een post dag, maand en jaar.</p>';
            return;
        }
        const groups = {};
        dated.forEach(function (it) {
            const k = parseDate(it);
            if (!groups[k]) groups[k] = [];
            groups[k].push(it);
        });
        let html = '<div class="cal-agenda">';
        Object.keys(groups).forEach(function (k) {
            const d = new Date(k + 'T12:00:00');
            html += '<div class="cal-agenda-day"><div class="cal-agenda-date">' +
                '<strong>' + d.getDate() + ' ' + MONTHS[d.getMonth()] + '</strong>' +
                '<span>' + DAYS_LONG[(d.getDay() + 6) % 7] + ' ' + d.getFullYear() + '</span></div><div class="cal-agenda-list">';
            groups[k].forEach(function (it) { html += chipHtml(it); });
            html += '</div></div>';
        });
        html += '</div>';
        root.innerHTML = html;
    }

    function bindGrid() {
        root.querySelectorAll('.cal-chip').forEach(bindChip);
        root.querySelectorAll('.cal-day, .cal-allday-col, .cal-slot').forEach(function (cell) {
            cell.addEventListener('click', function (e) {
                if (e.target.closest('.cal-chip')) return;
                if (this.dataset.date) openDate(this.dataset.date);
            });
        });
        root.querySelectorAll('.cal-mini-day').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const dateStr = this.dataset.date;
                const list = itemsOn(dateStr);
                if (list.length === 1) {
                    window.renoFillItem(list[0]);
                    return;
                }
                if (list.length > 1) {
                    cursor = new Date(dateStr + 'T12:00:00');
                    view = 'month';
                    render();
                    return;
                }
                openDate(dateStr);
            });
        });
    }

    function renderUnscheduled() {
        if (!unscheduledEl) return;
        const loose = items.filter(function (it) { return !parseDate(it); });
        if (!loose.length) {
            unscheduledEl.innerHTML = '';
            return;
        }
        unscheduledEl.innerHTML = '<p class="small mb-2">Nog zonder volledige datum — klik om dag, maand en jaar te zetten</p><div class="d-flex flex-wrap gap-2">' +
            loose.map(chipHtml).join('') + '</div>';
        unscheduledEl.querySelectorAll('.cal-chip').forEach(bindChip);
    }

    function setViewButtons() {
        document.querySelectorAll('.reno-cal-view').forEach(function (b) {
            b.classList.toggle('active', b.dataset.view === view);
        });
    }

    document.querySelectorAll('.reno-cal-view').forEach(function (b) {
        b.addEventListener('click', function () {
            view = this.dataset.view;
            render();
        });
    });
    document.getElementById('renoCalPrev').addEventListener('click', function () {
        if (view === 'year' || view === 'agenda') cursor.setFullYear(cursor.getFullYear() - 1);
        else if (view === 'week') cursor.setDate(cursor.getDate() - 7);
        else if (view === 'day') cursor.setDate(cursor.getDate() - 1);
        else cursor.setMonth(cursor.getMonth() - 1);
        render();
    });
    document.getElementById('renoCalNext').addEventListener('click', function () {
        if (view === 'year' || view === 'agenda') cursor.setFullYear(cursor.getFullYear() + 1);
        else if (view === 'week') cursor.setDate(cursor.getDate() + 7);
        else if (view === 'day') cursor.setDate(cursor.getDate() + 1);
        else cursor.setMonth(cursor.getMonth() + 1);
        render();
    });
    document.getElementById('renoCalToday').addEventListener('click', function () {
        cursor = new Date();
        cursor.setHours(12, 0, 0, 0);
        render();
    });
    const addBtn = document.getElementById('renoCalAdd');
    if (addBtn) {
        addBtn.addEventListener('click', function () {
            window.renoNewItem('', ymd(cursor));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('itemModal')).show();
        });
    }

    render();
})();
