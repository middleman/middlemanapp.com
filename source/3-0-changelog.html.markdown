---
title: Middleman 3.0 Changelog
---

# Middleman 3.0 Changelog

* Rewritten to work directly with Rack (Sinatra apps can still be mounted)
* Sitemap maintains own state
* New Extension Registration API
* Remove old 1.x mm- binaries and messaging
* New default layout functionality: https://github.com/middleman/middleman/issues/165
* Enable chained templates outside of sprockets (file.html.markdown.erb)
* Finally support Compass in Sprockets! Thanks to @xdite and @petebrowne
* Sitemap object representing the known world
* FileWatcher proxies file change events
* Unified callback solution
* Removed Slim from base install. Will need to be installed and required by the user (in - config.rb)
* Activate mobile html5boilerplate template
* Update to Redcarpet for Markdown (breaks Haml :markdown filter)
* Return correct exit codes (0 for success, 1 for failure) from CLI
* Yard code docs: http://rubydoc.info/github/middleman/middleman
* config.rb and extensions can add command-line commands
* Nested layouts using `wrap_layout` helper
* Support for placekitten.com
* Added MM_ROOT environmental variable
* activating extensions can now take an options hash