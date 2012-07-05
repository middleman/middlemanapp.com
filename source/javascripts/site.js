//= require "_lib/jquery.pjax"

$('h1 a, nav a').pjax('#main', {
  timeout: 5000,
  fragment: "#main",
  success: function() {
    window.scrollTo(0, 0);
  }
});