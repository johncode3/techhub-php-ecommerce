document.addEventListener('DOMContentLoaded', () => {
    const filterSidebar = document.querySelector('.filter-sidebar');
    if (!filterSidebar) {
        return;
    }

    const overlay = document.createElement('div');
    overlay.className = 'filter-overlay';
    document.body.appendChild(overlay);

    const closeBtn = document.createElement('button');
    closeBtn.className = 'filter-close';    z
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    filterSidebar.insertBefore(closeBtn, filterSidebar.firstChild);

    const mobileFilterBtn = document.querySelector('.mobile-filter-toggle');

    function closeFilters() {
        filterSidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    if (mobileFilterBtn) {
        mobileFilterBtn.addEventListener('click', () => {
            filterSidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        function updateMobileFilterButton(count) {
            if (count > 0) {
                mobileFilterBtn.innerHTML = `<i class="fas fa-filter"></i><span class="badge">${count}</span>`;
                mobileFilterBtn.style.background = '#ff6b6b';
            } else {
                mobileFilterBtn.innerHTML = '<i class="fas fa-filter"></i>';
                mobileFilterBtn.style.background = '#0066cc';
            }
        }

        const style = document.createElement('style');
        style.textContent = `
            .mobile-filter-toggle .badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #ff6b6b;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                font-size: 0.7rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        `;
        document.head.appendChild(style);
        updateMobileFilterButton(0);
    }

    closeBtn.addEventListener('click', closeFilters);
    overlay.addEventListener('click', closeFilters);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeFilters();
    });
});
