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

Your browser will now reload changed pages automatically.