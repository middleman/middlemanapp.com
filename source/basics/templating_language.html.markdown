---
title: Templates
---

# Templates

Middleman provides access to many templating languages to simplify your HTML development. The languages range from simply allowing you to use Ruby variables and loops in your pages, to providing a completely different format to write your pages in which compiles to HTML.  Middleman ships with support for the ERb, Haml, Sass, Scss and CoffeeScript engines. Many more engines can be enabled by including their Tilt-enabled gems. [See the list below](#other-templating-languages).

## Template Basics

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
    <li>Count <%= num %></li>
  <% end %>
</ul>
```

[Haml]: http://haml-lang.com/
[Slim]: http://slim-lang.com/
[Markdown]: http://daringfireball.net/projects/markdown/
[these guides are written in Markdown]: https://raw.github.com/middleman/middleman-guides/master/source/guides/basics-of-templates.html.markdown
[Frontmatter]: /basics/frontmatter/
[Padrino partial helper]: http://www.padrinorb.com/api/classes/Padrino/Helpers/RenderHelpers.html
