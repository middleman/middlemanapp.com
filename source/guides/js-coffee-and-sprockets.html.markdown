---
title: Javascript, CoffeeScript and Sprockets
---

# Javascript, CoffeeScript and Sprockets

Middleman 2.0 embraces the Rails 3.1 Asset Pipeline and includes both Sprockets and CoffeeScript.

## Dependency Management (Sprockets)

Sprockets is a tool for managing libraries of Javascript (and CoffeeScript) code, declaring dependency management and include 3rd-party code. At its core, Sprockets makes a `require` method available inside your .js and .coffee files which can pull in the contents of an external file from your project or from a 3rd party gem.

Say I have a file called `jquery.js` which contains the jQuery library and another file called `app.js.coffee` which contains my application code written in CoffeeScript. My app file can include jquery before it runs like so:

    #= require "jquery"
    
    $(document).ready ->
      $(".item").pluginCode
        param1: true
        param2: "maybe"

The output of this file will be the jQuery library at the code and then the app code, compiled into Javascript, beneath.

You could also write your app file in regular Javascript with a file named `app.js` which looks like this:

    //= require "jquery"

    $(document).ready(function() {
      $(".item").pluginCode({
        param1: true,
        param2: "maybe"
      });
    });

The output would be identical.

## CoffeeScript

[CoffeeScript] is a white-space aware language which compiles to Javascript. Here's the blurb from their website:

> CoffeeScript is a little language that compiles into JavaScript. Underneath all of those embarrassing braces and semicolons, JavaScript has always had a gorgeous object model at its heart. CoffeeScript is an attempt to expose the good parts of JavaScript in a simple way.

> The golden rule of CoffeeScript is: "It's just JavaScript". The code compiles one-to-one into the equivalent JS, and there is no interpretation at runtime. You can use any existing JavaScript library seamlessly (and vice-versa). The compiled output is readable and pretty-printed, passes through JavaScript Lint without warnings, will work in every JavaScript implementation, and tends to run as fast or faster than the equivalent handwritten JavaScript.

For more information, check out [the wonderful Peepcode screencast].

[CoffeeScript]: http://jashkenas.github.com/coffee-script/
[the wonderful Peepcode screencast]: http://peepcode.com/products/coffeescript
[Sprockets]: https://github.com/sstephenson/sprockets