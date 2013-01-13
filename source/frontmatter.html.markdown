---
title: Frontmatter
---

# Frontmatter

Frontmatter allows page-specific variables to be included at the top of a template using the YAML format.

Let's take a simple ERb template, adding some frontmatter variables to change the layout for this specific page.

``` html
---
layout: "custom"
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

Frontmatter must come at the very top of the template and be separated from the rest of the current by a leading and trailing triple hyphen `---`. Inside this block, you can create new data which will be available in the template using the `current_page.data` hash. The `layout` setting will pass directly to Middleman and change which layout is being used for rendering. You can also set `ignore` and `directory_index` in this way.