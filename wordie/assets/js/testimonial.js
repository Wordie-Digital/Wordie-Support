/* ==========================================================================
   Testimonial — carousel navigation
   ========================================================================== */

( function () {
	'use strict';

	document.querySelectorAll( '[data-testimonial]' ).forEach( function ( section ) {
		const slides = Array.from( section.querySelectorAll( '.block-testimonial__slide' ) );
		if ( slides.length < 2 ) { return; }

		let current = 0;

		function goTo( index ) {
			const total = slides.length;
			index = ( ( index % total ) + total ) % total;

			slides.forEach( function ( slide, i ) {
				const active = i === index;
				slide.classList.toggle( 'is-active', active );
				slide.setAttribute( 'aria-hidden', active ? 'false' : 'true' );
			} );

			current = index;
		}

		section.querySelectorAll( '[data-prev]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () { goTo( current - 1 ); } );
		} );

		section.querySelectorAll( '[data-next]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () { goTo( current + 1 ); } );
		} );

		section.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowLeft' )  { e.preventDefault(); goTo( current - 1 ); }
			if ( e.key === 'ArrowRight' ) { e.preventDefault(); goTo( current + 1 ); }
		} );
	} );

} )();
