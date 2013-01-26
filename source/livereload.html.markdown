---
title: LiveReload
---

# LiveReload

Middleman provides an official extension for livereloading functionality. Simply install the gem:

``` bash
gem install middleman-livereload
```

Add `middleman-livereload` to your Gemfile, open your `config.rb` and add

``` ruby
activate :livereload
```

Then simply restart the middleman webserver.

``` bash
middleman server
```

Your browser will reload changed pages automatically.

NOTE: [middleman 3.0.4 breaks middleman-livereload](https://github.com/middleman/middleman-livereload/issues/10)
