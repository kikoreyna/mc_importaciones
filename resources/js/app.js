import './bootstrap';

const navbarToggle = document.querySelector('[data-navbar-toggle]');
const navbarLinks = document.getElementById('navbar-links');

if (navbarToggle && navbarLinks) {
	const closeNavbar = () => {
		navbarLinks.classList.add('hidden');
		navbarToggle.setAttribute('aria-expanded', 'false');
		navbarToggle.setAttribute('aria-label', 'Abrir menú');
	};

	navbarToggle.addEventListener('click', () => {
		const isExpanded = navbarToggle.getAttribute('aria-expanded') === 'true';

		navbarLinks.classList.toggle('hidden', isExpanded);
		navbarToggle.setAttribute('aria-expanded', String(!isExpanded));
		navbarToggle.setAttribute('aria-label', isExpanded ? 'Abrir menú' : 'Cerrar menú');
	});

	navbarLinks.addEventListener('click', (event) => {
		if (event.target.closest('a')) {
			closeNavbar();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && navbarToggle.getAttribute('aria-expanded') === 'true') {
			closeNavbar();
			navbarToggle.focus();
		}
	});
}
