---
title: Localization (i18n)
---

# Localization (i18n)

The `:i18n` extension provides an way to localize your site. Activate it in
your `config.rb`:

``` ruby
activate :i18n
```

By default this will search the `locales` folder in the root of your project
for YAML files representing each locale you want to support. The YAML file is a
set of keys and values for each string you need to localize in your site. The
keys, which is how you will refer to these strings in your templates, must be
the same in each locale, but the values will change. Here are two example YAML
files.

`locales/en.yml`:

``` yaml
---
en:
  hello: "Hello"
```

`locales/es.yml`:

``` yaml
---
es:
  hello: "Hola"
```

Localizable templates live in the `source/localizable` folder by default (see
below on how to change this option). Each template in that folder will have
access to the `I18n` helper. Using this helper, you can refer to keys from your
YAML files and inject the language-specific values into your template. Here's a
simple `source/localizable/hello_world.html.erb` template:

``` html
    <%= I18n.t(:hello) %> World
```

This would output two files:

* /hello_world.html with contents: "Hello World"
* /es/hello_world.html with contents: "Hola World"

You can use `t` as a shortcut for `I18n.t` in your templates:

``` html
    <%= t(:hello) %> World
```


## Localized Paths

Each individual language is accessible in its own namespaced path. By default,
the first language lives at the root of the site (see below to change this
option). The default path is to simply use the language name (the name of the
YAML file) in the path:

```
 /en/index.html
 /es/index.html
 /fr/index.html
```

You can change this with the `:path` option, but remember: the URL will always include the name of the YAML file:

``` ruby
activate :i18n, :path => "/langs/:locale/"
```

Now the paths would be:

```
/langs/en/index.html
/langs/es/index.html
/langs/fr/index.html
```

If you are unhappy using the YAML file names as part of your path, you can
remap them to different values.

``` ruby
activate :i18n, :path => "/langs/:locale/",
  :lang_map => { :en => :english, :es => :spanish, :fr => :french }
```

Now the paths would be:

```
/langs/english/index.html
/langs/spanish/index.html
/langs/french/index.html
```

### Localizing Paths

In some cases you may want to localize the name of the file in addition to its
contents. You can use the special `paths` key in your language YAML files to
rename URLs to be language-specific.

Let's say we have a file `source/localizable/hello.html.erb`. By default, this
will output as:

```
/hello.html
/es/hello.html
```

If we want to rename that file to `hola.html` for Spanish only, we can use the
`paths` key in `locales/es.yml`:

``` yaml
---
es:
  hello: "Hola"
  paths:
    hello: "hola"
```

Now, the files would be output as:

```
/hello.html
/es/hola.html
```

## Localizable Templates

By default, the contents of `source/localizable` will be built in multiple
languages while the rest of your templates will continue to work normally. The
name of this folder can be changed with the `:templates_dir` option:

``` ruby
# Look in `source/language_specific` instead
activate :i18n, :templates_dir => "language_specific"
```

## Manually specifying languages

If you'd prefer specify a list of supported languages rather than automatically
discovering files in `locales/`, you can use the `:langs` option:

``` ruby
activate :i18n, :langs => [:en] # Ignore all languages except :en
```

## Default (Root) Language

By default, the first language (either specified by `:langs` or discovered in
your `locales/` folder) will be the "default" language and will be mounted at
the root of your site. Given our two languages, files localized to `:en` will
be at the root:

* source/localizable/index.html.erb
  * build/index.html is English
  * build/es/index.html is Spanish

You can change the default or disable mounting a specific language at the root
entirely using the `:mount_at_root` option:

``` ruby
activate :i18n, :mount_at_root => :es # Mount spanish at root instead
# or
activate :i18n, :mount_at_root => false # All languages will be prefixed
```

## Localizing Entire Templates

It can be inefficient to put translations of large blocks of text into the
locale YAML files. To help with this, Middleman offers a way to localize entire
templates. For example, if you had `index.html`, you could create two
templates, `index.en.html.erb` and `index.es.html.erb`. When the site is built,
you'll get:

```
build/index.html is English
build/es/index.html is Spanish
```

To use this localization method, be sure to put your files inside the `localizable` folder.
