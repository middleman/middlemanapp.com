# CodeRay syntax highlighting in Haml
# activate :code_ray

set :markdown, :layout_engine => :slim

require 'coderay'
require 'rack/codehighlighter'

use Rack::Codehighlighter, :coderay, 
  :markdown => true, 
  :element => "pre>code", 
  :pattern => /\A:::([-_+\w]+)\s*(\n|&#x000A;)/, 
  :logging => false

# Build-specific configuration
configure :build do
  Compass.configuration do |config|
    config.line_comments = false
  end
  
  # For example, change the Compass output style for deployment
  activate :minify_css

  # Minify Javascript on build
  activate :minify_javascript
  
  # Enable cache buster
  activate :cache_buster
end