---
title: Templates
---

# Templates

Middleman provides access to many templating languages to simplify your HTML
development. The languages range from simply allowing you to use Ruby variables
and loops in your pages, to providing a completely different format to write
your pages in which compiles to HTML. Middleman ships with support for the ERB,
Haml, Sass, SCSS and CoffeeScript engines. Many more engines can be enabled by
including their Tilt-enabled gems ([see the list]).

## Template Basics

The default templating language is ERB. ERB looks exactly like HTML, except it
allows you to add variables, call methods and use loops and if statements. The
following sections of this guide will use ERB in their examples.

All template files in Middleman include the extension of that templating
language in their file name. A simple index page written in ERB would be named
`index.html.erb` which includes the full filename, `index.html`, and the ERB
extension.

To begin, this file would just contain normal HTML:

```html
<h1>Welcome</h1>
```

If we wanted to get fancy, we could add a loop:

```erb
<h1>Welcome</h1>
<ul>
  <% 5.times do |num| %>
    <li>Count <%= num %></li>
  <% end %>
</ul>
```

  [see the list]: /basics/template-engine-options/
