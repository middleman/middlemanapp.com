$(window).scroll(function (event) {
  var y = $(this).scrollTop();

  function animateBoxes(parent) {
   var firstSelector = parent + ' .box-left';
   var secondSelector = parent + ' .box-right';
   $(firstSelector).addClass('animate')
   $(secondSelector).addClass('animate')
  }

  if (y >= 500) {
   animateBoxes('.second-graphic')
  }

  if (y >= 900) {
   animateBoxes('.third-graphic')
  }

  if (y >= 1200) {
   animateBoxes('.fourth-graphic')
  }
});
