const btn = document.getElementById('userMenuButton');
const menu = document.getElementById('userMenu');

if (btn) {
    btn.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
}
