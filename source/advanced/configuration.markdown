---
title: Configuration
---

# Configuration

## Discovering Middleman's Settings

Middleman is incredibly customizable, and extensions bring even more options to
the table. Rather than trying to keep up exhaustive documentation on each and
every setting, we've given Middleman the ability to tell you directly what
settings are available.

Once your preview service is running, visit
`http://localhost:4567/__middleman/config/` to see all the settings and
extensions available to you. Each one will include the setting name, a short
description, the default value, and what your site has it set to.

## Changing Settings

The most basic way to change a setting is to use `set` in your `config.rb`:

```ruby
set :js_dir, 'js'
```

You can also use a somewhat newer syntax:

```ruby
config[:js_dir] = 'js'
```

This is used for most of the global settings in Middleman.

## Configuring Extensions

Extensions are generally configured when they are activated. For most
extensions, you can either pass a hash of options when you `activate`, or use a
block to tweak options:

```ruby
activate :asset_hash, :exts => %w(.jpg) # Only hash for .jpg

# or:

activate :asset_hash do |opts|
  opts.exts += $(.ico)
end
```

## Environment-specific Settings

If you want some configuration to apply only during build or development, you
can put that in a block:

```ruby
configure :development do
  set :debug_assets, true
end

configure :build do
  activate :minify_css
end
```
