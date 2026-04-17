/**
 * Meadan — projects-slider.js
 * Vanilla JS prev/next slider for Projects, Designs, and News sections.
 * Shows one page of cards at a time, driven by CSS column visibility.
 * Keyboard accessible, ARIA state managed on buttons.
 */

( function () {
    'use strict';

    /**
     * Determine how many cards are visible based on current grid columns.
     * Reads the computed gridTemplateColumns to find the column count.
     */
    function getVisibleCount( slider ) {
        const cols = getComputedStyle( slider ).gridTemplateColumns;
        return cols.split( ' ' ).length;
    }

    function initSlider( section ) {
        const slider  = section.querySelector( '.js-slider' );
        const prevBtn = section.querySelector( '.js-slider-prev' );
        const nextBtn = section.querySelector( '.js-slider-next' );

        if ( ! slider || ! prevBtn || ! nextBtn ) return;

        const cards   = Array.from( slider.children );
        let   page    = 0;

        function render() {
            const perPage = getVisibleCount( slider );
            const total   = cards.length;
            const pages   = Math.ceil( total / perPage );

            cards.forEach( function ( card, i ) {
                const inView = i >= page * perPage && i < ( page + 1 ) * perPage;
                card.hidden = ! inView;
                card.setAttribute( 'aria-hidden', inView ? 'false' : 'true' );
            } );

            prevBtn.disabled = page <= 0;
            nextBtn.disabled = page >= pages - 1;

            prevBtn.setAttribute( 'aria-disabled', prevBtn.disabled ? 'true' : 'false' );
            nextBtn.setAttribute( 'aria-disabled', nextBtn.disabled ? 'true' : 'false' );
        }

        prevBtn.addEventListener( 'click', function () {
            if ( page > 0 ) {
                page--;
                render();
            }
        } );

        nextBtn.addEventListener( 'click', function () {
            const perPage = getVisibleCount( slider );
            const pages   = Math.ceil( cards.length / perPage );
            if ( page < pages - 1 ) {
                page++;
                render();
            }
        } );

        // Re-render on resize (debounced)
        let resizeTimer;
        window.addEventListener( 'resize', function () {
            clearTimeout( resizeTimer );
            resizeTimer = setTimeout( function () {
                page = 0;
                render();
            }, 200 );
        } );

        render();
    }

    document.addEventListener( 'DOMContentLoaded', function () {
        document.querySelectorAll( '.projects-section, .designs-section, .news-section' )
            .forEach( initSlider );
    } );
} )();
