---
title: Layouts
---

# Layouts

Layouts allow the common HTML surrounding individual pages to be shared across
all your templates. Developers coming from PHP will be used to the concept of
"header" and "footer" includes which they reference at the top and bottom of
every page. The Ruby world, and Middleman, take an inverse approach. The
"layout" includes both the header and footer and then wraps the individual page
content.

The most basic layout has some shared content and a `yield` call where
templates will place their contents.

Here is an example layout using ERB:

```erb
<html>
  <head>
    <title>My Site</title>
  </head>
  <body>
    <%= yield %>
  </body>
</html>
```

Given a page template in ERB:

```erb
<h1>Hello World</h1>
```

The combined final output in HTML will be:

```html
<html>
  <head>
    <title>My Site</title>
  </head>
  <body>
    <h1>Hello World</h1>
  </body>
</html>
```

Regarding file extensions and parsers, layouts have a different function from
templates in the building process, so care should be taken in giving them the
right extension. Here is why:

As you might have gathered from the section on templates, file extensions are
significant. For example, naming a layout file `layout.html.erb` would tell the
language parser that it should take this file, which is erb and turn it into
html.

In a sense, reading the extensions from right to left, will tell you the
parsings that the file will undergo, ending up as a file in the format of the
leftmost extension. In the case of the example, converting an erb file to an
html file when serving, and when building the file.

Unlike templates, layouts should not be rendered to html. Giving a layout file
the leftmost extension `.html` will cause an error when building. Therefore,
you should stick to the template language extension only, i.e.: `layout.erb`.

## Custom Layouts

By default, Middleman will use the same layout file for every page in your
site. However, you may want to use multiple layouts and specify which pages use
these other layouts. For example, you may have a "public" site and an "admin"
site which would each have their own layout files.

The default layout file lives in the `source` folder and is called "layout" and
has the extension of the templating language you are using. The default is
`layout.erb`. Any alternate layouts you create should live in `source/layouts`

To create a new layout for admin, add another file to your `source/layouts`
folder called "admin.erb". Let's assume the contents are:

```erb
<html>
  <head>
    <title>Admin Area</title>
  </head>
  <body>
    <%= yield %>
  </body>
</html>
```

Now, you need to specify which pages use this alternative layout. You can do
this in two ways. If you want to apply this layout to a large group of pages,
you can use the "page" command in your `config.rb`. Let's assume you have a
folder called "admin" in your `source` folder and all the templates in admin
should use the admin layout. The `config.rb` would look like:

```ruby
page "/admin/*", :layout => "admin"
```

This uses a wildcard in the page path to specify that any page under the admin
folder should use the admin layout.

You can also reference pages directly. For example, let's say we have a
`login.html.erb` template which lives in the source folder, but should also
have the admin layout. Let's use this example page template:

```html
<h1>Login</h1>
<form>
  <input type="email">
  <input type="password">
  <input type="submit">
</form>
```

Now you can specify that this specific page has a custom layout like this:

```ruby
page "/login.html", :layout => "admin"
```

Which would make the login page use the admin layout. As an alternative to
specifying everything in the `config.rb`, you can set the layout on individual
pages in their template file using [Frontmatter]. Here is an example
`login.html.erb` page which specifies its own layout.

```html
---
layout: admin
---

<h1>Login</h1>
<form>
  <input type="email">
  <input type="password">
  <input type="submit">
</form>
```

## Nested Layouts

Nested layouts allow you to create a stack of layouts. The easiest to
understand use-case is the `middleman-blog` extension. Blog Articles are a
subset of the entire site's content. They should contain additional content and
structure, but should still end up wrapped by the site-wide structure (header,
footer, etc).

Here's what a simple default layout might look like:

```erb
<html>
  <body>
    <header>Header</header>
    <%= yield %>
    <footer>Footer</footer>
  </body>
</html>
```

Let's say we have a blog article `blog/my-article.html.markdown`. I could then
tell all the blog articles to use a `article_layout` layout instead of the
default `layout`. In `config.rb`:

```ruby
activate :blog do |blog|
  blog.layout = "article_layout"
end

# or:

page "blog/*", :layout => :article_layout
```

That `layouts/article_layout.erb` layout would look like this

```erb
<% wrap_layout :layout do %>
  <article>
    <%= yield %>
  </article>
<% end %>
```

Like a normal layout, `yield` is where the resulting template content is
placed. In this example, you've end up with the following output:

```html
<html>
  <body>
    <header>Header</header>
    <article>
      <!-- Contents of my template/blog article -->
    </article>
    <footer>Footer</footer>
  </body>
</html>
```

## Disabling Layouts Entirely

In some cases, you may not want to use a layout at all. This can be
accomplished by setting the default layout to false in your `config.rb`:

```ruby
set :layout, false

# or for an individual file:
page '/foo.html', :layout => false
```

  [Frontmatter]: /basics/frontmatter/
