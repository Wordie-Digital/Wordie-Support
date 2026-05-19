/**
 * Wordie — navigation.js
 *
 * Mobile nav toggle. Toggles .is-open on .site-header__nav
 * and updates aria-expanded on the toggle button.
 */

( function () {

	const toggle = document.querySelector( '.site-header__menu-toggle' );
	const nav    = document.querySelector( '.site-header__nav' );

	if ( ! toggle || ! nav ) { return; }

	toggle.addEventListener( 'click', function () {
		const isOpen = nav.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );

	// Close on Escape
	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && nav.classList.contains( 'is-open' ) ) {
			nav.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			toggle.focus();
		}
	} );

	// Close when clicking outside the nav
	document.addEventListener( 'click', function ( e ) {
		const header = document.querySelector( '.site-header' );
		if ( nav.classList.contains( 'is-open' ) && header && ! header.contains( e.target ) ) {
			nav.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		}
	} );

} )();
