#= require "_lib/jquery-1.7.1"
#= require "_lib/jquery.pjax"

$('h1 a, nav a').pjax '#main', 
  timeout: 5000
  fragment: "#main"
  success: -> window.scrollTo(0, 0)