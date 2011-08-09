#= require "jquery-1.6.2"
#= require "jquery.pjax"

$('h1 a, nav a').pjax '#main', 
  timeout: 5000
  fragment: "#main"
  success: -> window.scrollTo(0, 0)