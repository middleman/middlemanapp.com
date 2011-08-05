---
title: Individual Page Configuration (YAML Frontmatter)
---

# Individual Page Configuration (YAML Frontmatter)

YAML Frontmatter is similar to the [Local YAML Data] feature, except it works on a single template file and can also send configuration options directly to Middleman.

Let's take a simple ERb template, adding some YAML variables and change the layout for this specific page.

    :::erb
    ---
    layout: "custom"
    my_list:
      - one
      - two
      - three
    ---
    
    <h1>List</h1>
    <ol>
      <% data.page.my_list.each do |f| %>
      <li><%= f %></li>
      <% end %>
    </ol>

YAML Frontmatter must come at the very top of the template and be separated from the rest of the current by a leading and trailing triple hyphen (`---`). Inside this block, you can create new variables which will be available in the template using the `data.page` object. The `layout` variable will pass directly to Middleman and change which layout is being used for rendering.