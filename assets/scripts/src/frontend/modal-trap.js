/* global document, window */

window.opi_jobs_theme = window.opi_jobs_theme || [];
window.opi_jobs_theme.functions = window.opi_jobs_theme.functions || [];

window.opi_jobs_theme.functions.modal_trap_focus = function(container) {
	var available_elements = [
		'a[href]',
		'button',
		'textarea',
		'input[type="email"]',
		'input[type="url"]',
		'input[type="number"]',
		'input[type="text"]',
		'input[type="radio"]',
		'input[type="checkbox"]',
		'select'
	];
	var focusable_elements, first_focusable_element, last_focusable_element;
	var KEYCODE_TAB = 9;
	var KEYCODE_ESC = 27;
	/**
	 * get focusable elements
	 */
	focusable_elements = '';
	available_elements.forEach( function( element ) {
		if ( focusable_elements ) {
			focusable_elements += ',';
		}
		focusable_elements += element;
		focusable_elements += ':not([disabled], [readonly])';
	});
	focusable_elements = container.querySelectorAll(focusable_elements);
	/**
	 * configure
	 */
	first_focusable_element = focusable_elements[0];
	last_focusable_element = focusable_elements[focusable_elements.length - 1];
	first_focusable_element.focus();
	/**
	 * bind
	 */
	container.addEventListener('keydown', function(e) {
		var is_tab_pressed = (e.key === 'Tab' || e.keyCode === KEYCODE_TAB);
		var is_esc_pressed = (e.key === 'Escape' || e.keyCode === KEYCODE_ESC);
		/**
		 * check escape
		 */
		if ( is_esc_pressed ) {
			container.remove();
			document.body.classList.remove( 'no-scroll');
			return;
		}
		/**
		 * check tab
		 */
		if (!is_tab_pressed) {
			return;
		}
		if (e.shiftKey) {
			if (document.activeElement === first_focusable_element) {
				last_focusable_element.focus();
				e.preventDefault();
			}
		} else {
			if (document.activeElement === last_focusable_element) {
				first_focusable_element.focus();
				e.preventDefault();
			}
		}
	});
};

window.opi_jobs_theme.functions.modal_close = function(element) {
	var KEYCODE_ESC = 27;

	element.addEventListener('keydown', function(e) {
		var isEscapePressed = (e.key === 'Escape' || e.keyCode === KEYCODE_ESC);

		if (!isEscapePressed) {
			return;
		}

		$(newsletterContainer).hide();
		$('#opi-newsletter-form-container').attr('aria-hidden', 'true');
	});
};

