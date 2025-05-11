/**
 * File aria-tabs.js
 *
 */
/* global document, window */
window.addEventListener('load', function(e) {
	var tablists = document.querySelectorAll('[role=tablist]');
	if ( 1 > tablists.length ) {
		return;
	}
	tablists.forEach( function( list ) {
		var tabs = list.querySelectorAll('[role=tab]');
		if ( 1 < tabs.length ) {
			tabs.forEach( function( tab ) {
				tab.addEventListener('click', function(event) {
					var id = this.getAttribute('id');
					event.preventDefault();
					if ( 'true' === this.getAttribute('aria-selected')) {
						return;
					}
					this.closest('[role=tablist]').querySelectorAll('[role=tab]').forEach( function( element ) {
						if ( id === element.getAttribute('id' ) ) {
							element.setAttribute('aria-selected', 'true');
							document.getElementById( element.getAttribute('aria-controls')).setAttribute('aria-expanded', 'true' );
						} else {
							element.setAttribute('aria-selected', 'false');
							document.getElementById( element.getAttribute('aria-controls')).setAttribute('aria-expanded', 'false' );
						}
					});
				});
			});
		}
	});
});


