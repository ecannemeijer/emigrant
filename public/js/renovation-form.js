(function () {
    function pad(n) {
        return String(n).padStart(2, '0');
    }
    function syncPlannedDate() {
        const dayEl = document.getElementById('item_day');
        const monthEl = document.getElementById('item_month');
        const yearEl = document.getElementById('item_year');
        const hidden = document.getElementById('item_date');
        if (!dayEl || !monthEl || !yearEl || !hidden) return;
        const d = parseInt(dayEl.value, 10);
        const m = parseInt(monthEl.value, 10);
        const y = parseInt(yearEl.value, 10);
        if (y && m && d) {
            const last = new Date(y, m, 0).getDate();
            const day = Math.min(d, last);
            if (day !== d) dayEl.value = String(day);
            hidden.value = y + '-' + pad(m) + '-' + pad(day);
        } else {
            hidden.value = '';
        }
    }
    function ensureOption(select, value) {
        if (!value) return;
        const exists = Array.prototype.some.call(select.options, function (o) { return o.value === String(value); });
        if (!exists) {
            const opt = document.createElement('option');
            opt.value = String(value);
            opt.textContent = String(value);
            select.appendChild(opt);
        }
    }
    function setPlannedParts(dateStr, yearOnly) {
        const dayEl = document.getElementById('item_day');
        const monthEl = document.getElementById('item_month');
        const yearEl = document.getElementById('item_year');
        if (!dayEl) return;
        dayEl.value = '';
        monthEl.value = '';
        yearEl.value = '';
        if (dateStr && /^\d{4}-\d{2}-\d{2}/.test(dateStr)) {
            const parts = dateStr.slice(0, 10).split('-');
            const y = String(parseInt(parts[0], 10));
            const m = String(parseInt(parts[1], 10));
            const d = String(parseInt(parts[2], 10));
            ensureOption(yearEl, y);
            yearEl.value = y;
            monthEl.value = m;
            dayEl.value = d;
        } else if (yearOnly) {
            ensureOption(yearEl, String(yearOnly));
            yearEl.value = String(yearOnly);
        }
        syncPlannedDate();
    }

    window.renoSyncPlannedDate = syncPlannedDate;
    window.renoSetPlannedParts = setPlannedParts;

    window.renoNewItem = function (room, dateStr) {
        document.getElementById('itemModalTitle').textContent = 'Nieuwe verbouwpost';
        document.getElementById('item_id').value = '';
        document.getElementById('item_title').value = '';
        const roomSelect = document.getElementById('item_room');
        if (room) {
            roomSelect.value = room;
        } else {
            roomSelect.selectedIndex = 0;
        }
        document.getElementById('item_status').value = 'planned';
        document.getElementById('item_priority').value = 'medium';
        document.getElementById('item_estimated').value = 0;
        document.getElementById('item_actual').value = 0;
        document.getElementById('item_vat').value = '10';
        document.getElementById('item_contractor').value = '';
        setPlannedParts(dateStr || '', null);
        document.getElementById('item_capital').checked = true;
    };

    window.renoFillItem = function (item) {
        document.getElementById('itemModalTitle').textContent = 'Verbouwpost bewerken';
        document.getElementById('item_id').value = item.id;
        document.getElementById('item_title').value = item.title || '';
        document.getElementById('item_room').value = item.room || 'Overig';
        document.getElementById('item_status').value = item.status || 'planned';
        document.getElementById('item_priority').value = item.priority || 'medium';
        document.getElementById('item_estimated').value = item.estimated_cost || 0;
        document.getElementById('item_actual').value = item.actual_cost || 0;
        document.getElementById('item_vat').value = String(parseFloat(item.vat_rate || 10));
        document.getElementById('item_contractor').value = item.contractor || '';
        setPlannedParts(item.planned_date || '', item.planned_year || null);
        document.getElementById('item_capital').checked = String(item.include_in_capital) === '1';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('itemModal')).show();
    };

    window.renoEditItem = function (btn) {
        window.renoFillItem(JSON.parse(btn.getAttribute('data-item')));
    };

    window.renoNewCategory = function () {
        document.getElementById('categoryModalTitle').textContent = 'Nieuwe categorie';
        document.getElementById('category_id').value = '';
        document.getElementById('category_name').value = '';
    };

    window.renoEditCategory = function (id, name) {
        document.getElementById('categoryModalTitle').textContent = 'Categorie hernoemen';
        document.getElementById('category_id').value = id;
        document.getElementById('category_name').value = name;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryModal')).show();
    };

    ['item_day', 'item_month', 'item_year'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', syncPlannedDate);
    });

    function padTime(t) {
        if (!t) return '09:00';
        const parts = String(t).split(':');
        return String(parts[0] || '09').padStart(2, '0') + ':' + String(parts[1] || '00').padStart(2, '0');
    }
    function toggleAptTimes() {
        const allDay = document.getElementById('apt_all_day');
        const wrap = document.getElementById('apt_times');
        if (!allDay || !wrap) return;
        wrap.style.display = allDay.checked ? 'none' : '';
    }
    function aptFromForm() {
        return {
            id: document.getElementById('apt_id').value,
            title: document.getElementById('apt_title').value || 'Afspraak',
            date: document.getElementById('apt_date').value,
            all_day: document.getElementById('apt_all_day').checked ? 1 : 0,
            start_time: document.getElementById('apt_start').value || '09:00',
            end_time: document.getElementById('apt_end').value || '10:00',
            location: document.getElementById('apt_location').value || '',
            description: document.getElementById('apt_description').value || '',
        };
    }
    function googleDates(apt) {
        const day = apt.date.replace(/-/g, '');
        if (apt.all_day) {
            const d = new Date(apt.date + 'T12:00:00');
            d.setDate(d.getDate() + 1);
            const next = d.getFullYear() + String(d.getMonth() + 1).padStart(2, '0') + String(d.getDate()).padStart(2, '0');
            return day + '/' + next;
        }
        const start = (apt.start_time || '09:00').replace(':', '') + '00';
        const end = (apt.end_time || '10:00').replace(':', '') + '00';
        return day + 'T' + start + '/' + day + 'T' + end;
    }
    function googleTemplateUrl(apt) {
        const params = new URLSearchParams({
            action: 'TEMPLATE',
            text: apt.title,
            dates: googleDates(apt),
            details: apt.description,
            location: apt.location,
            ctz: 'Europe/Amsterdam',
        });
        return 'https://calendar.google.com/calendar/render?' + params.toString();
    }
    function openGoogleFromForm() {
        const apt = aptFromForm();
        if (!apt.date) return false;
        window.open(googleTemplateUrl(apt), '_blank', 'noopener,noreferrer');
        return true;
    }
    function syncAptLinks() {
        const id = document.getElementById('apt_id').value;
        const ics = document.getElementById('aptIcsBtn');
        const del = document.getElementById('aptDeleteBtn');
        if (ics) {
            ics.classList.toggle('d-none', !id);
            ics.href = id ? '/renovation/appointment/ics/' + id : '#';
        }
        if (del) del.classList.toggle('d-none', !id);
    }

    window.renoNewAppointment = function (dateStr, hour) {
        const modal = document.getElementById('appointmentModal');
        if (!modal) return;
        document.getElementById('appointmentModalTitle').textContent = 'Nieuwe afspraak';
        document.getElementById('apt_id').value = '';
        document.getElementById('apt_title').value = '';
        document.getElementById('apt_date').value = dateStr || new Date().toISOString().slice(0, 10);
        document.getElementById('apt_all_day').checked = false;
        const hourNum = (hour || hour === 0) ? hour : 9;
        const startH = String(hourNum).padStart(2, '0') + ':00';
        const endH = String(Math.min(23, hourNum + 1)).padStart(2, '0') + ':00';
        document.getElementById('apt_start').value = startH;
        document.getElementById('apt_end').value = endH;
        document.getElementById('apt_location').value = '';
        document.getElementById('apt_description').value = '';
        const og = document.getElementById('apt_open_google');
        if (og) og.checked = false;
        toggleAptTimes();
        syncAptLinks();
        bootstrap.Modal.getOrCreateInstance(modal).show();
    };

    window.renoFillAppointment = function (apt) {
        const modal = document.getElementById('appointmentModal');
        if (!modal) return;
        const start = String(apt.starts_at || '').replace(' ', 'T');
        const end = String(apt.ends_at || '').replace(' ', 'T');
        document.getElementById('appointmentModalTitle').textContent = 'Afspraak bewerken';
        document.getElementById('apt_id').value = apt.id || '';
        document.getElementById('apt_title').value = apt.title || '';
        document.getElementById('apt_date').value = start.slice(0, 10);
        document.getElementById('apt_all_day').checked = String(apt.all_day) === '1' || apt.all_day === 1;
        document.getElementById('apt_start').value = padTime(start.slice(11, 16));
        document.getElementById('apt_end').value = padTime(end.slice(11, 16) || start.slice(11, 16));
        document.getElementById('apt_location').value = apt.location || '';
        document.getElementById('apt_description').value = apt.description || '';
        toggleAptTimes();
        syncAptLinks();
        bootstrap.Modal.getOrCreateInstance(modal).show();
    };

    const aptAllDay = document.getElementById('apt_all_day');
    if (aptAllDay) aptAllDay.addEventListener('change', toggleAptTimes);
    const aptGoogle = document.getElementById('aptGoogleBtn');
    if (aptGoogle) aptGoogle.addEventListener('click', openGoogleFromForm);
    const aptForm = document.getElementById('appointmentForm');
    if (aptForm) {
        aptForm.addEventListener('submit', function () {
            const openGoogle = document.getElementById('apt_open_google');
            if (openGoogle && openGoogle.checked) {
                openGoogleFromForm();
            }
        });
    }
    const aptDel = document.getElementById('aptDeleteBtn');
    if (aptDel) {
        aptDel.addEventListener('click', function () {
            const id = document.getElementById('apt_id').value;
            if (!id || !confirm('Deze afspraak verwijderen?')) return;
            const form = document.getElementById('aptDeleteForm');
            form.action = '/renovation/appointment/delete/' + id;
            form.submit();
        });
    }
})();
