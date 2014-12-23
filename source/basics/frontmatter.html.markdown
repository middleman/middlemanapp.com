---
title: Frontmatter
---

# Frontmatter

Frontmatter allows page-specific variables to be included at the top of a
template using the YAML or JSON format.

## YAML Frontmatter

Let's take a simple ERb template, adding some frontmatter variables to change
the layout for this specific page.

``` html
---
layout: "custom"
title: "My Title"
my_list:
  - one
  - two
  - three
---

<h1>List</h1>
<ol>
  <% current_page.data.my_list.each do |f| %>
  <li><%= f %></li>
  <% end %>
</ol>
```

Frontmatter must come at the very top of the template and be separated from the rest of the content by a leading and trailing triple hyphen `---`. Inside this block, you can create new data which will be available in the template using the `current_page.data` hash, e.g. `title: "My Title"` becomes `current_page.data.title`. The `layout` setting will pass directly to Middleman and change which layout is being used for rendering. You can also set `ignore`, `directory_index`, and some other page properties in this way.

## JSON Frontmatter

You can also use JSON for your frontmatter. It's delimited by `;;;` and looks
like this:

``` json
;;;
"layout": "custom",
"my_list": [
  "one",
  "two",
  "three"
]
;;;
```

After that, it can be used exactly the same as YAML frontmatter in your page.
