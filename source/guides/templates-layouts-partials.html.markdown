---
title: Templates, Layouts &amp; Partials
---

# Templates, Layouts &amp; Partials

Middleman provides many templating languages to simplify your HTML development. The languages range from simply allow you to use Ruby variables and loops in your pages, to providing a completely different format to write your pages in which compiles to HTML.

## Templates

The default templating language is ERb. ERb looks exactly like HTML, except it allows you to add variables, call methods and use loops and if statements. The following sections of this guide will use ERb in their examples. 

All template files in Middleman include the extension of that templating language in their file name. A simple index page written in ERb would be named `index.html.erb` which includes the full filename, `index.html`, and the ERb extension.

To begin, this file would just contain normal HTML:

    :::erb
    <h1>Welcome</h1>

If we wanted to get fancy, we could add a loop:

    :::erb
    <h1>Welcome</h1>
    <ul>
      <% 5.times do |num| %>
        <li>Count <%= num %>
      <% end %>
    </ul>

### Other Templating Languages

Middleman comes with quite a few other templating languages.

#### Haml

Ruby developers may be familiar with [Haml] which is a white-space aware language that compiles to HTML. The same index page from above would be called `index.html.haml` and look like:

    %h1 Welcome
    %ul
      - 5.times do |num|
        %li= "Count #{num}"

#### Slim

[Slim] is very similar to Haml, but it is even simpler and has more frequent updates and improvements. The same index page from above would be called `index.html.slim` and look like:

    h1 Welcome
    ul
      - 5.times do |num|
        li Count #{num}

#### Markdown

[Markdown] is a simple language that resembles text email and is optimized for writing text content and articles. In fact, [these guides are written in Markdown]. An example article in Markdown would be called `article.html.markdown` and look like:

    # Header
    
    A paragraph content
    
    ## Sub-header
    
    * List item 1
    * List item 2

## Layouts

Layouts allow the common HTML surrounding individual pages to be shared across all your templates. Developers coming from PHP will be used to the concept of "header" and "footer" includes which they reference at the top and bottom of every page. The Ruby world, and Middleman, take an inverse approach. The "layout" includes both the header and footer and then wraps the individual page content.

The most basic layout has some shared content and a `yield` call where templates will place their contents. 

Here is an example layout using ERb:

    :::erb
    <html>
    <head>
      <title>My Site</title>
    </head>
    <body>
      <%= yield %>
    </body>
    </html> 

Given a page template in ERb:

    :::erb
    <h1>Hello World</h1>

The combined final output in HTML will be:

    :::erb
    <html>
    <head>
      <title>My Site</title>
    </head>
    <body>
      <h1>Hello World</h1>
    </body>
    </html> 

### Custom Layouts

By default, Middleman will use the same layout file for every page in your site. However, you may want to use multiple layouts and specify which pages use these other layouts. For example, you may have a "public" site and an "admin" site which would each have their own layout files.

The default layout file lives in the `source` folder and is called "layout" and has the extension of the templating language you are using. The default is `layout.erb`.

To create a new layout for admin, add another file to your `source` folder called "admin.erb". Let's assume the contents are:

    :::erb
    <html>
    <head>
      <title>Admin Area</title>
    </head>
    <body>
      <%= yield %>
    </body>
    </html>

Now, you need to specify which pages use this alternative layout. You can do this in two ways. If you want to apply this layout to a large group of pages, you can use the "page" command in your `config.rb`. Let's assume you have a folder called "admin" in your `source` folder and all the templates in admin should use the admin layout. The `config.rb` would look like:

    :::ruby
    page "/admin/*", :layout => "admin"

This uses a wildcard in the page path to specify that any page under the admin folder should use the admin layout. 

You can also reference pages directly. For example, let's say we have a `login.html.erb` template which lives in the source folder, but should also have the admin layout. Let's use this example page template:

    :::erb
    <h1>Login</h1>
    <form>
      <input type="text" placeholder="Email">
      <input type="password">
      <input type="submit">
    </form>

Now you can specify that this specific page has a custom template like this:

    :::ruby
    page "/login.html", :layout => "admin"

Which would make the login page use the admin layout. As an alternative to specifying everything in the `config.rb`, you can set the layout on individual pages in their template file using [Individual Page Configuration]. Here is an example `login.html.erb` page which specifies its own layout.

    :::erb
    ---
    layout: admin
    ---
    
    <h1>Login</h1>
    <form>
      <input type="text" placeholder="Email">
      <input type="password">
      <input type="submit">
    </form>

### Using a Different Templating Language

By default, Middleman expects the templating language of the page and the layout to be the same. In the above example, we've used ERb for both. However, you may want to write your pages in Markdown or another simplified text language. Middleman will expect a layout.markdown file to exist, but Markdown doesn't generate structural HTML like body tags. 

In your `config.rb` file, you can tell Middleman to use a specific templating language for the layout that is different than the page templating language. For example:

    :::ruby
    set :markdown, :layout_engine => :erb

### Disabling Layouts Entirely

In some cases, you may not want to use a layout at all. This can be accomplished by setting the default layout to false in your `config.rb`:

    :::ruby
    disable :layout

## Partials

Partials are a way of sharing content across pages to avoid duplication. Partials can be used in page templates and layouts. Let's continue our above example of having two layouts: one for normal pages and one for admin pages. These two layouts could have duplicate content, such as a footer. We will create a footer partial and use it in both layouts.

Partial files are prefixed with an underscore and include the templating language extension you are using. Here is an example footer partial named `_footer.erb` that lives in the `source` folder:

    :::erb
    <footer>
      Copyright 2011
    </footer>

Now, we can include this partial in the default layout using the "partial" method:

    :::erb
    <html>
    <head>
      <title>My Site</title>
    </head>
    <body>
      <%= yield %>
      <%= partial "footer" %>
    </body>
    </html>
    
And in the admin layout:

    :::erb
    <html>
    <head>
      <title>Admin Area</title>
    </head>
    <body>
      <%= yield %>
      <%= partial "footer" %>
    </body>
    </html>

Now, any changes to `_footer.erb` will appear at the bottom of both layouts and any pages which use those layouts.

If you find yourself copying and pasting content into multiple pages or layouts, it's probably a good idea to extract that content into a partial.

[Haml]: http://haml-lang.com/
[Slim]: http://slim-lang.com/
[Markdown]: http://daringfireball.net/projects/markdown/
[these guides are written in Markdown]: https://raw.github.com/tdreyno/middleman-guides/master/source/guides/basics-of-templates.html.markdown
[Individual Page Configuration]: /guides/per-template-config.html