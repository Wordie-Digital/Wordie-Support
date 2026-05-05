( function () {
    'use strict';

    // Mobile menu toggle
    const hamburger = document.querySelector( '.eeta-nav__hamburger' );
    const mobileMenu = document.getElementById( 'eeta-mobile-menu' );

    if ( hamburger && mobileMenu ) {
        hamburger.addEventListener( 'click', function () {
            const isOpen = hamburger.getAttribute( 'aria-expanded' ) === 'true';
            hamburger.setAttribute( 'aria-expanded', String( ! isOpen ) );
            mobileMenu.hidden = isOpen;
            hamburger.classList.toggle( 'is-open', ! isOpen );
        } );
    }

    // Sticky nav shadow on scroll
    const nav = document.querySelector( '.eeta-nav' );
    if ( nav ) {
        window.addEventListener( 'scroll', function () {
            nav.classList.toggle( 'eeta-nav--scrolled', window.scrollY > 10 );
        }, { passive: true } );
    }
} )();
