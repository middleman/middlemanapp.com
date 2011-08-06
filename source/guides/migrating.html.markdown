---
title: Migrating to Middleman 2.0
---

# Migrating to Middleman 2.0

In addition to dozens of new features, Middleman 2.0 has several changes to the way earlier versions managed projects.

## Unified middleman command

In previous versions, Middleman shipped with 3 commands: `mm-init`, `mm-server` and `mm-build`. These tools were responsible for the 3 phases of project development: creation, construction and compilation. In version 2.0, these 3 commands (along with a new migrate command) have been unified into a single command. These change was made to make the name of the command, `middleman`, more obvious than the abbreviated mm- prefix and to follow the lead of the Rails and Compass projects which uses a unified `rails` and `compass` command respectively.

For the 2.0 series, the three old command will still be available to allow users time to migrate their scripts, tutorials and muscle memory to the new commands. In a future 2.x, these commands will be removed and only the new middleman command will be available.

As of 2.0, the following commands are possible:

* `middleman init NEW_PROJECT_FOLDER`
* `middleman server`
* `middleman build`
* `middleman migrate`
* `middleman version`

## Unified Source Folder

One of the biggest changes with Middleman 2.0 is the unification of both static files and template files into a single source folder.

In Middleman version 1, projects contained a `public` folder for static resources (images, javascripts and stylesheets) and a `views` folder for templated files (erb, haml, sass, coffee).

This awkward split is a remnant of the way Rails 3.0 and earlier divided files into static and templated content. This split, and the poor naming, caused problems for new users. Therefore, Middleman 2.0 has combined these folders into a single `source` folder. The entirety of your project, both static and templated files, live along side each other.

Middleman 2.0 does not offer a backwards compatibility mode. Old project must merge their folders to update Middleman. The process for doing this is fairly simple. From the terminal:

    :::bash
    cd MY_PROJECT_FOLDER
    mv public source
    cp -R views/* source/
    rm -rf views

As a convenience, Middleman 2.0 comes with a single command for running this migration.

    :::bash
    cd MY_PROJECT_FOLDER
    middleman migrate

## Removed/Deprecated Methods

Some methods which have been renamed or deprecated for a long time have finally been removed.

### enable -> activate

In your `config.rb`, you should be using activate to enable features like so:

    :::ruby
    activate :relative_assets

In the past, `enable` also worked. This old name has been removed. Always use `activate`.

### mime (add new mime types)

This method allowed the addition of new mime types to Middleman. This is now available higher up the stack, provided by Sinatra, as `mime_type`. Use as before:

    :::ruby
    mime_type "pdf", "application/pdf"

## Removed Features

One, rarely used, feature has been removed.

### Ugly Haml

The Ugly Haml feature told Haml to output unindented text. This was useful as a means of obfuscation. The `ugly_haml` feature has been removed from Middleman 2.0, but you can still achieve the same effect by setting the Haml options directly:

    :::ruby
    set :haml, { :ugly => true }