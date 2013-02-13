---
title: Minify HTML
---

# Minify HTML

Middleman provides an official extension for minifying its html output. Simply install the gem:

``` bash
gem install middleman-minify-html
```

Add `middleman-minify-html` to your Gemfile, open your `config.rb` and add

``` ruby
activate :minify_html
```

Then simply restart the middleman webserver.

``` bash
middleman server
```

You should notice whilst view-source:'ing that your html is now being minified.