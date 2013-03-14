/*!
Deck JS - deck.scroll
Copyright (c) 2012 Liam Morley
*/

/*
This module adds the ability to navigate through slides by means of scrolling
the page.
*/
(function($, deck, window, undefined) {
  var targets = [];
  // get slide offsets (called when deck is initialized, and upon resize)
  var init = function() {
    targets = $.map($[deck]('getSlides'), function(slide) {
      return [[slide.offset().top, slide]];
    }).sort(function (a, b) { return b[0] - a[0] }); // sort from bottom to top
  };
  $(document).bind('deck.init', init);
  $(window).resize(init).scroll(function () {
    var distance = $(this).scrollTop();
    var currentSlide = $[deck]('getSlide');
    // determine which slide is currently at the top
    for (var i = 0; i < targets.length; i++) {
      if (distance >= targets[i][0]) {
        // if current slide isn't already active, activate it
        if (currentSlide !== targets[i][1]) {
          var idx = targets.length - i - 1; // determine slide index
          $[deck]('go', idx);
        }
        break;
      }
    }
  });
})(jQuery, 'deck', this);

