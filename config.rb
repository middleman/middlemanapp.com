require "slim"
require "builder"
require "slim"
require "redcarpet"

mime_type :php, 'application/x-httpd-php'

helpers do
  def is_guide_page?
    request.path =~ /guides/
  end
  
  def edit_guide_url
    file_name = request.path.split("guides/").last
    "https://github.com/middleman/middleman-guides/blob/master/source/guides/#{file_name}.markdown"
  end
end

set :slim, :pretty => true

set :markdown, :layout_engine => :slim, :tables => true
set :markdown_engine, :redcarpet

activate :directory_indexes

require 'rack/codehighlighter'
require "pygments"
use Rack::Codehighlighter, 
  :pygments,
  :element => "pre>code",
  :pattern => /\A:::([-_+\w]+)\s*\n/,
  :markdown => true

# Build-specific configuration
configure :build do
  compass_config do |config|
    config.line_comments = false
  end
  
  # For example, change the Compass output style for deployment
  activate :minify_css

  # Minify Javascript on build
  activate :minify_javascript
  
  # Enable cache buster
  activate :cache_buster
end