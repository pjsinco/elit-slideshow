jQuery(document).ready(function($) {

  // see elit_add_no_fouc_snippet() in functions.php
  $('.no-fouc').removeClass('no-fouc');

  var updateControlsPosition = function() {
    var imageWidth = $('.elit-slideshow__img').width();
    if (imageWidth <= maxWidth) {
      $('.elit-slideshow__nav')
        .each(function(index, elem) { 
          $(this).css('top', (((imageWidth / 3) * 2) / 2 + 'px')); 
        });
    }
  };

  var maxWidth = 728;
  var owl = jQuery('#elit-slideshow');
  owl.owlCarousel({
    singleItem: true,
    slideSpeed: 350,
    paginationSpeed: 350,
    pagination: true,
    addClassActive: true,
    lazyLoad: true,
    afterInit: updateControlsPosition,
    afterUpdate: updateControlsPosition
  });

  jQuery('.next').click(function() {
    owl.trigger('owl.next');
  });

  jQuery('.prev').click(function() {
    owl.trigger('owl.prev');
  });
});


//$('.elit-slideshow__nav').each(function(index, elem) { $(this).css('top', '30%'); });
