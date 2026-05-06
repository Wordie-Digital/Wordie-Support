/**
 * Meadan — nav.js
 *
 * Handles:
 *  1. Mobile overlay toggle (hamburger)
 *  2. Desktop dropdown submenus (hover + keyboard)
 *  3. Mobile submenu accordion (tap to expand)
 *  4. Keyboard navigation (Enter, Space, ArrowDown, ArrowUp, Escape, Tab)
 *  5. Click-outside to close open dropdowns
 *  6. Scroll-triggered header state (.is-scrolled)
 */

( function () {
    'use strict';

    document.addEventListener( 'DOMContentLoaded', function () {

        const header  = document.querySelector( '.site-header' );
        const toggle  = document.querySelector( '.site-header__nav-toggle' );
        const nav     = document.querySelector( '#primary-navigation' );
        const parents = document.querySelectorAll( '.site-header__nav-list .menu-item-has-children' );

        if ( ! header || ! toggle || ! nav ) return;

        // ── 1. Mobile overlay toggle ──────────────────────────────────────

        toggle.addEventListener( 'click', function () {
            const isOpen = toggle.getAttribute( 'aria-expanded' ) === 'true';
            toggle.setAttribute( 'aria-expanded', isOpen ? 'false' : 'true' );
            nav.classList.toggle( 'is-open', ! isOpen );
            document.body.classList.toggle( 'nav-is-open', ! isOpen );
        } );

        // ── 2. Inject mobile accordion toggle buttons ─────────────────────
        //    Added programmatically so markup stays clean without JS.

        parents.forEach( function ( item ) {
            const btn = document.createElement( 'button' );
            btn.className = 'site-header__submenu-toggle';
            btn.type      = 'button';
            btn.setAttribute( 'aria-label', 'Toggle submenu' );
            btn.innerHTML = '<svg width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>';

            // Insert after the anchor tag
            const anchor = item.querySelector( ':scope > a' );
            if ( anchor ) {
                anchor.insertAdjacentElement( 'afterend', btn );
            }

            // Mobile: accordion toggle (only active below 1024px)
            btn.addEventListener( 'click', function ( e ) {
                e.stopPropagation();
                const isOpen = item.classList.contains( 'is-open' );
                // Close siblings
                parents.forEach( function ( sibling ) {
                    if ( sibling !== item ) sibling.classList.remove( 'is-open' );
                } );
                item.classList.toggle( 'is-open', ! isOpen );
            } );
        } );

        // ── 3. Desktop keyboard navigation ───────────────────────────────

        parents.forEach( function ( item ) {
            const anchor   = item.querySelector( ':scope > a' );
            const subMenu  = item.querySelector( ':scope > .sub-menu' );
            const subLinks = subMenu ? Array.from( subMenu.querySelectorAll( 'a' ) ) : [];

            if ( ! anchor || ! subMenu ) return;

            // Enter / Space on parent opens submenu
            anchor.addEventListener( 'keydown', function ( e ) {
                if ( e.key === 'Enter' || e.key === ' ' ) {
                    e.preventDefault();
                    const isOpen = item.classList.contains( 'is-open' );
                    closeAll();
                    if ( ! isOpen ) {
                        item.classList.add( 'is-open' );
                        if ( subLinks.length ) subLinks[ 0 ].focus();
                    }
                }

                // ArrowDown opens and focuses first sub-item
                if ( e.key === 'ArrowDown' ) {
                    e.preventDefault();
                    item.classList.add( 'is-open' );
                    if ( subLinks.length ) subLinks[ 0 ].focus();
                }

                // Escape closes
                if ( e.key === 'Escape' ) {
                    closeAll();
                    anchor.focus();
                }
            } );

            // Arrow navigation within submenu
            subLinks.forEach( function ( link, idx ) {
                link.addEventListener( 'keydown', function ( e ) {
                    if ( e.key === 'ArrowDown' ) {
                        e.preventDefault();
                        const next = subLinks[ idx + 1 ];
                        if ( next ) next.focus();
                    }
                    if ( e.key === 'ArrowUp' ) {
                        e.preventDefault();
                        if ( idx === 0 ) {
                            anchor.focus();
                        } else {
                            subLinks[ idx - 1 ].focus();
                        }
                    }
                    if ( e.key === 'Escape' ) {
                        e.preventDefault();
                        closeAll();
                        anchor.focus();
                    }
                    // Tab out of last item closes dropdown
                    if ( e.key === 'Tab' && ! e.shiftKey && idx === subLinks.length - 1 ) {
                        closeAll();
                    }
                } );
            } );
        } );

        // ── 4. Click-outside to close dropdowns ───────────────────────────

        document.addEventListener( 'click', function ( e ) {
            if ( ! nav.contains( e.target ) ) {
                closeAll();
            }
        } );

        // ── 5. Global Escape — close mobile nav or any open dropdown ─────

        document.addEventListener( 'keydown', function ( e ) {
            if ( e.key !== 'Escape' ) return;
            const anyOpen = document.querySelector(
                '.site-header__nav-list .menu-item-has-children.is-open'
            );
            if ( anyOpen ) {
                closeAll();
            } else if ( nav.classList.contains( 'is-open' ) ) {
                closeMobileNav();
                toggle.focus();
            }
        } );

        // ── 6. Scroll state ───────────────────────────────────────────────

        function onScroll() {
            header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
        }
        window.addEventListener( 'scroll', onScroll, { passive: true } );
        onScroll(); // initialise on load

        // ── Helpers ───────────────────────────────────────────────────────

        function closeAll() {
            parents.forEach( function ( item ) {
                item.classList.remove( 'is-open' );
            } );
        }

        function closeMobileNav() {
            toggle.setAttribute( 'aria-expanded', 'false' );
            nav.classList.remove( 'is-open' );
            document.body.classList.remove( 'nav-is-open' );
        }

    } );

} )();
