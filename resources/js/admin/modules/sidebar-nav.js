const MOBILE_BREAKPOINT = 1024;

export const setupSidebar = () => {
    const sidebar = document.querySelector('[data-admin-sidebar]');
    const toggle = document.querySelector('[data-admin-sidebar-toggle]');
    const backdrop = document.querySelector('[data-admin-sidebar-backdrop]');

    if (sidebar) {
        // 群組展開 / 收合
        sidebar.querySelectorAll('[data-admin-nav-toggle]').forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                const group = btn.closest('[data-admin-nav-group]');
                if (!group) return;
                const isOpen = group.classList.toggle('is-open');
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });
    }

    if (!toggle || !sidebar) {
        return;
    }

    const closeDrawer = () => {
        sidebar.classList.remove('is-mobile-open');
        toggle.setAttribute('aria-expanded', 'false');
        backdrop?.classList.remove('is-visible');
        document.body.style.removeProperty('overflow');
    };

    const openDrawer = () => {
        sidebar.classList.add('is-mobile-open');
        toggle.setAttribute('aria-expanded', 'true');
        backdrop?.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
    };

    toggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.contains('is-mobile-open');
        if (isOpen) {
            closeDrawer();
        } else {
            openDrawer();
        }
    });

    backdrop?.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebar.classList.contains('is-mobile-open')) {
            closeDrawer();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= MOBILE_BREAKPOINT) {
            closeDrawer();
        }
    });
};
