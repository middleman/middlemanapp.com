---
title: Pretty URLs (Directory Indexes)
---

# Pretty URLs (Directory Indexes)

By default Middleman will output the files exactly as you have described them in your project. For example a `about-us.html.erb` file in the `source` folder will be output as `about-us.html` when you build the project. If you were to place this project in the root of a web-server at `example.com`, then this page would be accessible at:

    http://example.com/about-us.html

This makes sense for a static website, but many file the .html distasteful and would prefer a clean (or pretty) extension-less URL. There are two ways to accomplish this.

## Ruby Web-server

If you are using a Rack-based web-server, you can use the `Rack::TryStatic` middleware found in the [rack-contrib] project. In your `config.ru` (or Rails Rack configuration), add the following:

    :::ruby
    require "rack/contrib/try_static"
    use Rack::TryStatic, :root => "build", :urls => %w[/], :try => ['.html']

The same `about-us.html` file would be accessible at:

    http://example.com/about-us

## Apache (and compatible server)

If you are not using a Rack-based web-server, you can use the Directory Indexes feature to tell Middleman to create a folder for each `.html` file and place the built template file as the index of that folder. In your `config.rb`:

    :::ruby
    activate :directory_indexes

Now when the above project is built, the `about-us.html.erb` file will be output as `about-us/index.html`. When placed in an Apache compatible web-server, the page would be available at:

    http://example.com/about-us
    
If you prefer a different file be output, you can use the `index_file` variable. For example, IIS uses default.html:

    :::ruby
    set :index_file, "default.html"

Or, you may want a PHP file:

    :::ruby
    set :index_file, "index.php"

### Opt-out

If there are pages which you don't want automatically renamed, you can opt-out:

    :::ruby
    page "/i-really-want-the-extension.html", :directory_index => false

### Manual Indexes

If your template file is already named `index.html` it will pass through Middleman untouched. For example, `my-page/index.html.erb` will generate `my-page/index.html` as you would expect.

[rack-contrib]: https://github.com/rack/rack-contrib/