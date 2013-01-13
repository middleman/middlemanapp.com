---
title: LiveReload
---

# LiveReload

Middleman provides an official extension for livereloading functionality. Simply install the gem:

``` bash
gem install middleman-livereload
```

Add `middleman-livereload` to your Gemfile and open your `config.rb` and add

``` ruby
activate :livereload
```

Simply start middleman webserver again.

``` bash
middleman server
```

Your browser will reload changed pages automatically.

NOTE: [middleman 3.0.4 breaks middleman-livereload](https://github.com/middleman/middleman-livereload/issues/10)
