require "rack/contrib/try_static"
use Rack::TryStatic, :root => "build", :urls => %w[/], :try => ['index.html', '/index.html']

require "rack/contrib/static_cache"
run Rack::StaticCache, :urls => ['/'], :root => 'build'