(() => {
    const header = document.querySelector('.header');
    const toggle = header?.querySelector('.menu-toggle');
    const navigation = header?.querySelector('#main-navigation');
    if (!toggle || !navigation) return;

    const mobile = window.matchMedia('(max-width: 700px)');
    const setOpen = (open, restoreFocus = false) => {
        header.classList.toggle('menu-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
        if (restoreFocus) toggle.focus();
    };

    header.dataset.menuReady = 'true';
    toggle.hidden = false;
    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    navigation.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!link || !mobile.matches) return;
        setOpen(false);
        const destination = document.querySelector(link.getAttribute('href'));
        if (destination) {
            destination.setAttribute('tabindex', '-1');
            destination.focus({ preventScroll: true });
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') setOpen(false, true);
    });
    document.addEventListener('click', (event) => {
        if (!header.contains(event.target)) setOpen(false);
    });
    header.addEventListener('focusout', (event) => {
        if (!header.contains(event.relatedTarget)) setOpen(false);
    });
    mobile.addEventListener('change', () => setOpen(false));
})();
