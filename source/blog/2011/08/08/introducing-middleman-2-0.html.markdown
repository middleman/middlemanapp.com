---
title: Introducing Middleman 2.0
date: 2011/08/08
---

# Introducing Middleman 2.0

Middleman 2.0 is a huge release featuring a refactored core, a unified source folder, a unified command line, tons of new features and a [full documentation website].

As always, install via RubyGems:

    :::bash
    gem install middleman
    
For more information, read the [Getting Started] guide.

Here's a quick overview of everything that's changed.

## Unified Source Folder

The `public` and `views` folders have been combined into a single `source` folder which contains all of your files. Use the [migration tool] to quickly update your folder structure, or manually combine the folders.

## Unified Command

The old commands, `mm-init`, `mm-server` and `mm-build`, have been combined into a single `middleman` command with the following subcommands:

* `middleman init`
* `middleman server`
* `middleman build`

## New Features

Here are the most interesting new features of Middleman 2.0.

### Sprockets

Sprockets is a tool for Javascript dependency management. Using Sprockets you can include other Javascript and CoffeeScript files into your scripts. 

    :::javascript
    //= require "another_file"
    
    function my_javascript() {
      
    }
    
Read more in the [Javascript, CoffeeScript and Sprockets] guide.

### Dynamic Pages

Dynamic pages allow you to generate HTML for files which share a single template. 

    :::ruby
    ["tom", "dick", "harry"].each do |name|
      page "/about/#{name}.html", :proxy => "/about/template.html" do
        @person_name = name
      end
    end

Read more in the [Dynamic Pages] guide.

### Pretty URLs

Pretty URLs (aka Directory Indexes) let you generate folders for each HTML file in your project which results in a pretty, extension-less URL in common web-servers.

    :::ruby
    activate :directory_indexes

Now `source/my-page.html` will generate `build/my-page/index.html`.

Read more in the [Pretty URLs] guide.

### YAML Frontmatter

YAML Frontmatter lets you add in-template variables at the top of a page, which are also available in the layout, and to configure which layout the page uses.

    :::erb
    ---
    layout: "login"
    page_name: "Login"
    ---
    
    <h1><%= data.page.page_name %></h1>

The above `login.html.erb` file will be rendered using the `login.erb` layout file.

Read more in the [Individual Page Configuration] guide.

### LiveReload

By default, LiveReload will monitor your `config.rb` file and automatically restart the Middleman server if it changes. This means, activating new features no longer requires a server restart.

In addition, you can have LiveReload monitor your project files as well and instruct the web-browser to reload when they change using the [LiveReload Extension] and the `--livereload` flag.

    :::bash
    middleman server --livereload

## Migrating to 2.0

Updating old projects to Middleman 2.0 is very easy. Simply use the new `migrate` command:

    :::bash
    middleman migrate

Read more about the migration edge cases in the [Migrating to Middleman 2.0] guide.

## Support

If there are any issues or regressions, please log bugs on the [Github Issue Tracker].

[full documentation website]: http://middlemanapp.com
[migration tool]: /guides/migrating
[Javascript, CoffeeScript and Sprockets]: /guides/js-coffee-and-sprockets
[Dynamic Pages]: /guides/dynamic-pages
[Pretty URLs]: /guides/pretty-urls
[Individual Page Configuration]: /guides/individual-page-configuration
[LiveReload Extension]: https://github.com/mockko/livereload#readme
[Getting Started]: /guides/getting-started
[Migrating to Middleman 2.0]: /guides/migrating
[Github Issue Tracker]: https://github.com/tdreyno/middleman/issues