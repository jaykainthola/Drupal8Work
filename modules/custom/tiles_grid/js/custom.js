/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.bootstrap_mint_custom = {
    attach: function (context, settings) {
		
		$('.tiles-grid-wrap').isotope({
		  // options
		  itemSelector: '.views-row',
		  layoutMode: 'fitRows'
		});

    }
  };

})(jQuery, Drupal);
