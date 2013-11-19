---
title: Asset Pipeline
---

# Asset Pipeline

## Dependency Management

[Sprockets] is a tool for managing libraries of JavaScript (and CoffeeScript) code, declaring dependency management and include 3rd-party code. At its core, Sprockets makes a `require` method available inside your .js and .coffee files which can pull in the contents of an external file from your project or from a 3rd party gem.

Say I have a file called `jquery.js` which contains the jQuery library and another file called `app.js` which contains my application code. My app file can include jquery before it runs like so:

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

If you're using Sass you should stick with Sass' `@import` rule rather than using Sprockets directives.

## Asset Gems

You can use assets from gems by including them in your `Gemfile`, like this:

```ruby
gem "bootstrap-sass", :require => false
```

The `:require => false` bit is important - many of these gems assume you're running in Rails, and break when they try to hook into Rails' or Compass' internals. Just avoid requiring the gems and Middleman will take care of the rest.

Once you've added a dependency on these gems, any images and fonts from the gem will be included in your project automatically. JavaScript and CSS are also available to be `require`ed or `@import`ed into your own files.

If you want to refer to a gem stylesheet or JS file directly from your HTML rather than including it in your own assets, you'll need to import it explicitly in `config.rb`:

```ruby
sprockets.import_asset 'jquery-mobile'
```

Then you can refer to that asset directly from `script` tags or `javascript_include_tag`.

## Sprockets Import Path

If you have assets in directories other than your `:js_dir` or `:css_dir`, you can make them importable by addin them to your Sprockets import path. Add this to your `config.rb`:

```ruby
sprockets.append_path '/my/shared/assets/'
```

## Compass

Middleman comes with [Compass] support out of the box. Compass is a powerful framework for writing cross-browser stylesheets in Sass. Compass also has its own extensions, like [Susy], which you can use in Middleman. All of Sprockets' path helpers like `image-url` are hooked into the Middleman Sitemap, so other extensions (like `:asset_hash`) will affect your stylesheets too.

[Sprockets]: https://github.com/sstephenson/sprockets
[Compass]: http://compass-style.org
[Susy]: http://susy.oddbird.net
