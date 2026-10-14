const sidebar = document.getElementById('adminSidebar');
const menuButton = document.getElementById('adminMobileMenu');

if (sidebar && menuButton) {
    menuButton.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('is-open');

        menuButton.setAttribute(
            'aria-expanded',
            String(isOpen)
        );
    });
}