const KiwiApp = (() => {
    const apiBase = 'class/api/ajax.php';

    async function request(action, payload = {}, method = 'POST') {
        const options = { method, headers: {} };

        if (method === 'GET') {
            const params = new URLSearchParams({ action, ...payload });
            const response = await fetch(`${apiBase}?${params.toString()}`);
            return response.json();
        }

        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify({ action, ...payload });
        const response = await fetch(apiBase, options);
        return response.json();
    }

    function showToast(message, isError = false) {
        let toast = document.getElementById('kiwi-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'kiwi-toast';
            toast.style.cssText = 'position:fixed;right:24px;bottom:24px;z-index:9999;padding:12px 18px;border-radius:10px;color:#fff;font-size:13px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.15);';
            document.body.appendChild(toast);
        }
        toast.style.background = isError ? '#ef4444' : '#059669';
        toast.textContent = message;
        toast.style.opacity = '1';
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => { toast.style.opacity = '0'; }, 2800);
    }

    function bindSidebarToggle() {
        const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
        if (!toggleSidebarBtn) return;
        toggleSidebarBtn.addEventListener('click', () => {
            const isMinimized = document.documentElement.classList.toggle('sidebar-minimized');
            localStorage.setItem('sidebarMinimized', isMinimized);
        });
    }

    function bindExportButtons() {
        document.querySelectorAll('[data-export-type]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const type = button.getAttribute('data-export-type');
                const format = button.getAttribute('data-export-format') || 'csv';
                const params = new URLSearchParams({ type, format });

                const searchInput = document.querySelector('[data-table-search]');
                const departmentSelect = document.querySelector('[data-filter-department]');
                const dateInput = document.querySelector('[data-filter-date]');

                if (searchInput && searchInput.value.trim()) params.set('search', searchInput.value.trim());
                if (departmentSelect && departmentSelect.value) params.set('department', departmentSelect.value);
                if (dateInput && dateInput.value) params.set('date', dateInput.value);

                window.location.href = `export.php?${params.toString()}`;
            });
        });
    }

    function bindTableSearch(callback) {
        const input = document.querySelector('[data-table-search]');
        if (!input || typeof callback !== 'function') return;

        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => callback(input.value.trim()), 250);
        });
    }

    function updateMetricCards(stats) {
        const map = {
            present: document.querySelector('[data-stat-present]'),
            late: document.querySelector('[data-stat-late]'),
            absent: document.querySelector('[data-stat-absent]'),
            avg_check_in: document.querySelector('[data-stat-avg-checkin]'),
        };

        if (map.present) map.present.textContent = stats.present ?? 0;
        if (map.late) map.late.textContent = stats.late ?? 0;
        if (map.absent) map.absent.textContent = stats.absent ?? 0;
        if (map.avg_check_in) map.avg_check_in.textContent = stats.avg_check_in ?? '--:-- --';
    }

    function formatTime(value) {
        if (!value || value === '00:00:00') return '--:--';
        const date = new Date(`1970-01-01T${value}`);
        if (Number.isNaN(date.getTime())) return value;
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    return {
        request,
        showToast,
        bindSidebarToggle,
        bindExportButtons,
        bindTableSearch,
        updateMetricCards,
        formatTime,
        escapeHtml,
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    KiwiApp.bindSidebarToggle();
    KiwiApp.bindExportButtons();
});
