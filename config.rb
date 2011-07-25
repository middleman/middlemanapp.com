require "builder"
require "redcarpet"

Slim::Engine.set_default_options :pretty => true

set :markdown, :layout_engine => :slim
set :markdown_engine, Middleman::CoreExtensions::FrontMatter::RedcarpetTemplate

activate :blog
set :blog_permalink, "blog/:year/:month/:day/:title.html"
set :blog_layout_engine, :slim
page "/blog/feed.xml", :layout => false

require 'rack/codehighlighter'
use Rack::Codehighlighter, 
  :pygments_api,
  :element => "pre>code",
  :pattern => /\A:::([-_+\w]+)\s*\n/,
  :markdown => true

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