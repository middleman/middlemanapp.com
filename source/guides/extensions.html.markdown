---
title: Custom Extensions
---

# Custom Extensions

Middleman extensions are Ruby classes which can hook into various points of the Middleman system, add new features and manipulate content.

The most basic extension looks like:

    :::ruby
    module Middleman::Features::MyFeature
      class << self
        def registered(app)
          
        end
        alias :included :registered
      end
    end

All features must be namespaced under the `Middleman::Features` prefix. This module must be accessible to your `config.rb` file. Either define it directly in that file, or define it in another ruby file and `require` it in `config.rb`

Finally, once your module is included, you must activate it in `config.rb`:

    :::ruby
    activate :my_feature

The name of your feature is the lowercased and underscored version of the original `MyFeature` module name.

In the `MyFeature` extension, the registered method will be called as soon as the `activate` command is run. The `app` variable is a `Middleman::Base` class. Using this class, you can augment the Middleman environment.

## Setting variables

The `Middleman::Base` class can be used to change settings (variables using the set command) in your extension.

    :::ruby
    module Middleman::Features::MyFeature
      class << self
        def registered(app)
          app.set :css_dir, "lib/my/css"
        end
        alias :included :registered
      end
    end

You can also use this ability to create new settings which can be accessed later in your extension.

    :::ruby
    module Middleman::Features::MyFeature
      class << self
        def registered(app)
          app.set :my_feature_interal_array, %w(one two three)
        end
        alias :included :registered
      end
    end

## Adding Methods to config.rb

Methods available inside `config.rb` as simply class methods of `Middleman::Base`. Let's add a new method to be used in the `config.rb`:

    :::ruby
    module Middleman::Features::MyFeature
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

By extending the `Middleman::Base` class, available as `app`, we've added a `say_hello` method to the environment which simply prints "Hello". Internally, these methods are used to build lists of paths and requests which will be processed later in the app.

## after_configuration Callback

Sometimes you will want to wait until the `config.rb` has been parsed to run code. For example, if you rely on the `:css_dir` variable, you should wait until it has been set. For this, we'll use a callback:

    :::ruby
    module Middleman::Features::MyFeature
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

### Compass Callback

Similarly, if you're extension relies on variable and settings within Compass to be ready, use the `compass_config` callback.

    :::ruby
    module Middleman::Features::MyFeature
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

## Adding Helpers

Helpers are methods available inside your template. To add helper methods, we do the following:

    :::ruby
    module Middleman::Features::MyFeature
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

Now, inside your templates, you will have access to a `make_a_link` method. Here's an example using an ERb template:

    :::erb
    <h1><%= make_a_link("http://example.com", "Click me") %></h1>

## Request Callback

The request callback allows you to do processing before Middleman renders the page. This can be useful for returning data from another source, or failing early. The internal `:front_matter` extension uses this hook to parse YAML inside the template files before the templates are rendered.

Here's an example:

    :::ruby
    module Middleman::Features::MyFeature
      class << self
        def registered(app)
          app.before_processing do
            app.set :currently_requested_path, request.path_info
            true
          end
        end
        alias :included :registered
      end
    end

The above sets the `:currently_requested_path` value at the beginning of each request. Notice the return value of "true." All blocks using `before_processing` must return either true or false. True allows the processing to continue after the callback in run. False halts the processing immediately. Internal extensions use this ability to set 404 headers on missing files and halt processing if they don't exist.

## build_reroute Callback

During the build cycle, you can hook into any file which Middleman is intending to generate and either cancel that file creation or change internal build target.

    :::ruby
    module Middleman::Features::MyFeature
      class << self
        def registered(app)
          app.build_reroute do |destination, request_path|
            if request_path =~ /my-most-hated-word/
              # Exceptions will cause the file to not be created
              throw
            elsif request_path =~ /my-safe-word/
              # False will allow the file to be create as normal
              false
            else
              # Returning an array will change the destination
              # and source variables during the build.
              [
                destination.gsub("original", "new"),
                request_path
              ]
            end
          end
        end
        alias :included :registered
      end
    end

## after_build Callback

This callback is used to execute code after the build process has finished. The [middleman-smusher] extension uses this feature to compress all the images in the build folder after it has been build. It's also conceivable to integrate a deployment script after build.

    :::ruby
    module Middleman::Features::MyFeature
      class << self
        def registered(app)
          app.after_build do
            `my_deploy_script.sh`
          end
        end
        alias :included :registered
      end
    end

## Sinatra 

The Middleman::Base class is an extension of Sinatra. Therefore the `app` variable also has access to Sinatra routes for additional customization:

    :::ruby
    module Middleman::Features::MyFeature
      class << self
        def registered(app)
          app.get "/hello" do
            "World"
          end
        end
        alias :included :registered
      end
    end

The above will show a page with the contents "World" for all request to: 

    http://localhost:4567/hello

Be aware, this only effects the Middleman preview server, not the built output.

[middleman-smusher]: https://github.com/middlemanapp/middleman-smusher