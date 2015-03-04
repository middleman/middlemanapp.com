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
`build`-directory. Now you've got different options to deploy your site:

1. Use `tar` and pipe it to `ssh`
2. Copy the `build`-directory using `scp`
3. Use `capistrano` to deploy your app

### Use "tar" and pipe it to "ssh"

You can copy the file to the server by piping `tar`'s output via `ssh` to a
remote `tar`.

```bash
$ bundle exec middleman build
$ tar -vczf - -C build/ . | ssh user@server 'tar -xvzf - -C /srv/http'  
```

### Copy the "build"-directory using "scp"

Another alternative is to copy files using `scp`.

```bash
$ bundle exec middleman build
$ scp -pr build/* user@server:/srv/http/
```

### Use "capistrano" to deploy your app

If you like [`capistrano`](https://github.com/capistrano/capistrano) to deploy
your apps, you will like
[`capistrano-middleman`](https://github.com/fedux-org/capistrano-middleman).
Just follow the instructions in the
[README](https://github.com/fedux-org/capistrano-middleman/blob/master/README.md)
to install that gem.

```bash
$ bundle exec capistrano production deploy
```
