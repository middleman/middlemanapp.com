require "slim"
require "builder"

require "lib/guide_helpers"
helpers GuideHelpers

activate :directory_indexes

set :markdown, :layout_engine => :slim, :tables => true, :autolink => true
set :markdown_engine, :redcarpet

require 'rack/codehighlighter'
require "pygments"
use Rack::Codehighlighter, 
  :pygments,
  :element => "pre>code",
  :pattern => /\A:::([-_+\w]+)\s*\n/,
  :markdown => true

activate :relative_assets

# Build-specific configuration
configure :build do
  # For example, change the Compass output style for deployment
  activate :minify_css

  # Minify Javascript on build
  activate :minify_javascript
  
  # Enable cache buster
  activate :cache_buster
end
