---
title: Starting a New Site
---

# Starting a New Site

To get started we will need to create a project folder for Middleman to work
out of. You can do this using an existing folder or have Middleman create one
for you using the `middleman init` command.

```bash
$ middleman init
```

builds a Middleman skeleton project in your current folder.

```bash
$ middleman init my_new_project
```

creates a subfolder `my_new_project` with the Middleman skeleton project.

## The Skeleton

Every new project creates a basic web development skeleton for you. This
automates the construction of a standard hierarchy of folders and files that
you can use in all of your projects.

A brand-new project will contain a `source` folder and a `config.rb` file. The
source folder is where you will build your website. The skeleton project
contains folders for JavaScript, CSS and images, but you can change these to
match your own personal preferences.

The `config.rb` file contains [settings for Middleman][settings].

### `Gemfile`

Middleman will respect a Bundler `Gemfile` for specifying and controlling your
gem dependencies. When creating a new project, Middleman will generate a
`Gemfile` for you which specifies the same version of Middleman you are using.
This will lock Middleman to this specific release series (the 4.0.x series, for
example). All plugins and extra libraries you use in your project should be
listed in your `Gemfile`, and Middleman will automatically `require` all of them
when it starts.

### `config.ru`

A `config.ru` file describes how the site should be loaded by a Rack-enabled
web server. If you'd like to host your Middleman site in development mode on a
Rack-based host such as Heroku, you can add a `config.ru` file at the root of
your project with the following contents:

```ruby
require 'middleman/rack'
run Middleman.server
```

Remember that Middleman is built to generate *static* sites, though. This
scenario is not a primary use-case.

  [settings]: /advanced/configuration/
