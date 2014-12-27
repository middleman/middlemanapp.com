---
title: Custom Extensions
---

# Custom Extensions

Middleman extensions are Ruby classes which can hook into various points of the
Middleman system, add new features and manipulate content. This guide explains
some of what's available, but you should read the middleman source and the
source of plugins like middleman-blog to discover all the hooks and extension
points.

## Basic Extension

The most basic extension looks like:

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end
  alias :included :registered
end

::Middleman::Extensions.register(:my_feature, MyFeature)
```

This module must be accessible to your `config.rb` file. Either define it
directly in that file, or define it in another ruby file and `require` it in
`config.rb`

Finally, once your module is included, you must activate it in `config.rb`:

``` ruby
activate :my_feature
```

The [`register`](http://rubydoc.info/gems/middleman-core/Middleman/Extensions#register-class_method)
method lets you choose the name your extension is activated with. It can also
take a block if you want to require files only when your extension is
activated.

In the `MyFeature` extension, the `initialize` method will be called as soon as
the `activate` command is run. The `app` variable is a
[`Middleman::Application`](http://rubydoc.info/gems/middleman-core/Middleman/Application)
class.

`activate` can also take an options hash (which are passed to `register`) or a
block which can be used to configure your extension. You define options with
the `options` class method and then access them with `options`:

``` ruby
class MyFeature < Middleman::Extension
  # All the options for this extension
  option :foo, false, 'Controls whether we foo'

  def initialize(app, options_hash={}, &block)
    super
    
    puts options.foo
  end
end

## Two ways to configure this extension
activate :my_feature, :foo => 'whatever'
activate :my_feature do |f|
  f.foo = 'whatever'
  f.bar = 'something else'
end
```

Passing options to `activate` is generally preferred to setting global
variables via `set` to configure your extension (see the next section).

## Setting variables

The [`Middleman::Application`](http://rubydoc.info/gems/middleman-core/Middleman/Application) class can be used to change global settings (variables using the `set` command) that can be used in your extension.

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
 
    app.set :css_dir, "lib/my/css"
  end
end
```

You can also use this ability to create new settings which can be accessed
later in your extension.

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
   
    app.set :my_feature_setting, %w(one two three)
  end

  helpers do
    def my_helper
      my_feature_setting.to_sentence
    end
  end
end
```

`set` adds a new method to `Middleman::Application`, meaning you can read the
value of your variable via `my_feature_setting` elsewhere. However, consider
using `activate` options instead of global settings when only your extension
needs a particular value.

## Adding Methods to config.rb

Methods available inside `config.rb` are simply class methods of
`Middleman::Application`. Let's add a new method to be used in the `config.rb`:

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
    app.extend ClassMethods
  end

  module ClassMethods
    def say_hello
      puts "Hello"
    end
  end
end
```

By extending the `Middleman::Application` class, available as `app`, we've
added a `say_hello` method to the environment which simply prints "Hello".
Internally, these methods are used to build lists of paths and requests which
will be processed later in the app.

## Adding Helpers

Helpers are methods available inside your template. To add helper methods, we
do the following:

``` ruby
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

Now, inside your templates, you will have access to a `make_a_link` method.
Here's an example using an ERb template:

``` html
<h1><%= make_a_link("http://example.com", "Click me") %></h1>
```


## Sitemap Manipulators

You can modify or add pages in the [sitemap](/advanced/sitemap/) by creating a
Sitemap extension. The [`:directory_indexes`](/basics/pretty_urls/) extension
uses this feature to reroute normal pages to their directory-index version, and
the [blog extension](/basics/blogging/) uses several plugins to generate tag
and calendar pages. See [the `Sitemap::Store`
class](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#register_resource_list_manipulator-instance_method)
for more details.

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end

  def manipulate_resource_list(resources)
    resources.each do |resource|
      resource.destination_path.gsub!("original", "new")
    end
  end
end
```

## Callbacks

There are many parts of the Middleman lifecycle that can be hooked into by
extensions. These are some examples, but there are many more.

### after_configuration

Sometimes you will want to wait until the `config.rb` has been executed to run
code. For example, if you rely on the `:css_dir` variable, you should wait
until it has been set. For this, we'll use a callback:

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end
  
  def after_configuration
    the_users_setting = app.settings.css_dir
    app.set :my_setting, "#{the_users_setting}_with_my_suffix"
  end
end
```

### before

The before callback allows you to do processing right before Middleman renders
the page. This can be useful for returning data from another source, or failing
early.

Here's an example:

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
    app.before do
      app.set :currently_requested_path, request.path_info
      true
    end
  end
end
```

The above sets the `:currently_requested_path` value at the beginning of each
request. Notice the return value of "true." All blocks using `before` must
return either true or false.

### after_build

This callback is used to execute code after the build process has finished. The
[middleman-smusher] extension uses this feature to compress all the images in
the build folder after it has been built. It's also conceivable to integrate a
deployment script after build.

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
    app.after_build do |builder|
      builder.run './my_deploy_script.sh'
    end
  end
end
```

The [`builder`](http://rubydoc.info/gems/middleman-core/Middleman/Cli/Build) parameter is the class that runs the build CLI, and you can use [Thor actions](http://rubydoc.info/github/wycats/thor/master/Thor/Actions) from it.

### compass_config

Similarly, if your extension relies on variable and settings within Compass to
be ready, use the `compass_config` callback.

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
    
    app.compass_config do |config|
      # config is the Compass.configuration object
      config.output_style = :compact
    end
  end
end
```

[middleman-smusher]: https://github.com/middleman/middleman-smusher
