import $ from 'jquery';
import 'waypoints/lib/jquery.waypoints';
import 'waypoints/lib/shortcuts/sticky';

export default function() {
  const $stickyEl = $('[data-waypoint="sidebar"]');
  const $stopEl   = $('[data-waypoint="footer"]');
  const $verticalGutter = $stopEl.outerHeight();
  const $stickyElHeight = $stickyEl.outerHeight();

  if ($stickyEl.length <= 0) { return; }

  new Waypoint.Sticky({
    element: $stickyEl,
    wrapper: '<aside class="sidebar-container" />'
  });

  $stopEl.waypoint((direction) => {
    if (direction == 'down') {
      const footerOffset = $stopEl.offset();

      $stickyEl.css({
        position: 'absolute',
        top: footerOffset.top - $stickyElHeight - $verticalGutter
      });
    } else if (direction == 'up') {
      $stickyEl.attr('style', '');
    }
  }, {
    offset: () => $stickyElHeight
  });
}
