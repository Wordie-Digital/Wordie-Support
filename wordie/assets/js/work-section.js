/* ==========================================================================
   Work Section — carousel navigation
   ========================================================================== */

( function () {
	'use strict';

	document.querySelectorAll( '[data-carousel]' ).forEach( function ( carousel ) {
		const slides    = Array.from( carousel.querySelectorAll( '.block-work-section__slide' ) );
		const section   = carousel.closest( '[data-block="work-section"]' );
		const thumbBtns = section
			? Array.from( section.querySelectorAll( '[data-goto]' ) )
			: [];

		let current = 0;

		function goTo( index ) {
			const total = slides.length;
			index = ( ( index % total ) + total ) % total;

			slides.forEach( function ( slide, i ) {
				const active = i === index;
				slide.classList.toggle( 'is-active', active );
				slide.setAttribute( 'aria-hidden', active ? 'false' : 'true' );
			} );

			thumbBtns.forEach( function ( btn ) {
				const isTarget = parseInt( btn.dataset.goto, 10 ) === index;
				btn.setAttribute( 'aria-pressed', isTarget ? 'true' : 'false' );
				btn.closest( '.block-work-section__thumbnail' )
					?.classList.toggle( 'is-active', isTarget );
			} );

			current = index;
		}

		// Prev / next buttons (one pair per slide)
		carousel.querySelectorAll( '[data-prev]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () { goTo( current - 1 ); } );
		} );

		carousel.querySelectorAll( '[data-next]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () { goTo( current + 1 ); } );
		} );

		// Thumbnail buttons
		thumbBtns.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				goTo( parseInt( btn.dataset.goto, 10 ) );
			} );
		} );

		// Keyboard: arrow left/right when carousel or thumbnails are focused
		if ( section ) {
			section.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'ArrowLeft' )  { e.preventDefault(); goTo( current - 1 ); }
				if ( e.key === 'ArrowRight' ) { e.preventDefault(); goTo( current + 1 ); }
			} );
		}
	} );

} )();
