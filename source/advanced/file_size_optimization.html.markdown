---
title: File Size Optimization
---

# File Size Optimization

## Compressing CSS and JavaScript

Middleman handles CSS minification and Javascript compression so you don't have
to worry about it. Most libraries ship minified and compressed versions of
their files for users to deploy, but these files are unreadable or editable.
Middleman allows you to keep the original, commented files in our project so
you can easily read them and edit them if needed. Then, when you build the
project, Middleman will handle all the optimization for you.

In your `config.rb`, activate the `minify_css` and `minify_javascript` features
during the build of your site.

```ruby
configure :build do
  activate :minify_css
  activate :minify_javascript
end
```

If you are already using a compressed file that includes `.min` in its
filename, Middleman won't touch it. This can be good for libraries like jQuery
which are carefully compressed by their authors ahead of time.

You can customize how the JavaScript compressor works by setting the
`:compressor` option for the `:minify_javascript` extension in
`config.rb` to a custom instance of Uglifier. See [Uglifier's
docs](https://github.com/lautis/uglifier) for details.

For example, you could
enable unsafe optimizations and mangle top-level variable names like this:

```ruby
require "uglifier"
activate :minify_javascript,
  compressor: proc {
    ::Uglifier.new(:mangle => {:toplevel => true}, :compress => {:unsafe => true})
  }
```

If you have `asset_hash` activated, are building your site on multiple servers
during deploy to sit behind a load balancer, and are compressing Javascript,
ensure that mangling variables is disabled. If mangling is enabled, Uglifier
will create different compressed versions of the Javascript on each machine,
leading to different hashes in the filename and different references in each
version of the HTML. For example:

```ruby
require "uglifier"
activate :minify_javascript, compressor: -> { Uglifier.new(:mangle => false) }
```

If you want to exclude any files from being minified, pass the `:ignore` option
when activating these extensions, and give it one or more globs, regexes, or
procs that identify the files to ignore. Likewise, you can pass an `:exts`
option to change which file extensions are renamed.

You can speed up your JavaScript minification (and CoffeeScript builds) by
including these gems in your `Gemfile`:

```ruby
gem 'therubyracer' # faster JS compiles
gem 'oj' # faster JS compiles
```

## GZIP text files

It's a good idea to [serve compressed
files](https://developer.yahoo.com/performance/rules.html#gzip) to user agents
that can handle it. Many web servers have the ability to gzip files on the fly,
but that requires CPU work every time the file is served, and as a result most
servers don't perform the maximum compression. Middleman can produce gzipped
versions of your HTML, CSS, and JavaScript alongside your regular files, and
you can instruct your web server to serve those pre-gzipped files directly.
First, enable the `:gzip` extension:

```ruby
activate :gzip
```

Then configure your server to serve those files. If you use Nginx, check out
[the gzip_static](http://wiki.nginx.org/NginxHttpGzipStaticModule) module. For
Apache, you'll have to do something a little trickier - see [this
Gist](https://gist.github.com/2200790) for an example.

## Compressing Images

If you also want to compress images on build, try
[`middleman-imageoptim`](https://github.com/plasticine/middleman-imageoptim).

## Minify HTML

Middleman provides an official extension for minifying its HTML output. Simply
install the gem:

```bash
gem install middleman-minify-html
```

Add `middleman-minify-html` to your `Gemfile`:

```ruby
gem "middleman-minify-html"
```

Then open your `config.rb` and add:

```ruby
activate :minify_html
```

You should notice whilst view-source:'ing that your HTML is now being minified.

## Using source sets

One of the more recent additions to HTML is the `srcset` attribute for the `img` or `picture` tag. It allows you to define for the browser to load different images with different sizes dependent on either the viewport (using width such as `1024w, 800w, 600w, or 320w`) or the resolution of the current browser display (using factors `1x, 2x, 3x, ...`).

```html
<img src="img/100px.jpg" srcset="img/300px.jpg 3x, img/200px.jpg 2x, img/100px.jpg 1x">
<img src="img/100px.jpg" srcset="img/300px.jpg 300w, img/200px.jpg 200w, img/100px.jpg 100w">

```

If you want to use `srcset` in conjunction with the `:asset_hash` option, you need to employ the `image_path` helper, which is described [in this middleman section](/advanced/asset_pipeline.html):

```erb
<img src="<%= image_path('100px.jpg') %>" srcset="<%= image_path('300px.jpg') %> 3x, <%= image_path('200px.jpg') %> 2x, <%= image_path('100px.jpg') %> 1x">
```

The `srcset` attribute is not yet supported by all browsers as you can see at [caniuse.com](http://caniuse.com/#feat=srcset). If it is not supported the browser uses the `src` attribute as fallback. We noted, that some browsers use the first entry of the `srcset` as fallback. This is why we have put the largest image first in our example.
