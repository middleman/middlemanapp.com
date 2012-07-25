---
title: Blogging Extension
---

# Blogging Extension

Middleman has an official extension to support blogging, articles and tagging. `middleman-blog` ships as an extension and must be installed to use. Simply specify the gem in your `Gemfile`:

``` ruby
gem "middleman-blog"
```

Or install it by hand if you're not using Bundler:

``` bash
gem install middleman-blog
```

Then activate the extension in your `config.rb`:

``` ruby
activate :blog do |blog|
  # set options on blog
end
```

Alternatively, you can generate a fresh project already setup for blogging:

``` bash
middleman init MY_BLOG_PROJECT --template=blog
```

If you already have a Middleman project, you can re-run `middleman init` with the blog template option to generate the sample [`index.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/index.html.erb), [`tag.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/tag.html.erb), [`calendar.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/calendar.html.erb), and [`feed.xml`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/feed.xml.builder), or you can write those yourself. You can see [what gets generated](https://github.com/middleman/middleman-blog/tree/master/lib/middleman-blog/template/source) on GitHub.

## Articles

Like Middleman itself, the blog extension is focused on individual files. Each article is its own file, using any template language you like. The default filename structure for articles is  `:year-:month-:day-:title.html`. When you want to create a new article, place it in the correct path and include the basic [YAML frontmatter](/metadata/yaml-frontmatter) to get going. You can set the `blog.sources` option while activating `:blog` in your `config.rb` to change where and in what format Middleman should look for articles.

Let's say I want to create a new post about Middleman. I would create a file at `source/2011-10-18-middleman.html.markdown`. The minimum contents of this file are a `title` entry in the frontmatter:

``` html
--- 
title: My Middleman Blog Post
---

Hello World
```

If you want, you can specify a full date and time as a `date` entry in the front matter, to help with ordering multiple posts from the same day. You can also include a list of `tags` in the front matter to generate tag pages.

As a shortcut, you can run `middleman article TITLE` and Middleman will create a new article for you in the right place with the right filename.

### Custom Paths

The base path for your blog defaults to `/` (the root of your website) but can be overridden in `config.rb`:

``` ruby
activate :blog do |blog|
  blog.prefix = "blog"
end
```

The permalink for viewing your posts can be easily changed as well:

``` ruby
activate :blog do |blog|
  blog.permalink = "blog/:year/:title.html"
end
```

Now, your articles will show up at: `blog/2011/blog.html`. Your permalink can be totally different from the format your posts are stored at. By default, the permalink path is `:year/:month/:day/:title.html`. You might also consider enabling the [pretty urls](/advanced/pretty-urls) feature if you want your blog posts to appear as directories instead of HTML files.

## Summary

By default, articles can be truncated when viewed outside their permalink page. The blogging extension looks for `READMORE` in your article text and only shows content before this text on the homepage, but strips this metadata on the permalink page.

This can be changed in `config.rb`:

``` ruby
activate :blog do |blog|
  blog.summary_separator = /SPLIT_SUMMARY_BEFORE_THIS/
end
```

You can use the summary in templates from the [`summary`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle#summary-instance_method) attribute of a [`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle).

## Tags

What would blogging be without organizing articles around tags? Simply add a `tag` entry to your articles' [frontmatter](/metadata/yaml-frontmatter). Then, you can access the tags for a [`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle) using the [`tag`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle#tags-instance_method) method, and you can get a list of all tags with their associated article from [`blog.tags`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogData#tags-instance_method). If you set the `blog.tag_template` setting in `config.rb` to a template (see [the default config.rb](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/config.tt)) you can render a page for each tag. The tag template has the local variable `@tag` set to the current tag and `@articles` set to a list of articles with that tag, and you can use the [`tag_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#tag_path-instance_method) helper to generate links to a particular tag page.

The default template produces a [`tag.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/tag.html.erb) template for you that produces a page for each tag at `tags/TAGNAME.html`. Adding a couple tags to the above example would look like this: 

``` html
--- 
title: My Middleman Blog Post
date: 2011/10/18
tags: blogging, middleman, hello, world
---

Hello World
```

Now you can find this article listed on `tags/blogging.html`.

This path can be changed in `config.rb`:

``` ruby
activate :blog do |blog|
  blog.taglink = "categories/:tag.html"
end
```

Now you can find this article listed on `categories/blogging.html`.

## Calendar Pages

Many blogging engines produce pages that list out all articles for a specific year, month, or day. Middleman does this using a [`calendar.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/calendar.html.erb) template and the `blog.calendar_template` setting. The default template generates [`calendar.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/calendar.html.erb) for you. This template gets `@year`, `@month`, and `@day` variables set in it, as well as `@articles` which is a list of articles for that day. 

If you only want certain calendar pages (say, year but not day), or if you want different templates for each type of calendar page, you can set `blog.year_template`, `blog.month_template`, and `blog.day_template` individually. Setting `blog.calendar_template` is just a shortcut for setting them all to the same thing. 

In templates, you can use the [`blog_year_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_year_path-instance_method), [`blog_month_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_month_path-instance_method), and [`blog_day_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_day_path-instance_method) helpers to generate links to your calendar pages. You can customize what those links look like with the `blog.year_link`, `blog.month_link`, and `blog.day_link` settings. By default, your calendar pages will look like `/2012.html`, `/2012/03.html`, and `/2012/03/15.html` for year, month, and day, respectively.

## Layouts

You can set a specific [layout](/templates/templates-layouts-partials) to be used for all articles in your `config.rb`:

``` ruby
activate :blog do |blog|
  blog.layout = "blog_layout"
end
```

If you want to wrap each article in a bit of structure before inserting it into a layout, you can use Middleman's [nested layouts](/templates/nested-layouts) feature to create an article layout that is then wrapped with your main layout.

## Article Data

The list of articles in your blog is accessible from templates as [`blog.articles`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogData#articles-instance_method), which returns a list of [`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle)s.

Each [`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle) has some informative methods on it, and can also produce the [`Page`](http://rubydoc.info/github/middleman/middleman/master/Middleman/Sitemap/Page) from the [sitemap](/advanced/sitemap) which has even more information (such as the [`data`](http://rubydoc.info/github/middleman/middleman/master/Middleman/Sitemap/Page#data-instance_method) from your [frontmatter](/metadata/yaml-frontmatter))

For example, the following shows the 5 most-recent articles and their summary:

``` html
<% blog.articles[0...5].each do |article| %>
  <article>
    <h1>
      <a href="<%= article.url %>"><%= article.title %></a>
      <time><%= article.date.strftime('%b %e %Y') %></time>
    </h1>

    <%= article.summary %>

    <a href="<%= article.url %>">Read more</a></div>
  </article>
<% end %>
```

You can also get access to the tag data for a tag archive:

``` html
<ul>
  <% blog.tags.each do |tag, articles| %>
    <li>
      <h5><%= tag %></h5>
      <ul>
        <% articles.each do |article| %>
          <li><a href="<%= article.url %>"><%= article.title %></a></li>
        <% end %>
      </ul>
  <% end %>
</ul>
```

Or similarly for a calendar list:

``` html
<ul>
  <% blog.articles.group_by {|a| a.date.year }.each do |year, articles| %>
    <li>
      <h5><%= year %></h5>
      <ul>
        <% articles.each do |article| %>
          <li><a href="<%= article.url %>"><%= article.title %></a></li>
        <% end %>
      </ul>
    </li>
  <% end %>
</ul>
```

Or if you added a `published` flag to your front matter:

``` html
<h1>Published Articles</h1>
<% blog.articles.select {|a| a.page.data[:published] }.each do |article| %>
  ...
<% end %>
```

## Helpers

There are [several helpers](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers) to use in your templates to make things simpler. They allow you to do things like get the current article, see if the current page is a blog article, or build paths for tag and calendar pages.
