(() => {
    const sidebar = document.getElementById('sidebar');
    const menuButton = document.getElementById('mobileMenuButton');
    const scrim = document.getElementById('sidebarScrim');

    function setMenuOpen(isOpen) {
        sidebar?.classList.toggle('is-open', isOpen);
        scrim?.classList.toggle('is-visible', isOpen);
        menuButton?.setAttribute('aria-expanded', String(isOpen));
    }

    menuButton?.addEventListener('click', () => {
        const currentlyOpen = sidebar?.classList.contains('is-open') ?? false;
        setMenuOpen(!currentlyOpen);
    });

    scrim?.addEventListener('click', () => setMenuOpen(false));
    window.addEventListener('resize', () => {
        if (window.innerWidth > 920) setMenuOpen(false);
    });

    const exportButton = document.getElementById('exportOrdersButton');
    const ordersTable = document.getElementById('recentOrdersTable');

    exportButton?.addEventListener('click', () => {
        if (!ordersTable) return;

        const rows = Array.from(ordersTable.querySelectorAll('tr')).map((row) =>
            Array.from(row.querySelectorAll('th, td')).map((cell) => {
                const value = cell.innerText.trim().replace(/\s+/g, ' ');
                return `"${value.replace(/"/g, '""')}"`;
            }).join(',')
        );
        const csv = '\uFEFF' + rows.join('\r\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'harvest-bread-sample-orders.csv';
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    });
})();
