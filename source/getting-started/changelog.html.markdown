---
title: Middleman 3.0 Beta Changelog
---

# Middleman 3.0 Beta Changelog

* Split into 3 gems (`middleman-core`, `middleman-more` and `middleman` which simply includes both)
* Rewritten to work directly with Rack (Sinatra apps can still be mounted)
* Finally support Compass in Sprockets! Thanks to [@xdite](https://twitter.com/xdite) and [@petebrowne](https://twitter.com/petebrowne)
* [Sitemap](http://rubydoc.info/github/middleman/middleman/master/Middleman/Sitemap/Store) object representing the known world
* [FileWatcher](http://rubydoc.info/github/middleman/middleman/master/Middleman/CoreExtensions/FileWatcher) proxies file change events
* Build `--clean` mode
* `config.rb` and extensions can add command-line commands
* Support for http://placekitten.com
* New [Extension Registration API](http://rubydoc.info/github/middleman/middleman/master/Middleman/Extensions)
* Activate mobile html5boilerplate template
* Nested layouts using `wrap_layout` helper
* New default layout functionality: https://github.com/middleman/middleman/issues/165
* Enable chained templates outside of sprockets (`file.html.markdown.erb`)
* Remove old 1.x mm- binaries and messaging
* Removed Slim from base install. Will need to be installed and required by the user (in `config.rb`)
* Update to Redcarpet for Markdown (breaks Haml `:markdown` filter)
* Return correct exit codes (0 for success, 1 for failure) from CLI
* Yard code docs: http://rubydoc.info/github/middleman/middleman
* Added MM_ROOT environmental variable
* [activating extensions](http://rubydoc.info/github/middleman/middleman/master/Middleman/CoreExtensions/Extensions/InstanceMethods#activate-instance_method) can now take an options hash
* Don't re-minify files with `.min` in their name
* Serve purely static folders directly (without `source/` and `config.rb`)
* `middleman init` generates a `Gemfile` by default.
* Errors stop the build and print a stacktrace rather than silently getting printed into files.
* `with_layout` works with globs or regexes.
* Setting `directory_index` from `page` with a glob or regex now works.
* `:gzip` extension for pre-gzipping files for better compression with no server CPU cost.
* `:asset_hash` extension that generates unique-by-content filenames for assets and rewrites references to use those filenames, so you can set far-future expires on your assets.
* Removed the `--relative` CLI option.
* Properly output Compass-generated sprited images.
* Include vendored assets in sprockets path.
* Switch built-in CSS compressor to Rainpress.
* Automatically load helper modules from `helpers/`, like Rails.
* `ignore` and `page` both work with file globs or regexes.
* `layout`, `ignore`, and `directory_index` can be set from front matter.
* JavaScript and CSS are minified no matter where they are in the site, including in inline code blocks.
* Files with just a template extension get output with the correct exension (foo.erb => foo.html)
* `link_to` is smart about source paths, and can produce relative URLs with the `:relative` option or the sitewide `:relative_links` setting.
* Include vendored assets in sprockets path.
* Finally support Compass in Sprockets! Thanks to @xdite and @petebrowne
* Moved Sprockets into an extension
* Support loading Less @imports
* Doing a build now shows identical files
* asset_hash, minify_javascript, and minify_css can now accept regexes, globs, 
  and procs
* The `link_to` helper can now accept a sitemap Resource as a URL
* UTF-8 is now the new default encoding for data and templates
* New :encoding setting that allows users to change default encoding
* You may now use the `use` method with a block when adding Rack middleware
* automatic_directory_matcher
