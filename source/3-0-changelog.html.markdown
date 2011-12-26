---
title: Middleman 3.0 Changelog
---

# Middleman 3.0 Changelog

* Rewritten to work directly with Rack (Sinatra apps can still be mounted)
* Finally support Compass in Sprockets! Thanks to @xdite and @petebrowne
* Sitemap object representing the known world
* FileWatcher proxies file change events
* Build --clean mode
* config.rb and extensions can add command-line commands
* Support for placekitten.com
* New Extension Registration API
* Activate mobile html5boilerplate template
* Nested layouts using `wrap_layout` helper
* New default layout functionality: https://github.com/middleman/middleman/issues/165
* Enable chained templates outside of sprockets (file.html.markdown.erb)
* Remove old 1.x mm- binaries and messaging
* Removed Slim from base install. Will need to be installed and required by the user (in - config.rb)
* Update to Redcarpet for Markdown (breaks Haml :markdown filter)
* Return correct exit codes (0 for success, 1 for failure) from CLI
* Yard code docs: http://rubydoc.info/github/middleman/middleman
* Added MM_ROOT environmental variable
* activating extensions can now take an options hash