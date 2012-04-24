---
title: Speeding Things Up
---

# Speeding Things Up

Some of the components of Middleman can automatically take advantage of faster tools if they are available on the system. However, these tools are often slow to compile and not cross-platform so they are not included by default.

## Ruby 1.9

The latest version of Ruby, version 1.9.3, is much faster than its predecessor. Upgrading your installed version of Ruby will speed up both Middleman and its dependencies. However, Ruby versions do not share gems, so you will need to reinstall Middleman in this new environment.

## Javascript Compilation

Use `therubyracer` which uses the Google Chrome engine to compile Javascript. Using a faster JSON parser can also speed up Javascript magnification. In your Gemfile:

    :::ruby
    gem 'therubyracer' # faster ExecJS
    gem 'oj'           # faster JSON
    # gem 'yajl-ruby'  # if 'oj' doesn't work for you
    
Don't forget to run `bundle install`!
