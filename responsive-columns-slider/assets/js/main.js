(function($){
  $( document ).ready(function(){
    var currentIndex = 0,
      currentIndex1 = 1,
      currentIndex2 = 2,
      items = $('.recommended-slider-wrapper .recommended-post-wrapper'),
      itemAmt = items.length;

    function cycleItems() {
      var item = $('.recommended-slider-wrapper .recommended-post-wrapper').eq(currentIndex);
      var item1 = $('.recommended-slider-wrapper .recommended-post-wrapper').eq(currentIndex1);
      var item2 = $('.recommended-slider-wrapper .recommended-post-wrapper').eq(currentIndex2);
      items.addClass('visible-xs');
      item.removeClass('visible-xs');
      item1.removeClass('visible-xs');
      item2.removeClass('visible-xs');
    }

    var autoSlide = setInterval(function() {
      currentIndex = (currentIndex+1)%itemAmt;
      currentIndex1 = (currentIndex+1)%itemAmt;
      currentIndex2 = (currentIndex2+1)%itemAmt;
      cycleItems();
    }, 4000);
    cycleItems();
  })
})(jQuery);