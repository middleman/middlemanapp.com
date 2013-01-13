---
title: Custom Extensions
---

# Custom Extensions

Middleman extensions are Ruby classes which can hook into various points of the Middleman system, add new features and manipulate content.

The most basic extension looks like:

``` ruby
module MyFeature
  class << self
    def registered(app)
      
    end
    alias :included :registered
  end
end

::Middleman::Extensions.register(:my_feature, MyFeature) 
```

This module must be accessible to your `config.rb` file. Either define it directly in that file, or define it in another ruby file and `require` it in `config.rb`

Finally, once your module is included, you must activate it in `config.rb`:

``` ruby
activate :my_feature
```

The [`register`](http://rubydoc.info/github/middleman/middleman/master/Middleman/Extensions#register-class_method) method lets you choose the name your extension is activated with. It can also take a block if you want to require files only when your extension is activated.

In the `MyFeature` extension, the `registered` method will be called as soon as the `activate` command is run. The `app` variable is a [`Middleman::Application`](http://rubydoc.info/github/middleman/middleman/master/Middleman/Application) class. Using this class, you can augment the Middleman environment.

`activate` can also take an options hash (which are passed to `register`) or a block which can be used to configure your extension. 

``` ruby
module MyFeature
  # All the options for this extension
  class Options < Struct.new(:foo, :bar); end
  
  class << self
    def registered(app, options_hash={}, &block)
    options = Options.new(options_hash)
    yield options if block_given?
  end
end

# Two ways to configure this extension
activate :my_feature, :foo => 'whatever'
activate :my_feature do |f|
  f.foo = 'whatever'
  f.bar = 'something else'
end
```

Passing options to `activate` is generally preferred to setting global variables via `set` to configure your extension (see the next section).
    
## Setting variables

The [`Middleman::Application`](http://rubydoc.info/github/middleman/middleman/Middleman/Application) class can be used to change global settings (variables using the `set` command) that can be used in your extension.

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.set :css_dir, "lib/my/css"
    end
    alias :included :registered
  end
end
```

You can also use this ability to create new settings which can be accessed later in your extension.

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.set :my_feature_setting, %w(one two three)
      app.send :include, Helpers
    end
    alias :included :registered
  end
  
  module Helpers
    def my_helper
      my_feature_setting.to_sentence
    end
  end
end
```

`set` adds a new method to `Middleman::Application`, meaning you can read the value of your variable via `my_feature_setting` elsewhere. However, consider using `activate` options instead of global settings when only your extension needs a particular value.

## Adding Methods to config.rb

Methods available inside `config.rb` as simply class methods of `Middleman::Application`. Let's add a new method to be used in the `config.rb`:

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.extend ClassMethods
    end
    alias :included :registered
  end
  
  module ClassMethods
    def say_hello
      puts "Hello"
    end
  end
end
```

By extending the `Middleman::Application` class, available as `app`, we've added a `say_hello` method to the environment which simply prints "Hello". Internally, these methods are used to build lists of paths and requests which will be processed later in the app.

## after_configuration Callback

Sometimes you will want to wait until the `config.rb` has been executed to run code. For example, if you rely on the `:css_dir` variable, you should wait until it has been set. For this, we'll use a callback:

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.after_configuration do
        the_users_setting = app.settings.css_dir
        set :my_setting, "#{the_users_setting}_with_my_suffix"
      end
    end
    alias :included :registered
  end
end
```

### Compass Callback

Similarly, if you're extension relies on variable and settings within Compass to be ready, use the `compass_config` callback.

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.compass_config do |config|
        # config is the Compass.configuration object
        config.output_style = :compact
      end
    end
    alias :included :registered
  end
end
```

## Adding Helpers

Helpers are methods available inside your template. To add helper methods, we do the following:

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.helpers HelperMethods
    end
    alias :included :registered
  end

  module HelperMethods
    def make_a_link(url, text)
      "<a href='#{url}'>#{text}</a>"
    end
  end
end
```

Now, inside your templates, you will have access to a `make_a_link` method. Here's an example using an ERb template:

``` html
<h1><%= make_a_link("http://example.com", "Click me") %></h1>
```

## Request Callback

The request callback allows you to do processing before Middleman renders the page. This can be useful for returning data from another source, or failing early.

Here's an example:

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.before do
        app.set :currently_requested_path, request.path_info
        true
      end
    end
    alias :included :registered
  end
end
```

The above sets the `:currently_requested_path` value at the beginning of each request. Notice the return value of "true." All blocks using `before_processing` must return either true or false.

## Sitemap Extensions

You can modify or add pages in the [sitemap](/advanced/sitemap/) by creating a Sitemap extension. The [`:directory_indexes`](/pretty-urls/) extension uses this feature to reroute normal pages to their directory-index version, and the [blog extension](/blogging/) uses several plugins to generate tag and calendar pages. See [the `Sitemap::Store` class](http://rubydoc.info/github/middleman/middleman/Middleman/Sitemap/Store#register_resource_list_manipulator-instance_method) for more details.

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.after_configuration do
        sitemap.register_resource_list_manipulator(
          :my_feature,
          MyFeatureManipulator.new(self),
          false
        )
      end
    end
    alias :included :registered
  end
  
  class MyFeatureManipulator
    def initialize(app)
      @app = app
    end
    
    def manipulate_resource_list(resources)
      resources.each do |resource|
         resource.destination_path.gsub!("original", "new")
      end
    end
  end
end
```

## after_build Callback

This callback is used to execute code after the build process has finished. The [middleman-smusher] extension uses this feature to compress all the images in the build folder after it has been built. It's also conceivable to integrate a deployment script after build.

``` ruby
module MyFeature
  class << self
    def registered(app)
      app.after_build do |builder|
        builder.run my_deploy_script.sh
      end
    end
    alias :included :registered
  end
end
```

The [`builder`](http://rubydoc.info/github/middleman/middleman/master/Middleman/Cli/Build) parameter is the class that runs the build CLI, and you can use [Thor actions](http://rubydoc.info/github/wycats/thor/master/Thor/Actions) from it.

[middleman-smusher]: https://github.com/middleman/middleman-smusher