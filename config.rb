require "builder"

activate :directory_indexes

set :markdown, :tables => true, :autolink => true
set :markdown_engine, :redcarpet

require 'rack/codehighlighter'
require "pygments"
use Rack::Codehighlighter, 
  :pygments,
  :element => "pre>code",
  :pattern => /\A:::([-_+\w]+)\s*\n/,
  :markdown => true

# Build-specific configuration
configure :build do
  # For example, change the Compass output style for deployment
  activate :minify_css

  # Minify Javascript on build
  activate :minify_javascript
end
