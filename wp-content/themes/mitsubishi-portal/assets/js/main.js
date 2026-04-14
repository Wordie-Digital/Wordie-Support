import {balanceElements, onWindowEvents} from '../lib/utils/utils';
import '../scss/main.scss';
import '../lib/overlayscrollbars/css/OverlayScrollbars.min.css';
import '../lib/bootstrap-5.1.3/js/src/modal';

// LOAD ELEMENTOR SCSS
require.context('inc/elementor/widgets/', true, /\.scss$/i).keys().map(name => {
  name = name.slice(2, -5);
  import(`inc/elementor/widgets/${name}.scss`);
});

// LOAD TEMPLATES SCSS
require.context('template-parts/', true, /\.scss$/i).keys().map(name => {
  name = name.slice(2, -5);
  import(`template-parts/${name}.scss`);
});

// MAIN SCRIPT
jQuery(document).ready(function($) {
  const Portal = window['Portal'] || {};

  Portal.init = function() {
    // Balance all elements
    onWindowEvents(() => {
      Portal.balanceAllElements();
    }, 1, 1, 1, 0);

    // After Ajax Load More
    window.almComplete = function(alm) {
      // Balance elements
      Portal.balanceAllElements();

      // Check if BOM in window and updateButtonsStatus function exists in BOM
      if ('BOM' in window && 'updateButtonsStatus' in window.BOM) {
        // Update buttons status
        window.BOM.updateButtonsStatus();
      }

      // Load feather icons for ALM
      feather.replace();

      // Re-initiate addtoany share
      if ('a2a' in window) {
        window.a2a.init_all('page');
      }
    };

    // Wow animations
    if ('WOW' in window) {
      const wow = new WOW({
        boxClass: 'wow',
        animateClass: 'animate__animated',
        offset: 0,
        mobile: true,
        live: true
      });

      wow.init();
    }
  };

  Portal.balanceAllElements = function() {
    balanceElements($('.balance-elements'), false, 30);
  };

  // No code should be added below this
  window['Portal'] = Portal;
  Portal.init();
});
