$(function() {
  var $stickyEl = $("[data-waypoint='toc']");
  var $stopEl   = $("[data-waypoint='footer']");
  var $verticalGutter = $stopEl.outerHeight()

  $stickyEl.waypoint("sticky", function() {
    $("[data-waypoint='main']").toggleClass("stuck");
  });

  $stopEl.waypoint(function (direction) {
    if (direction == "down") {
      var footerOffset = $stopEl.offset();

      $stickyEl.css({
        position: "absolute",
        top: footerOffset.top - $stickyEl.outerHeight() - $verticalGutter
      });
    } else if (direction == "up") {
      $stickyEl.attr("style", "");
    }
  }, {
    offset: function () {
      return $stickyEl.outerHeight();
    }
  });
});
