---
title: Dynamic Pages
---

# Dynamic Pages

## Defining proxies

Middleman has the ability to generate pages which do not have a one-to-one
relationship with their template files. What this means is that you can have a
single template which generates multiple files based on variables. To create a
proxy, you use the `proxy` method in your `config.rb`, and give the path you
want to create, and then the path to the template you want to use (without any
templating file extensions). Here's an example `config.rb` setup:

``` ruby
# Assumes the file source/about/template.html.erb exists
["tom", "dick", "harry"].each do |name|
  proxy "/about/#{name}.html", "/about/template.html", :locals => { :person_name => name }
end
```

When this project is built, four files will be output:

* `/about/tom.html` (with `person_name` equalling "tom" in the template)
* `/about/dick.html` (with `person_name` equalling "dick" in the template)
* `/about/harry.html` (with `person_name` equalling "harry" in the template)
* `/about/template.html` (with `person_name` being nil in the template)

In most cases, you will not want to generate the template itself without the
`person_name` variable, so you can tell Middleman to ignore it:

``` ruby
["tom", "dick", "harry"].each do |name|
  proxy "/about/#{name}.html", "/about/template.html", :locals => { :person_name => name }, :ignore => true
end
```

Now, only the `about/tom.html`, `about/dick.html` and `about/harry.html` files
will be output.

## Ignoring Files

It is also possible to ignore arbitrary paths when building a site using the
new `ignore` method in your `config.rb`:

``` ruby
ignore "/ignore-this-template.html"
```

You can give ignore exact source paths, filename globs, or regexes.
