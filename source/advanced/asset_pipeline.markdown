---
title: Asset Pipeline
---

# Asset Pipeline

## Dependency Management

[Sprockets] is a tool for managing libraries of JavaScript (and CoffeeScript)
code, declaring dependency management and include 3rd-party code. At its core,
Sprockets makes a `require` method available inside your .js and .coffee files
which can pull in the contents of an external file from your project or from a
3rd party gem.

Say I have a file called `jquery.js` which contains the jQuery library and
another file called `app.js` which contains my application code. My app file
can include jquery before it runs like so:

``` javascript
//= require "jquery"

$(document).ready(function() {
  $(".item").pluginCode({
    param1: true,
    param2: "maybe"
  });
});
```

This system also works within CSS files:

``` css
/*
 *= require base
 */

body {
  font-weight: bold;
}

```

If you're using Sass you should stick with Sass' `@import` rule rather than
using Sprockets directives.

## Deploying combined assets only

If you prefer to deploy only the combined (concatenated) assets to the `build`
directory with `middleman build` command, you should use the underscore-names
for your ingredient assets. Example: the main `/source/javascripts/all.js` file
is used for all dependencies:

``` javascript
//= require "_jquery"
//= require "_my_lib_code"
//= require "_my_other_code"
```

and the `/source/javascripts/` directory should contain files: `_jquery.js`,
`_my_lib_code.js`, `_my_other_code.js`. The resulting `/build/javascripts/`
directory will contain the `all.js` file only, with all dependant code
included.

## Asset Gems

You can use assets from gems by including them in your `Gemfile`, like this:

```ruby
gem "bootstrap-sass", :require => false
```

The `:require => false` bit is important - many of these gems assume you're
running in Rails, and break when they try to hook into Rails' or Compass'
internals. Just avoid requiring the gems and Middleman will take care of the
rest.

Once you've added a dependency on these gems, any images and fonts from the gem
will be included in your project automatically. JavaScript and CSS are also
available to be `require`ed or `@import`ed into your own files.

If you want to refer to a gem stylesheet or JS file directly from your HTML
rather than including it in your own assets, you'll need to import it
explicitly in `config.rb`:

```ruby
sprockets.import_asset 'jquery-mobile'
```

Then you can refer to that asset directly from `script` tags or
`javascript_include_tag`.

## Sprockets Import Path

If you have assets in directories other than your `:js_dir` or `:css_dir`, you
can make them importable by adding them to your Sprockets import path. Add this
to your `config.rb`:

```ruby
sprockets.append_path '/my/shared/assets/'
```

Sprockets supports Bower, so you can add your Bower components path directly:

```ruby
sprockets.append_path File.join root, 'bower_components'
```

To make your bower controlled assets - images, fonts etc. - available within
your application, you need to import them using `sprockets.import_asset`. Given
your component is called `jquery`, you can import all files mentioned in the
[`main`-section](https://github.com/bower/bower.json-spec) of the `bower.json`
by using the following statement in your `config.rb`:

```ruby
sprockets.import_asset 'jquery'
```

If you prefer to import a specific asset you need to use its relative path,
which is `<component_name>/<path_to_asset>`:

```ruby
sprockets.import_asset 'jquery/dist/jquery.js'
```

If you need to set an individual output path, you can pass `#import_asset`
a block. This block gets the logical path of the asset as `Pathname` and needs
to return the output path for the asset.

```ruby
sprockets.import_asset('jquery/dist/jquery.js') do |logical_path|
  Pathname.new('javascripts_new.d') + logical_path
  # => javascripts_new.d/jquery/dist/jquery.js
end
```

Make sure to use parentheses for `#import_asset` if you are using curly braces
for the block! Otherwise the block might get passed to another method and not
to `#import_asset` and you wonder why the output path is not set correctly.

```ruby
sprockets.import_asset('jquery/dist/jquery.js') { |logical_path| Pathname.new('javascripts_new.d') + logical_path }
```

To automate this a bit, you can use file lists from
[rake](https://github.com/jimweirich/rake). Another option might be
[hike](https://github.com/sstephenson/hike). You CANNOT use
`sprockets.each_file` for this, because `sprockets` on top-level in `config.rb`
is a [faked sprocket
environment](https://github.com/middleman/middleman-sprockets/blob/master/lib/middleman-sprockets/config_only_environment.rb)
and therefor this method is not available. But be careful, you might need to add `gem
"rake"` or `gem "hike"` to your project-`Gemfile` to make this work.

```ruby
require 'rake/file_list'
require 'pathname'

bower_directory = 'vendor/assets/components'

# Build search patterns
patterns = [
  '.png',  '.gif', '.jpg', '.jpeg', '.svg', # Images
  '.eot',  '.otf', '.svc', '.woff', '.ttf', # Fonts
  '.js',                                    # Javascript
].map { |e| File.join(bower_directory, "**", "*#{e}" ) }

# Create file list and exclude unwanted files
Rake::FileList.new(*patterns) do |l|
  l.exclude(/src/)
  l.exclude(/test/)
  l.exclude(/demo/)
  l.exclude { |f| !File.file? f }
end.each do |f|
  # Import relative paths
  sprockets.import_asset(Pathname.new(f).relative_path_from(Pathname.new(bower_directory)))
end
```

## Helpers

There are helpers available to be used within your `*.scss` files:

* `image-path()`, `image-url()`
* `font-path()`, `font-url()`

Those helpers prepend the correct directory/url to your asset, e.g.
`image-path('lightbox2/img/close.png')` becomes
`images/lightbox2/img/close.png`. To reference a bower controlled asset you
need to use its relative name `lightbox2/img/close.png` for an image which is
part of the `lightbox2`-component.

## Compass

Middleman comes with [Compass] support out of the box. Compass is a powerful
framework for writing cross-browser stylesheets in Sass. Compass also has its
own extensions, like [Susy], which you can use in Middleman. All of Sprockets'
path helpers like `image-url` are hooked into the Middleman Sitemap, so other
extensions (like `:asset_hash`) will affect your stylesheets too.

[Sprockets]: https://github.com/sstephenson/sprockets
[Compass]: http://compass-style.org
[Susy]: http://susy.oddbird.net
