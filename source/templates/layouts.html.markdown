---
title: Template Layouts
---


# Template Layouts

Layouts allow the common HTML surrounding individual pages to be shared across all your templates. The most basic layout has some shared content and a `yield` call where templates will place their contents. Furthermore, With `wrap_layout`, `content_for` and `partial` you can build a complex layout inheritance, reduce the duplication of you code in templates to a minimal amount.

Regarding file extensions and parsers, layouts have a different function from templates in the building process, so care should be taken in giving them the right extension. Unlike templates, layouts should not be rendered to html. Giving a layout file the leftmost extension `.html` will cause an error when building. Therefore, you should stick to the template language extension only, i.e.: `layout.erb`.

The default layout file is called `layout` and has the extension of the templating language you are using. The default is `layout.erb`. It can lives in the `source` folder or `source/layouts` folder. 

## Basic layout 

Here is an basic layout using ERb:

    <html>
    <head>
      <title>My Site</title>
    </head>
    <body>
      <%= yield %>
    </body>
    </html> 

Given a page template in ERb:

    <h1>Hello World</h1>

The combined final output in HTML will be:

    <html>
    <head>
      <title>My Site</title>
    </head>
    <body>
      <h1>Hello World</h1>
    </body>
    </html> 


## Content Block

The `content_for` functionality supports capturing content and then rendering this into a different block within a layout. One such example is including assets onto the layout from a template:

    <% content_for :assets do %>
      <%= stylesheet_link_tag 'index', 'custom' %>
    <% end %>
    <h1>Hello World</h1>
    
Added `yield_content: BLOCKNAME` to layout, it will capture the includes from the block and allow them to be yielded into the layout:

    <head>
      <title>Example</title>
      <%= stylesheet_link_tag 'style' %>
      <%= yield_content :assets %>
    </head>
    <body>
      <%= yield %>
    </body>
    
This will automatically insert the contents of the block (in this case a stylesheet include) into the location the content is yielded within the layout.

    <head>
      <title>Example</title>
      <%= stylesheet_link_tag 'style' %>
      <%= stylesheet_link_tag 'index', 'custom' %>
    </head>
    <body>
      <h1>Hello World</h1>
    </body>


You can also check if a `content_for` block exists for a given key using `content_for?`, usefull to provide a default content:

    <% if content_for?(:assets) %>
      <%= stylesheet_link_tag 'advance' %>
      <%= yield_content :assets %>
    <% else %>
      <%= stylesheet_link_tag 'base' %>
    <% end %>
  
Also supports arguments yielded to the content block

    yield_content :head, param1, param2
    content_for(:head) { |param1, param2| ...content... }


## Nested Layouts

Sometimes, one layer of layout is not enough. Normally, the contents of the layout will wrap the contents of the template. With `wrap_layout`, You can add the following to the layout and wrap the contents yet again:

    <% wrap_layout :admin do %>
      I am the Defaul Layout
      <%= yield %>
    <% end %>

Now, the final contents will be the template, wrapped in the default layout, wrapped in the admin layout. This can 
continue indefinitely.


## Partials

Partials are a way of sharing content across pages to avoid duplication. Partials can be used in page templates and layouts. Let's continue our above example of having two layouts: one for normal pages and one for admin pages. These two layouts could have duplicate content, such as a footer. We will create a footer partial and use it in both layouts.

Partial files are prefixed with an underscore and include the templating language extension you are using. Here is an example footer partial named `_footer.erb` that lives in the `source` folder:

    <footer>
      Copyright 2011
    </footer>

Now, we can include this partial in the default layout using the "partial" method:

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

After you start using partials, you may find you want to call it in different ways by passing variables. You can do this by:

    <%= partial(:paypal_donate_button, :locals => {:amount => 1, :amount_text => "Pay $1"}) %>
    <%= partial(:paypal_donate_button, :locals => {:amount => 2, :amount_text => "Pay $2}) %>

Then, within the partial, you can set the text appropriately as follows:

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <input name="amount" type="hidden" value="<%= "#{amount}.00" %>" >
      <input type="submit" value=<%= amount_text %> >
    </form>

Read the [Padrino partial helper] documentation for more information.

[Haml]: http://haml-lang.com/
[Slim]: http://slim-lang.com/
[Markdown]: http://daringfireball.net/projects/markdown/
[these guides are written in Markdown]: https://raw.github.com/middleman/middleman-guides/master/source/guides/basics-of-templates.html.markdown
[Individual Page Configuration]: /guides/individual-page-configuration
[Padrino partial helper]: http://www.padrinorb.com/api/classes/Padrino/Helpers/RenderHelpers.html




## Multiple Layouts

By default, Middleman will use the same default layout file `layout.erb` for every page in your site. However, you may want to use multiple layouts and specify which pages use these other layouts. For example, you may have a "public" site and an "admin" site which would each have their own layout files.

To create a new layout for admin, add another file to your `source` folder called "admin.erb". Let's assume the contents are:

    <html>
    <head>
      <title>Admin Area</title>
    </head>
    <body>
      <%= yield %>
    </body>
    </html>

Now, you need to specify which pages use this alternative layout. You can do this in two ways. If you want to apply this layout to a large group of pages, you can use the "page" command in your `config.rb`. Let's assume you have a folder called "admin" in your `source` folder and all the templates in admin should use the admin layout. The `config.rb` would look like:

    page "/admin/*", :layout => "admin"

This uses a wildcard in the page path to specify that any page under the admin folder should use the admin layout. 

You can also reference pages directly. For example, let's say we have a `login.html.erb` template which lives in the source folder, but should also have the admin layout. Let's use this example page template:

    <h1>Login</h1>
    <form>
      <input type="text" placeholder="Email">
      <input type="password">
      <input type="submit">
    </form>

Now you can specify that this specific page has a custom template like this:

    page "/login.html", :layout => "admin"

Which would make the login page use the admin layout. As an alternative to specifying everything in the `config.rb`, you can set the layout on individual pages in their template file using [Individual Page Configuration]. Here is an example `login.html.erb` page which specifies its own layout.

    ---
    layout: admin
    ---
    
    <h1>Login</h1>
    <form>
      <input type="text" placeholder="Email">
      <input type="password">
      <input type="submit">
    </form>


## Disabling Layouts Entirely

In some cases, you may not want to use a layout at all. This can be accomplished by setting the default layout to false in your `config.rb`:

    disable :layout
