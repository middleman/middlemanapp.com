require "rack/contrib/try_static"
use Rack::TryStatic, :root => "build", :urls => %w[/], :try => ['.html', 'index.html', '/index.html']

require "rack/contrib/static_cache"
use Rack::StaticCache, :urls => ['/'], :root => 'build'

class NoWww
  def initialize(app)
    @app = app
  end
  
  def call(env)
    if env["HTTP_HOST"].include? "www."
      [301, { 
        "Location"     => env["rack.url_scheme"] + "://" + env["HTTP_HOST"].gsub("www.", "") + env["PATH_INFO"],
        "Content-Type" => "text/plain"
      }, 'No-www']
    else
      @app.call(env)
    end
  end
end
use NoWww