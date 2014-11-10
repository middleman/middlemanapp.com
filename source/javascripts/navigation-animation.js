$(function() {
  var globalNav = $("[data-js='global-nav']");
  var waypointHideTrigger = $("[data-js='hero-content']");
  var waypointTrigger = $("[data-js='introduction-container']");

  waypointHideTrigger.waypoint(function() {
    globalNav.toggleClass("hidden");
  });

  waypointTrigger.waypoint(function() {
    globalNav.toggleClass("active");
    waypointTrigger.toggleClass("active");
  });
});
