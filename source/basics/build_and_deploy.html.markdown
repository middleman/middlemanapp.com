---
title: Build & Deploy
---

# Exporting the Static Site (middleman build)

Finally, when you are ready to deliver static code or, in the case of "blog
mode", host a static blog, you will need to build the site. Using the
command-line, from the project folder, run `middleman build`:

``` bash
$ cd my_project
$ bundle exec middleman build
```

This will create a static file for each file located in your `source` folder.
Template files will be compiled, static files will be copied and any enabled
build-time features (such as compression) will be executed. Middleman will
automatically clean out files from the build directory for you that are left
over from earlier builds but would no longer be produced.
