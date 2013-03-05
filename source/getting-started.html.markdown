---
title: Getting Started
---

# Getting Started

Middleman is a command-line tool for creating static websites using all the shortcuts and tools of the modern web development environment.

Middleman assumes familiarity with the command-line. The Ruby language and the Sinatra web framework form the base of the tool. Familiarity with both will go a long way in helping users understand why Middleman works the way it does.

## Installation

Middleman is distributed using the RubyGems package manager. This means you will need both the Ruby language runtime installed and RubyGems to begin using Middleman.

Mac OS X comes prepackaged with both Ruby and Rubygems, however, some of the Middleman's dependencies need to be compiled during installation and on OS X that requires Xcode. Xcode can be installed via the [Mac App Store](http://itunes.apple.com/us/app/xcode/id497799835?ls=1&mt=12). Alternately, if you have a free Apple Developer account, you can just install Command Line Tools for Xcode from their [downloads page](https://developer.apple.com/downloads/index.action).

Once you have Ruby and RubyGems up and running, execute the following from the command line:

``` bash
gem install middleman
```

This will install Middleman, its dependencies and the command-line tools for using Middleman.

The installation process will add one new command to your environment, with 3 useful features:

* middleman init
* middleman server
* middleman build

The uses of each of these commands will be covered below.

## Starting a New Site: middleman init

To get started we will need to create a project folder for Middleman to work out of. You can do this using an existing folder or have Middleman create one for you using the `middleman init` command.

Simply point the command at the folder for your new site and Middleman will build a skeleton project in that folder (or create the folder for you).

``` bash
middleman init my_new_project
```

### The Skeleton

Every new project creates a basic web development skeleton for you. This automates the construction of a hierarchy of folders and files that you can use in all of your projects.

A brand-new project will contain a `source` folder and a `config.rb` file. The source folder is where you will build your website. The skeleton project contains folders for javascript, css and images, but you can change these to match your own personal preferences.

The `config.rb` file contains settings for Middleman and commented documentation on how to enable complex features such as compile-time compression and "blog mode."

#### Gemfile

Middleman will respect a Bundler Gemfile for locking down your gem dependencies. When creating a new project, Middleman will generate a Gemfile for you which specifies the same version of Middleman you are using. This will lock Middleman to this specific release series (the 2.x series, for example).

#### config.ru

A config.ru file describes how the site should be loaded by a Rack-enabled webserver. This file is provided as a convenience for users wishing to host their Middleman site in development mode on a Rack-based host such as Heroku.

To include a boilerplate `config.ru` file in your project, add the `--rack` flag to the init command:

``` bash
middleman init my_new_project --rack
```

### Project Templates

In addition to the default basic skeleton, Middleman comes with an optional project template based on the [HTML5 Boilerplate] project. Alternative templates can be accessed using the `-T` or `--template` command-line flags. For example, to start a new project based on HTML5 Boilerplate, run this command:

``` bash
middleman init my_new_boilerplate_project --template=html5
```

Finally, you can create your own custom template skeletons by creating folders in the `~/.middleman/` folder. For example, I can create a folder at `~/.middleman/mobile/` and fill it with files I intend to use on mobile projects.

If you run middleman init with the help flag, you will see a list of all the possible templates it has detected:

``` bash
middleman init --help
```

This will list my custom mobile framework and I can create new projects based on it as before:

``` bash
middleman init my_new_mobile_project --template=mobile
```
    
### Included Project Templates

Middleman ships with a number of basic project templates, including:

**[HTML5 Boilerplate](http://html5boilerplate.com/)** 

``` bash
middleman init my_new_mobile_project --template=html5
```

**[SMACSS](https://github.com/nsteiner/middleman-smacss)**

``` bash
middleman init my_new_mobile_project --template=smacss
```

### Community Project Templates

There are also a number of [community-developed project templates](/community/3rd-party-project-templates/).

## The Development Cycle (middleman server)

The Middleman separates your development and production code from the start. This allows you to utilize a bevy of tools (such as [HAML](http://haml-lang.com), [SASS](http://sass-lang.com), etc) during development that are unnecessary or undesirable in production.  We refer to these environments as The Development Cycle and the Static Site.

The vast majority of time spent using Middleman will be in the Development Cycle.

From the command-line, start the preview web-server from inside your project folder:

``` bash
cd my_project
bundle exec middleman server
```

This will start a local web server running at: http://localhost:4567/

You can create and edit files in the `source` folder and see the changes reflected on the preview web-server.

You can stop the preview server from the command-line using CTRL-C.

### Unadorned middleman command

Running `middleman` without any commands is the same as starting a server.

``` bash
bundle exec middleman
```

This will do exactly the same thing as `middleman server`.

### When something goes wrong

Under some circumstances (one known case is under Windows, see [here](https://github.com/middleman/middleman/issues/101)), `middleman` might not work as expected, try using a full command instead:

``` bash
middleman server -p 4567 -e development
```

Under some circumstances (say if your config file has gone wild), `middleman server` might not be able to boot itself, and no error output can be seen on the console, don't panic, just try `middleman build` to see the full trace of the problem and fix it.

## Exporting the Static Site (middleman build)

Finally, when you are ready to deliver static code or, in the case of "blog mode", host a static blog, you will need to build the site. Using the command-line, from the project folder, run `middleman build`:

``` bash
cd my_project
bundle exec middleman build
```

This will create a static file for each file located in your `source` folder. Template files will be compiled, static files will be copied and any enabled build-time features (such as compression) will be executed. You can pass a `--clean` flag to `middleman build` to have it clean out any files from the build directory that are left over from earlier builds but would no longer be produced.

[HTML5 Boilerplate]: http://html5boilerplate.com/
