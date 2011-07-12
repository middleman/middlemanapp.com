---
title: Getting Started
---

# Getting Started

The Middleman is a command-line tool for creating static websites using all the shortcuts and tools of the modern web development environment. 

Middleman assumes familiarity with the command-line. The Ruby language and the Sinatra web framework form the base of the tool. Familiarity with both will go a long way in helping users understand why Middleman works the way it does.

## Installation

Middleman is distributed using the RubyGems package manager. This means you will need both the Ruby language runtime installed and RubyGems to begin using Middleman. 

Mac OS X comes prepackaged with both Ruby and Rubygems, however, some of the Middleman's dependencies need to be compiled during installation and on OS X that requires XCode. XCode can be installed via the [Mac App Store](http://itunes.apple.com/us/app/xcode/id422352214?mt=12).

Windows users will need to install Ruby using [The RubyInstaller for Windows]. Windows users should also install [The RubyInstall DevKit] to install Middleman's compiled dependencies.

Once you have Ruby and RubyGems up and running, execute the following from the command line:

    gem install middleman --pre

This will install Middleman, its dependencies and the command-line tools for using Middleman.

The installation process will add three commands to your environment:

* mm-init
* mm-server
* mm-build

The uses of each of these commands will be covered below.

## Starting a New Site: mm-init

To get started we will need to create a project folder for Middleman to work out of. You can do this using an existing directory or have Middleman create one for you using the mm-init command.

Simply point the command at the folder for your new site and Middleman will build a skeleton project in that folder (or create the folder for you).

    :::bash
    mm-init my_new_project

### The Skeleton

Every new project creates a basic web development skeleton for you. This automates the construction of a hierarchy of folders and files that you can use in all of your projects.

A brand-new project folder will contain a source/ directory, a config.rb file and a config.ru file. The source directory is where you will build your website. The skeleton project contains folders for javascript, css and images, but you can change these to match your own personal preferences.

The config.rb file contains settings for Middleman and commented documentation on how to enable complex features such as compile-time compression and "blog mode."

The config.ru file describes how the site should be loaded by a Rack-enabled webserver. This file is provided as a convenience for users wishing to host their Middleman site in development mode on a Rack-based host such as Heroku. In "blog mode" this file describes how the web server can serve static files from the Middleman build/ folder.

### Project Templates

In addition to the default basic skeleton, Middleman comes with an optional project template based on the [HTML5 Boilerplate] project. Alternative templates can be accessed using the -t or --template command-line flags. For example, to start a new project based on HTML5 Boilerplate, run this command:

    :::bash
    mm-init my_new_boilerplate_project --template=html5

Finally, you can create your own custom template skeletons by creating folders in the ~/.middleman/ folder. For example, I can create a folder at ~/.middleman/mobile/ and fill it with files I intend to use on mobile projects 

If you run mm-init with the help flag, you will see a list of all the possible templates it has detected:

    :::bash
    mm-init --help

This will list my custom mobile framework and I can create new projects based on it as before:

    :::bash
    mm-init my_new_mobile_project --template=mobile

## The Development Cycle (mm-server)

The Middleman separates your development and production code from the start. This allows you to utilize a bevy of tools (such as HAML, SASS, etc) during development that are unnecessary or undesirable in production.  We refer to these environments as The Development Cycle and the Static Site.

The vast majority of time spent using Middleman will be in the Development Cycle. 

From the command-line, start the preview webserver from inside your project directory:

    :::bash
    cd my_project
    mm-server

This will start a local web server running at: http://localhost:4567/

You can create and edit files in the source/ folder and see the changes reflected on the preview webserver.

Note that changes to the config.rb file will require mm-server to be restarted before they take effect. You can stop the preview server from the command-line using CTRL-C.

## Exporting the Static Site (mm-build)

Finally, when you are ready to deliver static code or, in the case of "blog mode," host a static blog, you will need to build the site. Using the command-line, from the project folder, run mm-build:

    :::bash
    cd my_project
    mm-build
    
This will create a static file for each file located in your source/ directory. Template files will be compiled, static files will be copied and any enabled build-time features (such as compression) will be executed.

[The RubyInstaller for Windows]: http://rubyinstaller.org/
[The RubyInstall DevKit]: http://rubyinstaller.org/add-ons/devkit/
[HTML5 Boilerplate]: http://html5boilerplate.com/