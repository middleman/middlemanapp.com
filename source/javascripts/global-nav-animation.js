$(function() {
  var globalNav = $("[data-waypoint='global-nav']");
  var waypointHideTrigger = $("[data-waypoint='hero-content']");
  var waypointTrigger = $("[data-waypoint='introduction-container']");

  waypointHideTrigger.waypoint(function() {
    globalNav.toggleClass("hidden");
  });

  waypointTrigger.waypoint(function() {
    globalNav.toggleClass("active");
    waypointTrigger.toggleClass("active");
  });
});
