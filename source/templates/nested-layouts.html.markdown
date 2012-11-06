---
title: Nested Layouts
---

# Nested Layouts

Nested layouts allow you to create a stack of layouts. The easiest to understand use-case is the `middleman-blog` extension. Blog Articles are a subset of the entire site's content. They should contain additional content and structure, but should still end up wrapped by the site-wide structure (header, footer, etc).

Here's what a simple default layout might look like:

``` html
<html>
  <body>
    <header>Header</header>
    <%= yield %>
    <footer>Footer</footer>
  </body>
</html>
```

Let's say we have a blog article `blog/my-article.html.markdown`. I could then tell all the blog articles to use a `article_layout` layout instead of the default `layout`. In `config.rb`:

``` ruby
page "blog/*", :layout => :article_layout
```

That `layouts/article_layout.erb` layout would look like this

``` html
<% wrap_layout :layout do %>
  <article>
    <%= yield %>
  </article>
<% end %>
```

Like a normal layout, `yield` is where the resulting template content is placed. In this example, you've end up with the following output:

``` html
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