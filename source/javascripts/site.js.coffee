#= require "jquery-1.6.2"
#= require "jquery.pjax"

$('aside a').pjax('#main', timeout: 5000, fragment: "#main")