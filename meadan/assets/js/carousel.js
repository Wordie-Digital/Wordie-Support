/**
 * Meadan — carousel.js
 * Vanilla JS image carousel for acf/image-carousel blocks.
 * Keyboard accessible, ARIA-managed, dot-driven pagination.
 */

( function () {
    'use strict';

    function initCarousel( carousel ) {
        const slides    = Array.from( carousel.querySelectorAll( '.image-carousel__slide' ) );
        const dots      = Array.from( carousel.querySelectorAll( '.image-carousel__dot' ) );
        const autoplay  = carousel.dataset.autoplay === 'true';
        const speed     = parseInt( carousel.dataset.speed, 10 ) || 4000;
        let   current   = 0;
        let   timer     = null;

        if ( slides.length < 2 ) return;

        function goTo( index ) {
            slides[ current ].classList.remove( 'image-carousel__slide--active' );
            slides[ current ].setAttribute( 'aria-hidden', 'true' );
            dots[ current ].classList.remove( 'image-carousel__dot--active' );
            dots[ current ].setAttribute( 'aria-selected', 'false' );

            current = ( index + slides.length ) % slides.length;

            slides[ current ].classList.add( 'image-carousel__slide--active' );
            slides[ current ].removeAttribute( 'aria-hidden' );
            dots[ current ].classList.add( 'image-carousel__dot--active' );
            dots[ current ].setAttribute( 'aria-selected', 'true' );
        }

        function startAutoplay() {
            if ( ! autoplay ) return;
            timer = setInterval( function () {
                goTo( current + 1 );
            }, speed );
        }

        function stopAutoplay() {
            clearInterval( timer );
        }

        // Dot click
        dots.forEach( function ( dot, i ) {
            dot.addEventListener( 'click', function () {
                stopAutoplay();
                goTo( i );
                startAutoplay();
            } );
        } );

        // Keyboard navigation (arrow keys when carousel is focused)
        carousel.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'ArrowRight' || e.key === 'ArrowDown' ) {
                e.preventDefault();
                stopAutoplay();
                goTo( current + 1 );
                startAutoplay();
            }
            if ( e.key === 'ArrowLeft' || e.key === 'ArrowUp' ) {
                e.preventDefault();
                stopAutoplay();
                goTo( current - 1 );
                startAutoplay();
            }
        } );

        // Pause autoplay on hover/focus
        carousel.addEventListener( 'mouseenter', stopAutoplay );
        carousel.addEventListener( 'focusin',    stopAutoplay );
        carousel.addEventListener( 'mouseleave', startAutoplay );
        carousel.addEventListener( 'focusout',   startAutoplay );

        // Touch / swipe support
        let touchStartX = 0;
        carousel.addEventListener( 'touchstart', function ( e ) {
            touchStartX = e.changedTouches[ 0 ].screenX;
        }, { passive: true } );
        carousel.addEventListener( 'touchend', function ( e ) {
            const delta = e.changedTouches[ 0 ].screenX - touchStartX;
            if ( Math.abs( delta ) > 40 ) {
                stopAutoplay();
                goTo( delta < 0 ? current + 1 : current - 1 );
                startAutoplay();
            }
        }, { passive: true } );

        startAutoplay();
    }

    document.addEventListener( 'DOMContentLoaded', function () {
        document.querySelectorAll( '.image-carousel' ).forEach( initCarousel );
    } );
} )();
