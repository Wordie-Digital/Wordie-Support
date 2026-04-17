/**
 * Meadan — nav.js
 * Mobile navigation toggle. Manages aria-expanded and open state.
 */

( function () {
    'use strict';

    document.addEventListener( 'DOMContentLoaded', function () {
        const toggle = document.querySelector( '.site-header__nav-toggle' );
        const nav    = document.querySelector( '#primary-navigation' );

        if ( ! toggle || ! nav ) return;

        toggle.addEventListener( 'click', function () {
            const isOpen = toggle.getAttribute( 'aria-expanded' ) === 'true';
            toggle.setAttribute( 'aria-expanded', ! isOpen ? 'true' : 'false' );
            nav.classList.toggle( 'is-open', ! isOpen );
        } );

        // Close on Escape
        document.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'Escape' && nav.classList.contains( 'is-open' ) ) {
                toggle.setAttribute( 'aria-expanded', 'false' );
                nav.classList.remove( 'is-open' );
                toggle.focus();
            }
        } );
    } );
} )();
