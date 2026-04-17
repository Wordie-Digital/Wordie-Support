/**
 * Meadan Homes — Testimonial Slider
 * File: assets/js/testimonial-slider.js
 *
 * Vanilla JS slider for the acf/testimonial-slider block.
 * - Supports multiple sliders on the same page
 * - Auto-advances every 6 seconds
 * - Pauses on hover and focus-within
 * - Prev / next button navigation
 * - Dot navigation with ARIA roles
 * - Keyboard arrow-left / arrow-right support
 * - ARIA: aria-live on track, aria-hidden on inactive slides
 */

( function () {
    'use strict';

    /** Auto-advance interval in milliseconds. */
    var AUTO_ADVANCE_MS = 6000;

    /**
     * Initialise a single slider instance.
     *
     * @param {HTMLElement} section  The .testimonial-slider element.
     */
    function initSlider( section ) {
        var track  = section.querySelector( '[data-slider]' );
        var slides = section.querySelectorAll( '[data-slide-index]' );
        var dots   = section.querySelectorAll( '[data-slider-dot]' );
        var btnPrev = section.querySelector( '[data-slider-prev]' );
        var btnNext = section.querySelector( '[data-slider-next]' );

        if ( ! track || slides.length === 0 ) {
            return;
        }

        var total       = slides.length;
        var currentIndex = 0;
        var timer       = null;
        var paused      = false;

        /**
         * Move to a specific slide index.
         *
         * @param {number} index  Target slide index.
         */
        function goTo( index ) {
            // Wrap around
            if ( index < 0 ) {
                index = total - 1;
            } else if ( index >= total ) {
                index = 0;
            }

            // Deactivate current slide
            slides[ currentIndex ].classList.remove( 'testimonial-slide--active' );
            slides[ currentIndex ].setAttribute( 'aria-hidden', 'true' );

            // Deactivate current dot
            if ( dots.length > 0 ) {
                dots[ currentIndex ].classList.remove( 'testimonial-slider__dot--active' );
                dots[ currentIndex ].setAttribute( 'aria-selected', 'false' );
            }

            // Update index
            currentIndex = index;

            // Activate new slide
            slides[ currentIndex ].classList.add( 'testimonial-slide--active' );
            slides[ currentIndex ].setAttribute( 'aria-hidden', 'false' );

            // Activate new dot
            if ( dots.length > 0 ) {
                dots[ currentIndex ].classList.add( 'testimonial-slider__dot--active' );
                dots[ currentIndex ].setAttribute( 'aria-selected', 'true' );
            }

            // Announce change via aria-live on track
            track.setAttribute( 'aria-label',
                'Testimonial ' + ( currentIndex + 1 ) + ' of ' + total
            );
        }

        /** Advance one slide forward. */
        function next() {
            goTo( currentIndex + 1 );
        }

        /** Step one slide back. */
        function prev() {
            goTo( currentIndex - 1 );
        }

        /** Start auto-advance timer. */
        function startTimer() {
            if ( total <= 1 ) {
                return;
            }
            stopTimer();
            timer = setInterval( function () {
                if ( ! paused ) {
                    next();
                }
            }, AUTO_ADVANCE_MS );
        }

        /** Clear auto-advance timer. */
        function stopTimer() {
            if ( timer ) {
                clearInterval( timer );
                timer = null;
            }
        }

        // -------------------------------------------------------------------------
        // Event listeners
        // -------------------------------------------------------------------------

        // Prev button
        if ( btnPrev ) {
            btnPrev.addEventListener( 'click', function () {
                prev();
                stopTimer();
                startTimer();
            } );
        }

        // Next button
        if ( btnNext ) {
            btnNext.addEventListener( 'click', function () {
                next();
                stopTimer();
                startTimer();
            } );
        }

        // Dot navigation
        dots.forEach( function ( dot ) {
            dot.addEventListener( 'click', function () {
                var targetIndex = parseInt( dot.getAttribute( 'data-slider-dot' ), 10 );
                goTo( targetIndex );
                stopTimer();
                startTimer();
            } );
        } );

        // Keyboard navigation (arrow keys) — active when focus is inside the section
        section.addEventListener( 'keydown', function ( event ) {
            var key = event.key || event.keyCode;

            if ( key === 'ArrowLeft' || key === 37 ) {
                event.preventDefault();
                prev();
                stopTimer();
                startTimer();
            } else if ( key === 'ArrowRight' || key === 39 ) {
                event.preventDefault();
                next();
                stopTimer();
                startTimer();
            }
        } );

        // Pause on hover
        section.addEventListener( 'mouseenter', function () {
            paused = true;
        } );

        section.addEventListener( 'mouseleave', function () {
            paused = false;
        } );

        // Pause on focus-within (keyboard / tab navigation)
        section.addEventListener( 'focusin', function () {
            paused = true;
        } );

        section.addEventListener( 'focusout', function ( event ) {
            // Only resume if focus leaves the section entirely
            if ( ! section.contains( event.relatedTarget ) ) {
                paused = false;
            }
        } );

        // -------------------------------------------------------------------------
        // Initialise — ensure slide 0 is active and aria states are set
        // -------------------------------------------------------------------------
        slides.forEach( function ( slide, i ) {
            slide.setAttribute( 'aria-hidden', i === 0 ? 'false' : 'true' );
            if ( i !== 0 ) {
                slide.classList.remove( 'testimonial-slide--active' );
            } else {
                slide.classList.add( 'testimonial-slide--active' );
            }
        } );

        dots.forEach( function ( dot, i ) {
            dot.setAttribute( 'aria-selected', i === 0 ? 'true' : 'false' );
        } );

        // Set initial aria-live label
        track.setAttribute( 'aria-live', 'polite' );
        track.setAttribute( 'aria-label', 'Testimonial 1 of ' + total );

        // Start the timer
        startTimer();
    }

    /**
     * Find all slider sections on the page and initialise each one.
     */
    function initAll() {
        var sliders = document.querySelectorAll( '.testimonial-slider' );

        sliders.forEach( function ( slider ) {
            // Skip empty/placeholder sliders with no real slides
            if ( slider.querySelectorAll( '[data-slide-index]' ).length > 0 ) {
                initSlider( slider );
            }
        } );
    }

    // -------------------------------------------------------------------------
    // Boot on DOMContentLoaded
    // -------------------------------------------------------------------------
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initAll );
    } else {
        // DOM already parsed
        initAll();
    }

} )();
