/**
 * process-overview.js
 * Handles the horizontal prev/next scrolling for the
 * .process-overview__steps slider on the Our Process page template.
 *
 * Strategy: CSS uses display:grid with overflow:hidden.
 * JS clones columns into a horizontally scrollable track
 * and drives it with prev/next buttons.
 *
 * Falls back gracefully when there are 3 or fewer steps
 * (buttons remain hidden, all steps visible).
 */

( function () {
    'use strict';

    document.addEventListener( 'DOMContentLoaded', function () {

        const sliders = document.querySelectorAll( '.js-overview-slider' );

        sliders.forEach( function ( slider ) {

            const wrap    = slider.closest( '.process-overview__slider-wrap' );
            if ( ! wrap ) return;

            const prevBtn = wrap.querySelector( '.js-overview-prev' );
            const nextBtn = wrap.querySelector( '.js-overview-next' );
            const steps   = Array.from( slider.querySelectorAll( '.process-overview__step' ) );

            if ( steps.length <= 3 ) {
                // All steps fit — hide navigation
                if ( prevBtn ) prevBtn.hidden = true;
                if ( nextBtn ) nextBtn.hidden = true;
                return;
            }

            let current = 0;
            const visible = 3;
            const max     = steps.length - visible;

            function updateVisibility() {
                steps.forEach( function ( step, i ) {
                    if ( i >= current && i < current + visible ) {
                        step.style.display = '';
                        step.removeAttribute( 'aria-hidden' );
                    } else {
                        step.style.display = 'none';
                        step.setAttribute( 'aria-hidden', 'true' );
                    }
                } );

                if ( prevBtn ) prevBtn.disabled = ( current === 0 );
                if ( nextBtn ) nextBtn.disabled = ( current >= max );
            }

            if ( prevBtn ) {
                prevBtn.addEventListener( 'click', function () {
                    if ( current > 0 ) {
                        current--;
                        updateVisibility();
                    }
                } );
            }

            if ( nextBtn ) {
                nextBtn.addEventListener( 'click', function () {
                    if ( current < max ) {
                        current++;
                        updateVisibility();
                    }
                } );
            }

            // Initialise
            updateVisibility();
        } );

    } );

}() );
