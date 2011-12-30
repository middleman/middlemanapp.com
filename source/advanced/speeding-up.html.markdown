---
title: Speeding Things Up
---

# Speeding Things Up

Many of the components of Middleman can automatically take advantage of faster tools if they are available on the system. However, these tools are often slow to compile and not cross-platform so they are not included by default.

## Ruby 1.9

The latest version of Ruby, version 1.9.x, is much faster than its predecessor. Upgrading your installed version of Ruby will speed up both Middleman and its dependencies. However, Ruby versions do not share gems, so you will need to reinstall Middleman in this new environment.

## Javascript Compilation

Install `therubyracer` which uses the Google Chrome engine to compile Javascript.

    :::bash
    gem install therubyracer
    
## LiveReload and config.rb update speed

### Mac OS X

Install `rb-fsevent`:

    :::bash
    gem install rb-fsevent

### Linux

Install `rb-inotify`:

    :::bash
    gem install rb-inotify

### Windows

Nothing to do, `rb-fchange` is already installed.