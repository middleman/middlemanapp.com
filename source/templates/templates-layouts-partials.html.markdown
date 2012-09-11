---
title: Templates, Layouts &amp; Partials
---

# Templates, Layouts &amp; Partials

Middleman provides many templating languages to simplify your HTML development. The languages range from simply allowing you to use Ruby variables and loops in your pages, to providing a completely different format to write your pages in which compiles to HTML.

## Templates

The default templating language is ERb. ERb looks exactly like HTML, except it allows you to add variables, call methods and use loops and if statements. The following sections of this guide will use ERb in their examples. 

All template files in Middleman include the extension of that templating language in their file name. A simple index page written in ERb would be named `index.html.erb` which includes the full filename, `index.html`, and the ERb extension.

To begin, this file would just contain normal HTML: 

``` html
<h1>Welcome</h1>
```

If we wanted to get fancy, we could add a loop:

``` html
<h1>Welcome</h1>
<ul>
  <% 5.times do |num| %>
    <li>Count <%= num %>
  <% end %>
</ul>
```

### Other Templating Languages

Middleman ships with support for the ERb, Haml, Sass, Scss and CoffeeScript engines. The Tilt library to enable support for many different templating engines. Here is the list of Tilt-enabled templating languages and the RubyGems which must be installed (and required in `config.rb`) for them to work:

ENGINE                  | FILE EXTENSIONS        | REQUIRED LIBRARIES
------------------------|------------------------|----------------------------
ERB                     | .erb, .rhtml           | included
Interpolated String     | .str                   | included
Haml                    | .haml                  | included
Sass                    | .sass                  | included
Scss                    | .scss                  | included
Slim                    | .slim                  | slim
Erubis                  | .erb, .rhtml, .erubis  | erubis
Less CSS                | .less                  | less
Builder                 | .builder               | builder
Liquid                  | .liquid                | liquid
RDiscount               | .markdown, .mkd, .md   | rdiscount
Redcarpet               | .markdown, .mkd, .md   | redcarpet
BlueCloth               | .markdown, .mkd, .md   | bluecloth
Kramdown                | .markdown, .mkd, .md   | kramdown
Maruku                  | .markdown, .mkd, .md   | maruku
RedCloth                | .textile               | redcloth
RDoc                    | .rdoc                  | rdoc
Radius                  | .radius                | radius
Markaby                 | .mab                   | markaby
Nokogiri                | .nokogiri              | nokogiri
CoffeeScript            | .coffee                | coffee-script
Creole (Wiki markup)    | .wiki, .creole         | creole
WikiCloth (Wiki markup) | .wiki, .mediawiki, .mw | wikicloth
Yajl                    | .yajl                  | yajl-ruby
Stylus                  | .styl                  | ruby-stylus

## Markdown

[Markdown](http://daringfireball.net/projects/markdown/) is a popular template language that is readable even as plain text. Middleman's default Markdown renderer is [Maruku](http://maruku.rubyforge.org/), though [RedCarpet is suggested](/advanced/speeding-up) for speed and extra features. You can customize you Markdown options in `config.rb`:

``` ruby
set :markdown_engine, :redcarpet
set :markdown, :fenced_code_blocks => true,
               :autolink => true, 
               :smartypants => true
```

See the [Maruku](http://maruku.rubyforge.org/) or [RedCarpet](https://github.com/tanoku/redcarpet) docs for more customization options. 

## Layouts

Layouts allow the common HTML surrounding individual pages to be shared across all your templates. Developers coming from PHP will be used to the concept of "header" and "footer" includes which they reference at the top and bottom of every page. The Ruby world, and Middleman, take an inverse approach. The "layout" includes both the header and footer and then wraps the individual page content.

The most basic layout has some shared content and a `yield` call where templates will place their contents. 

Here is an example layout using ERb:

``` html
<html>
<head>
  <title>My Site</title>
</head>
<body>
  <%= yield %>
</body>
</html> 
```

Given a page template in ERb:

``` html
<h1>Hello World</h1>
```

The combined final output in HTML will be:

``` html
<html>
<head>
  <title>My Site</title>
</head>
<body>
  <h1>Hello World</h1>
</body>
</html>
```

Regarding file extensions and parsers, layouts have a different function from templates in the building process, so care should be taken in giving them the right extension. Here is why:

As you might have gathered from the section on templates, file extensions are significant. For example, naming a layout file `layout.html.erb` would tell the language parser that it should take this file, which is erb and turn it into html.

In a sense, reading the extensions from right to left, will tell you the parsings that the file will undergo, ending up as a file in the format of the leftmost extension. In the case of the example, converting an erb file to an html file when serving, and when building the file. 

Unlike templates, layouts should not be rendered to html. Giving a layout file the leftmost extension `.html` will cause an error when building. Therefore, you should stick to the template language extension only, i.e.: `layout.erb`.

### Custom Layouts

By default, Middleman will use the same layout file for every page in your site. However, you may want to use multiple layouts and specify which pages use these other layouts. For example, you may have a "public" site and an "admin" site which would each have their own layout files.

The default layout file lives in the `source` folder and is called "layout" and has the extension of the templating language you are using. The default is `layout.erb`.

To create a new layout for admin, add another file to your `source` folder called "admin.erb". Let's assume the contents are:

``` html
    <html>
    <head>
      <title>Admin Area</title>
    </head>
    <body>
      <%= yield %>
    </body>
    </html>
```

Now, you need to specify which pages use this alternative layout. You can do this in two ways. If you want to apply this layout to a large group of pages, you can use the "page" command in your `config.rb`. Let's assume you have a folder called "admin" in your `source` folder and all the templates in admin should use the admin layout. The `config.rb` would look like:

``` ruby
page "/admin/*", :layout => "admin"
```

This uses a wildcard in the page path to specify that any page under the admin folder should use the admin layout. 

You can also reference pages directly. For example, let's say we have a `login.html.erb` template which lives in the source folder, but should also have the admin layout. Let's use this example page template:

``` html
<h1>Login</h1>
<form>
  <input type="text" placeholder="Email">
  <input type="password">
  <input type="submit">
</form>
```

Now you can specify that this specific page has a custom template like this:

``` ruby
page "/login.html", :layout => "admin"
```

Which would make the login page use the admin layout. As an alternative to specifying everything in the `config.rb`, you can set the layout on individual pages in their template file using [Individual Page Configuration]. Here is an example `login.html.erb` page which specifies its own layout.

``` html
---
layout: admin
---

<h1>Login</h1>
<form>
  <input type="text" placeholder="Email">
  <input type="password">
  <input type="submit">
</form>
```

### Disabling Layouts Entirely

In some cases, you may not want to use a layout at all. This can be accomplished by setting the default layout to false in your `config.rb`:

``` ruby
disable :layout
```

## Partials

Partials are a way of sharing content across pages to avoid duplication. Partials can be used in page templates and layouts. Let's continue our above example of having two layouts: one for normal pages and one for admin pages. These two layouts could have duplicate content, such as a footer. We will create a footer partial and use it in both layouts.

Partial files are prefixed with an underscore and include the templating language extension you are using. Here is an example footer partial named `_footer.erb` that lives in the `source` folder:

``` html
<footer>
  Copyright 2011
</footer>
```

Now, we can include this partial in the default layout using the "partial" method:

``` html
<html>
<head>
  <title>My Site</title>
</head>
<body>
  <%= yield %>
  <%= partial "footer" %>
</body>
</html>
```

And in the admin layout:

``` html
<html>
<head>
  <title>Admin Area</title>
</head>
<body>
  <%= yield %>
  <%= partial "footer" %>
</body>
</html>
```

Now, any changes to `_footer.erb` will appear at the bottom of both layouts and any pages which use those layouts.

If you find yourself copying and pasting content into multiple pages or layouts, it's probably a good idea to extract that content into a partial.

After you start using partials, you may find you want to call it in different ways by passing variables. You can do this by:

``` html
<%= partial(:paypal_donate_button, :locals => { :amount => 1, :amount_text => "Pay $1" }) %>
<%= partial(:paypal_donate_button, :locals => { :amount => 2, :amount_text => "Pay $2" }) %>
```

Then, within the partial, you can set the text appropriately as follows:

``` html
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <input name="amount" type="hidden" value="<%= "#{amount}.00" %>" >
  <input type="submit" value=<%= amount_text %> >
</form>
```

Read the [Padrino partial helper] documentation for more information.

[Haml]: http://haml-lang.com/
[Slim]: http://slim-lang.com/
[Markdown]: http://daringfireball.net/projects/markdown/
[these guides are written in Markdown]: https://raw.github.com/middleman/middleman-guides/master/source/guides/basics-of-templates.html.markdown
[Individual Page Configuration]: /guides/individual-page-configuration
[Padrino partial helper]: http://www.padrinorb.com/api/classes/Padrino/Helpers/RenderHelpers.html
