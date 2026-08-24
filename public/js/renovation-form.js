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
})();
