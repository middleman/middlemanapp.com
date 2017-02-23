---
title: Custom Extensions
---

# Custom Extensions

Middleman extensions are Ruby classes which can hook into various points of the Middleman system, add new features and manipulate content. This guide explains some of what's available, but you should read the middleman source and the source of plugins like middleman-blog to discover all the hooks and extension points.

## Bootstrapping a new extension

To bootstrap a new extension you can use the `extension`-command. This will create all needed files.

```bash
middleman extension middleman-my_extension

# create  middleman-my_extension/.gitignore
# create  middleman-my_extension/Rakefile
# create  middleman-my_extension/middleman-my_extension.gemspec
# create  middleman-my_extension/Gemfile
# create  middleman-my_extension/lib/middleman-my_extension/extension.rb
# create  middleman-my_extension/lib/middleman-my_extension.rb
# create  middleman-my_extension/features/support/env.rb
# create  middleman-my_extension/fixtures
```

## Basic Extension

The most basic extension looks like:

```ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end
  alias :included :registered
end

::Middleman::Extensions.register(:my_feature, MyFeature)
```

This module must be accessible to your `config.rb` file. Either define it directly in that file, or define it in another ruby file and `require` it in `config.rb`

Finally, once your module is included, you must activate it in `config.rb`:

```ruby
activate :my_feature
```

The [`register`](http://rubydoc.info/gems/middleman-core/Middleman/Extensions#register-class_method) method lets you choose the name your extension is activated with. It can also take a block if you want to require files only when your extension is activated.

In the `MyFeature` extension, the `initialize` method will be called as soon as the `activate` command is run. The `app` variable is an instance of
[`Middleman::Application`](http://rubydoc.info/gems/middleman-core/Middleman/Application) class.

`activate` can also take an options hash (which are passed to `register`) or a block which can be used to configure your extension. You define options with the `options` class method and then access them with `options`:

```ruby
class MyFeature < Middleman::Extension
  # All the options for this extension
  option :foo, false, 'Controls whether we foo'

  def initialize(app, options_hash={}, &block)
    super

    puts options.foo
  end
end

# Two ways to configure this extension
activate :my_feature, foo: 'whatever'
activate :my_feature do |f|
  f.foo = 'whatever'
  f.bar = 'something else'
end
```

Passing options to `activate` is preferred to setting global or singleton variables.

## Adding Methods to config.rb

Methods within your extension can be made available to `config.rb` with the `expose_to_config` method.

```ruby
class MyFeature < Middleman::Extension
  expose_to_config :say_hello

  def say_hello
    puts "Hello"
  end
end
```

## Adding Methods to templates

Similar to config, methods can be exposed to the template context:

```ruby
class MyFeature < Middleman::Extension
  expose_to_template :say_hello

  def say_hello
    "Hello Template"
  end
end
```

## Adding Helpers

Another way to add methods to templates are as helpers. Unlike "exposed methods" above, helpers do not have access to the rest of your extension. These are good for large packages of "pure" methods grouped into a module. In most cases, the above "exposed methods" are preferred.

```ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end

  helpers do
    def make_a_link(url, text)
      "<a href='#{url}'>#{text}</a>"
    end
  end
end
```

Now, inside your templates, you will have access to a `make_a_link` method. Here's an example using an ERB template:

```erb
<h1><%= make_a_link("http://example.com", "Click me") %></h1>
```

## Sitemap Manipulators

You can modify or add pages in the [sitemap](/advanced/sitemap/) by creating a Sitemap extension. The [`:directory_indexes`](/advanced/pretty-urls/) extension uses this feature to reroute normal pages to their directory-index version, and the [blog extension](/basics/blogging/) uses several plugins to generate tag and calendar pages. See [the `Sitemap::Store` class](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#register_resource_list_manipulator-instance_method) for more details.

**Note:** `manipulate_resource_list` is a "reducer". It is required to return the full set of resources to be passed to the next step of the pipeline.

```ruby
class MyFeature < Middleman::Extension
  def manipulate_resource_list(resources)
    resources.each do |resource|
      resource.destination_path.gsub!("original", "new")
    end

    resources
  end
end
```

## Callbacks

There are many parts of the Middleman lifecycle that can be hooked into by extensions. These are some examples, but there are many more.

### after_configuration

Sometimes you will want to wait until the `config.rb` has been executed to run code. For example, if you rely on the `:css_dir` variable, you should wait until it has been set. For this, we'll use a callback:

```ruby
class MyFeature < Middleman::Extension
  def after_configuration
    puts app.config[:css_dir]
  end
end
```

### `after_build`

This callback is used to execute code after the build process has finished. The [middleman-smusher](https://github.com/middleman/middleman-smusher) extension uses this feature to compress all the images in the build folder after it has been built. It's also conceivable to integrate a deployment script after build.

```ruby
class MyFeature < Middleman::Extension
  def after_build(builder)
    builder.thor.run './my_deploy_script.sh'
  end
end
```

The [`builder.thor`](http://rubydoc.info/gems/middleman-core/Middleman/Cli/Build) parameter is the class that runs the build CLI, and you can use [Thor actions](http://rubydoc.info/github/wycats/thor/master/Thor/Actions) from it.
