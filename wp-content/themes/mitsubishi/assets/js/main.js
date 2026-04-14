import '../scss/main.scss';
import '../lib/bootstrap-5.1.3/js/src/tab.js';
import '../lib/bootstrap-5.1.3/js/src/collapse.js';
import {balanceElements, onWindowEvents} from '../lib/utils/utils';

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
  const Mitsubishi = window['Mitsubishi'] || {};

  Mitsubishi.init = function() {
    // Balance all elements
    onWindowEvents(() => {
      Mitsubishi.balanceAllElements();
    }, 1, 1, 1, 0);

    // Balance elements after Ajax Load More
    window.almComplete = function(alm) {
      Mitsubishi.balanceAllElements();

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

    // Focus on search for heading section
    $(document).on('click', '#alm-filter-search-title', function() {
      $(this).closest('.alm-filter--search').find('.textfield').focus();
    });
  };

  Mitsubishi.balanceAllElements = function() {
    balanceElements($('.balance-elements'), false, 30);
    balanceElements($('#product-tab-content-overview .elementor-image-box-content .elementor-image-box-title'), false, 30);
  };

  // No code should be added below this
  window['Mitsubishi'] = Mitsubishi;
  Mitsubishi.init();
});
