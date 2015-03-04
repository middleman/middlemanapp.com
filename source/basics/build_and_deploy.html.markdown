---
title: Build & Deploy
---

# Exporting the Static Site

## Building the site with "middleman build"

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

## Deploying the site

After building the site you have everything you need within the
`build`-directory. There are nearly limitless ways to deploy a static build. So
we present our very own solution for this here. Feel free to search the web or
look at our [extension
directory](https://directory.middlemanapp.com/#/extensions/deployment) for more
alternatives to deploy `middleman`. If you are an author of a deployment tool
suitable to deploy `middleman`, please make a PR
[here](https://directory.middlemanapp.com/#/extensions/deployment).

A very handy tool to deploy a build is
[`middleman-deploy`](https://github.com/middleman-contrib/middleman-deploy). It
can deploy a site via rsync, ftp, sftp, or git.

```bash
$ middleman build [--clean]
$ middleman deploy [--build-before]
```
