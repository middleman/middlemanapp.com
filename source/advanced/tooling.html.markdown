---
title: Tooling
---

# Tooling

## Run multiple "middleman"-sites in development mode in parallel

By default `middleman` uses port `4567`. If you use it for multiple projects
and run multiple instances of `middleman`, you may know this error message:

```
== Port 4567 is unavailable. Either close the instance of Middleman already
running on 4567 or start this Middleman on a new port with: --port=4568
```

To work around this problem you can use the following script. It uses a random
port between 1024 and 65536 and uses `launchy` to open your `middleman`-site in
your default browser.


```ruby
#!/usr/bin/env ruby

require 'launchy'
require 'bundler'

Bundler.require

port = (1024..65535).to_a.sample

Launchy.open "http://127.0.0.1:#{port}"
system("middleman server -p #{port}")
```

Please do not forget to add `launchy` to your `Gemfile`.

```ruby
gem 'launchy'
```
