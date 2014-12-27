# Project Templates

In addition to the default basic skeleton, Middleman comes with several
optional project templates based on the [HTML5 Boilerplate] project, [SMACSS],
and [Mobile Boilerplate](http://html5boilerplate.com/mobile/). Middleman
extensions (like [middleman-blog](/basics/blogging/)) can contribute their own
templates as well. Alternative templates can be accessed using the `-T` or
`--template` command-line flags. For example, to start a new project based on
HTML5 Boilerplate, run this command:

``` bash
$ middleman init my_new_boilerplate_project --template=html5
```

Finally, you can create your own custom project skeletons by creating folders
in the `~/.middleman/` folder. For example, I can create a folder at
`~/.middleman/mobile/` and fill it with files I intend to use on mobile
projects.

If you run middleman init with the help flag, you will see a list of all the
possible templates it has detected:

``` bash
$ middleman init --help
```

This will list my custom mobile framework and I can create new projects based
on it as before:

``` bash
$ middleman init my_new_mobile_project --template=mobile
```

### Included Project Templates

Middleman ships with a number of basic project templates, including:

**[HTML5 Boilerplate]**

``` bash
$ middleman init my_new_html5_project --template=html5
```

**[SMACSS]**

``` bash
$ middleman init my_new_smacss_project --template=smacss
```

**[Mobile Boilerplate](http://html5boilerplate.com/mobile/)**

``` bash
$ middleman init my_new_mobile_project --template=mobile
```

### Community Project Templates

There are also a number of [community-developed project templates](http://directory.middlemanapp.com/#/templates/all).

[HTML5 Boilerplate]: http://html5boilerplate.com/
[SMACSS]: http://smacss.com/
