---
title: Rack Middleware
---

# Rack Middleware

Rack is a system of classes that can modify content on-the-fly and intercept
requests before they are processed by the server (Middleman).

Middleman has full access to Rack Middleware which opens up an expansive
universe of libraries which work with Middleman.

## Example: Syntax Highlighting

**Note:** The official
[middleman-syntax](https://github.com/middleman/middleman-syntax) extension is
a better way to do syntax highlighting - what follows is just an example of how
to use Rack middleware.

This site is written in Middleman and features many code blocks which have
syntax highlighting. This syntax highlighting is accomplished outside the scope
of Middleman. This site renders `<code>` blocks and then Rack Middleware takes
over and enhances those blocks with syntax highlight. The middleware in use is
called [`Rack::Codehighlighter`](https://github.com/wbzyl/rack-codehighlighter).
Here's how it can be used in your `config.rb`:

``` ruby
require 'rack/codehighlighter'
require "pygments"
use Rack::Codehighlighter,
  :pygments,
  :element => "pre>code",
  :pattern => /\A:::([-_+\w]+)\s*\n/,
  :markdown => true
```

Make sure you add the right dependencies to your `Gemfile` to make those work:

``` ruby
gem "rack-codehighlighter", :git => "git://github.com/wbzyl/rack-codehighlighter.git"
gem "pygments.rb"
```

The above block required the `rack/codehighlighter` and `pygments.rb`
libraries. Then the `use` command tells Middleman to use this middleware. The
rest is standard Rack Middleware setup, passing some variables to the
middleware itself instructing the syntax highlighter on how to locate code
blocks and which backend to use for parsing the syntax.

## Build Cycle

The Rack Middleware is run on all requests, including those done during the
build cycle. This means anything the Rack Middleware effects during preview
will be present in the built files. However, be aware that once the project is
built, it is a static site. Rack Middleware which does processing on requests,
expecting things like cookies, sessions or variables, won't work once the site
is built.

## Useful Middleware

* [Rack::GoogleAnalytics]
* [Rack::Tidy]
* [Rack::Validate]
* [Rack::SpellCheck]

[Rack::GoogleAnalytics]: https://github.com/ambethia/rack-google_analytics
[Rack::Tidy]: https://github.com/rbialek/rack-tidy
[Rack::Validate]: https://gist.github.com/235715
[Rack::SpellCheck]: https://gist.github.com/235097
