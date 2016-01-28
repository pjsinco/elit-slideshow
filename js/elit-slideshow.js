jQuery(document).ready(function($) {

  // height of .elit-slideshow__item-title div
  var compensateForTitle = 60;


  // see elit_add_no_fouc_snippet() in functions.php
  $('.no-fouc').removeClass('no-fouc');

  var updateControlsPosition = function() {
    var imageWidth = $('.elit-slideshow__img').width();
    // Sometimes, event though this function is called 
    // on the afterInit event, it doesn't catch the image
    // size. So let's set it in those cases.
    if (imageWidth === 0) {
      imageWidth = 728; 
    }
    console.log(imageWidth);
    if (imageWidth <= maxWidth) {
      $('.elit-slideshow__nav')
        .each(function(index, elem) { 
          $(this).css('top', (((imageWidth / 3) * 2) / 2 + compensateForTitle + 'px')); 
        });
    }
  };

  var maxWidth = 728;
  var owl = $('#elit-slideshow');
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
