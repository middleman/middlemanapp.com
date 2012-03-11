---
title: Blogging Extension
---

# Blogging Extension

Middleman 2.1 ships with an official extension to support blogging, articles and tagging. `middleman-blog` ships as an extension and must be installed to use. Simply install the gem:

    :::bash
    gem install middleman-blog

Or if you are using Bundler, you should specify it in your `Gemfile`:

    :::ruby
    gem "middleman-blog"

Then activate the extension in your `config.rb`:

    :::ruby
    activate :blog
    
Alternatively, you can generate a fresh project already setup for blogging:

    :::bash
    middleman init MY_BLOG_PROJECT --template=blog

## Articles

Like Middleman itself, the blog extension is focused on individual files. For now, blog articles are limited to the Markdown format. The default folder structure is `:year/:month/:day/:title.html`. When you want to create a new article, place it in the correct path and include the basic YAML frontmatter to get going.

Let's say I want to create a new post about Middleman. I would create a file at `source/2011/10/18/middleman.html.markdown`. The minimum contents of this file are `title` and `date` frontmatter:

    --- 
    title: My Middleman Blog Post
    date: 2011/10/18
    ---

    Hello World

### Custom Paths

The path for storing, and viewing, your posts can be easily changed in `config.rb`:

    :::ruby
    set :blog_permalink, "blog/:year/:title.html"

Now, I'd place the same file above at: `blog/2011/blog.html.markdown`.

## Summary

By default, articles can be truncated when viewed outside their permalink page. The blogging extension looks for `READMORE` in your article text and only shows content before this text on the homepage, but strips this metadata on the permalink page.

This can be changed in `config.rb`:

    :::ruby
    set :blog_summary_separator, /SPLIT_SUMMARY_BEFORE_THIS/

## Tags

What would blogging be without organizing articles around tags. Simply add tag frontmatter to your articles and they will be organized on a tag page at `tags/TAGNAME.html` by default. Adding a couple tags to the above example would look like this: 

    --- 
    title: My Middleman Blog Post
    date: 2011/10/18
    tags: blogging, middleman, hello, world
    ---

    Hello World

Now you can find this article listed on `tags/blogging.html`.

This path can be changed in `config.rb`:

    :::ruby
    set :blog_taglink, "categories/:tag.html"

Now you can find this article listed on `categories/blogging.html`.

## Templates

When creating a fresh blog project using `middleman init` as above, you'll have several template files generated for you. Just like a normal Middleman project, all templates are wrapped in the contents of a layout. By default this layout is the same as the rest of your project.

This can be changed in `config.rb`:
  
    :::ruby
    set :blog_layout, "blog_layout"

Unlike a normal Middleman project, the layout will not use the same templating engine as the content because Markdown doesn't make sense as a layout. The blog extension defaults to ERb, but this can be changed in `config.rb`:

    :::ruby
    set :blog_layout_engine, "haml"

In addition to the normal layout, blog permalink pages, the blog index and tag index are wrapped in additional templates for maximum customization. The permalink uses `_article_template.erb` and the index pages use `_index_template.erb`. These can both be changed in `config.rb`:

    :::ruby
    set :blog_index_template, "custom_index_template"
    set :blog_article_template, "custom_article_template"

## Template Data

The metadata extracted from your articles is available using the same `data` object as normal Middleman data. You can see how this is in a generic blog homepage. The following example shows the 5 most-recent articles and their summary:

    <% data.blog.articles[0...5].each do |article| %>
      <article>
        <h1>
          <a href="<%= article.url %>"><%= article.title %></a>
          <time><%= article.date.strftime('%b %e %Y') %></time>
        </h1>
    
        <%= article.summary %>
    
        <a href="<%= article.url %>">Read more</a></div>
      </article>
    <% end %>

You can also get access to the tag data for a tag archive:

    <ul>
      <% data.blog.tags.each do |slug, tag| %>
        <li>
          <h5><%= tag.title %></h5>
          <ul>
            <% tag.pages.each do |title, url| %>
              <li><a href="<%= url %>"><%= title %></a></li>
            <% end %>
          </ul>
      <% end %>
    </ul>

## Helpers

There are a bunch of helpers to use in your templates to make things simpler:

* `is_blog_article?`: Whether the current page is an article
* `current_article_date`
* `current_article_title`
* `current_article_metadata`
* `current_article_tags`
* `blog_tags`
* `current_tag_data`
* `current_tag_articles`
* `current_tag_title`

## Markdown Engines

The default Markdown engine is Maruku. This can be easily changed in `config.rb`:

    :::ruby
    require "redcarpet"
    set :markdown_engine, :redcarpet