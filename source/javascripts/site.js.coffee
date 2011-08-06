#= require "jquery-1.6.2"
#= require "jquery.pjax"

$('nav a').pjax '#main', 
  timeout: 5000
  fragment: "#main"
  success: -> window.scrollTo(0, 0)