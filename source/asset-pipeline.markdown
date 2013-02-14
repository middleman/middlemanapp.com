---
title: Asset Pipeline
---

## Dependency Management (Sprockets)

[Sprockets] is a tool for managing libraries of Javascript (and CoffeeScript) code, declaring dependency management and include 3rd-party code. At its core, Sprockets makes a `require` method available inside your .js and .coffee files which can pull in the contents of an external file from your project or from a 3rd party gem.

Say I have a file called `jquery.js` which contains the jQuery library and another file called `app.js` which contains my application code. My app file can include jquery before it runs like so:

``` javascript
//= require "jquery"

$(document).ready(function() {
  $(".item").pluginCode({
    param1: true,
    param2: "maybe"
  });
});
```

This system also works within CSS files:

``` css
/*
 *= require base
 */

body {
  font-weight: bold;
}

```


[Sprockets]: https://github.com/sstephenson/sprockets
