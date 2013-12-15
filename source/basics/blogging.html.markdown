---
title: Blogging
---

# Blogging

Middleman has an official extension to support blogging, articles and tagging. `middleman-blog` ships as an extension and must be installed to use. Simply specify the gem in your `Gemfile`:

``` ruby
gem "middleman-blog"
```

Then activate the extension in your `config.rb`:

``` ruby
activate :blog do |blog|
  # set options on blog
end
```

Alternatively, once you've installed `middleman-blog` you can generate a fresh project already setup for blogging:

``` bash
middleman init MY_BLOG_PROJECT --template=blog
```

If you already have a Middleman project, you can re-run `middleman init` with the blog template option to generate the sample [`index.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/index.html.erb), [`tag.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/tag.html.erb), [`calendar.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/calendar.html.erb), and [`feed.xml`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/feed.xml.builder), or you can write those yourself. You can see [what gets generated](https://github.com/middleman/middleman-blog/tree/master/lib/middleman-blog/template/source) on GitHub.

The blog extension has many configuration options - you can see what they all are and what they do by going to `http://localhost:4567/__middleman/config/` in your preview server.

**Note:** If you are using the [`directory_indexes`](/basics/pretty-urls/) extension, you'll want to make sure that you activate it *after* you activate the blog extension.

## Articles

Like Middleman itself, the blog extension is focused on individual files. Each article is its own file, using any template language you like. The default filename structure for articles is  `{year}-{month}-{day}-{title}.html`. When you want to create a new article, place it in the correct path and include some basic [frontmatter](/basics/frontmatter/) to get going. You can set the `blog.sources` option while activating `:blog` in your `config.rb` to change where and in what format Middleman should look for articles.

Let's say I want to create a new post about Middleman. I would create a file at `source/2011-10-18-middleman.html.markdown`. The minimum contents of this file are a `title` entry in the frontmatter:

``` html
---
title: My Middleman Blog Post
---

Hello World
```

If you want, you can specify a full date and time as a `date` entry in the front matter, to help with ordering multiple posts from the same day. You can also include a list of `tags` in the front matter to generate tag pages.

## Generating Articles

As a shortcut, you can run `middleman article TITLE` and Middleman will create a new article for you in the right place with the right filename. You can pass `--date`, `--lang`, and `--blog` options to this command.

If you want to use your own template, you can set `blog.new_article_template` to the path (relative to the project root) of an ERb template that will be used by `middleman article`. This template can use the instance variables `@title`, `@slug`, `@date`, and `@lang` to generate your new article.

## Customizing Paths and URLs

The base path for your blog defaults to `/` (the root of your website) but can be overridden in `config.rb`:

``` ruby
activate :blog do |blog|
  blog.prefix = "blog"
end
```

All other link settings (`permalink`, `sources`, `taglink`, `year_link`, `month_link`, `day_link`) are added on to `prefix`, so you don't need to repeat it in every setting. Note that template locations (`calendar_template`, `year_template`, `month_template`, `day_template`, and `tag_template`) do *not* get `prefix` added to them, so if you want them to be in the same place as your articles you should add the same prefix to those settings.

The permalink for viewing your posts can changed on its own as well:

``` ruby
activate :blog do |blog|
  blog.permalink = "blog/{year}/{title}.html"
end
```

Now, your articles will show up at: `blog/2011/blog.html`. Your permalink can be totally independent from the format your posts are stored at. By default, the permalink path is `{year}/{month}/{day}/{title}.html`. Permalinks can be made up of any components of the article date (`{year}`, `{month}`, `{day}`), the title of the article (as `{title}`, transformed to be URL-appropriate), and `{lang}`

You can also use any other frontmatter data that is used throughout your articles. For example, if you have a `category` frontmatter key in your articles and wanted to include that in your permalinks:

```html
---
title: My Middleman Blog Post
date: 2013/10/13
category: HTML5
---

Hello World
```

``` ruby
activate :blog do |blog|
  blog.permalink = "blog/{category}/{title}.html"
end
```

The article above would now be under: `blog/html5/my-middleman-blog-post.html`.

Information extracted from your `blog.sources` URL template can also be used in `blog.permalinks`. So for example if you had a config like this:

``` ruby
activate :blog do |blog|
  blog.sources = "{category}/{year}-{month}-{day}-{title}.html"
  blog.permalink = "{category}/{year}/{month}/{day}/{title}.html"
end
```

You'd be able to put your article sources at `cats/2013-11-12-best-cats.html` and they'd get written out to `cats/2013/11/12/best-cats.html` without you having to specify a `category` in frontmatter. You can also access the category extracted from the source path via `current_article.metadata[:page]['category']`.

You might also consider enabling the [pretty urls](/basics/pretty-urls/) feature if you want your blog posts to appear as directories instead of HTML files.


## Layouts

You can set a specific [layout](/templates/#toc_3) to be used for all articles in your `config.rb`:

``` ruby
activate :blog do |blog|
  blog.layout = "blog_layout"
end
```

If you want to wrap each article in a bit of structure before inserting it into a layout, you can use Middleman's [nested layouts](/templates/#toc_4) feature to create an article layout that is then wrapped with your main layout.

## Listing Articles

The list of articles in your blog is accessible from templates as [`blog.articles`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogData#articles-instance_method), which returns a list of [`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle)s.

Each [`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle) has some informative methods on it, and it is also a [`Resource`](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Resource) from the [sitemap](/advanced/sitemap) which has even more information (such as the [`data`](http://rubydoc.info/gems/middleman-core/Middleman/CoreExtensions/FrontMatter/ResourceInstanceMethods#data-instance_method) from your [frontmatter](/basics/frontmatter/)). Within layouts and even your articles themselves you can get the current article via `current_article`,

For example, the following shows the 5 most-recent articles and their summary:

``` html
<% blog.articles[0...5].each do |article| %>
  <article>
    <h1>
      <a href="<%= article.url %>"><%= article.title %></a>
      <time><%= article.date.strftime('%b %e %Y') %></time>
    </h1>

    <%= article.summary %>

    <a href="<%= article.url %>">Read more</a>
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

Or if you added a `public` flag to your front matter:

``` html
<h1>Public Articles</h1>
<% blog.articles.select {|a| a.data[:public] }.each do |article| %>
  ...
<% end %>
```

## Helpers

There are [several helpers](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers) to use in your templates to make things simpler. They allow you to do things like get the current article, see if the current page is a blog article, or build paths for tag and calendar pages.

## Tags

What would blogging be without organizing articles around tags? Simply add a `tag` entry to your articles' [frontmatter](/basics/frontmatter/). Then, you can access the tags for a [`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle) using the [`tag`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle#tags-instance_method) method, and you can get a list of all tags with their associated article from [`blog.tags`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogData#tags-instance_method). If you set the `blog.tag_template` setting in `config.rb` to a template (see [the default config.rb](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/config.tt)) you can render a page for each tag. The tag template has the local variable `tagname` set to the current tag and `articles` set to a list of articles with that tag, and you can use the [`tag_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#tag_path-instance_method) helper to generate links to a particular tag page.

The default template produces a [`tag.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/tag.html.erb) template for you that produces a page for each tag at `tags/{tag}.html`. Adding a couple tags to the above example would look like this:

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
  blog.taglink = "categories/{tag}.html"
end
```

Now you can find this article listed on `categories/blogging.html`.

## Calendar Pages

Many blogging engines produce pages that list out all articles for a specific year, month, or day. Middleman does this using a [`calendar.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/calendar.html.erb) template and the `blog.calendar_template` setting. The default template generates [`calendar.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/calendar.html.erb) for you. This template gets `year`, `month`, and `day` variables set in it, as well as `articles` which is a list of articles for that day.

If you only want certain calendar pages (say, year but not day), or if you want different templates for each type of calendar page, you can set `blog.year_template`, `blog.month_template`, and `blog.day_template` individually. Setting `blog.calendar_template` is just a shortcut for setting them all to the same thing.

In templates, you can use the [`blog_year_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_year_path-instance_method), [`blog_month_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_month_path-instance_method), and [`blog_day_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_day_path-instance_method) helpers to generate links to your calendar pages. You can customize what those links look like with the `blog.year_link`, `blog.month_link`, and `blog.day_link` settings. By default, your calendar pages will look like `/2012.html`, `/2012/03.html`, and `/2012/03/15.html` for year, month, and day, respectively.


## Article Summaries

Middleman supports article truncation for cases when you'd like to show an article summary with a link to the article's permalink page, such as on the homepage. The blogging extension looks for the string `READMORE` in your article body and shows only the content before this text on the homepage. On the permalink page, this data is then stripped out.

You can configure the text that the blogging extension looks for to tell it to truncate in the `config.rb` file:

``` ruby
activate :blog do |blog|
  blog.summary_separator = /SPLIT_SUMMARY_BEFORE_THIS/
end
```

You can then show just the article summary, accompanied by a link to the full article, by adding the following lines on your homepage template (or wherever you'd like the summary to appear):

``` erb
<%= article.summary %>
<%= link_to 'Read more…', article %>
```

This will then link to the article, where `READMORE` (or the text you have configured the extension to match on) will be removed.

You can use the summary in templates from the [`summary`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle#summary-instance_method) attribute of a [`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle).

`summary` can also take an optional length to chop summaries down to, and a string to use when the text is truncated:

```erb
<%= article.summary(250, '>>') %>
```

This would produce a summary of no more than 250 characters, followed by ">>". The default summary length is 250 characters - if you wish to disable this entirely, set `blog.summary_length` to `nil`.

Note that, in order to provide HTML-aware summaries, you must add `gem 'nokogiri'` to your `Gemfile` in order to use summaries.

If you have your own method of generating summaries, you can set `blog.summary_generator` to a `Proc` that takes the rendered blog post, desired length, and ellipsis string and produces a summary.

## Pagination

Long lists of articles can be split across multiple pages. A template will be split into pages if it has

```html
---
pageable: true
---
```

in the frontmatter, and pagination is enabled for the site in `config.rb`:

```ruby
activate :blog do |blog|
  blog.paginate = true
end
```
By default the second and subsequent pages will have links that look like `page/{num}` (such as `2011/page/2.html`). This can be customized, along with the default number of articles per page, in `config.rb`. For example:

```ruby
activate :blog do |blog|
  blog.paginate = true
  blog.page_link = "p{num}"
  blog.per_page = 20
end
```

will result in up to 20 articles per page and links that look like `/2012/p2.html`. The `per_page` parameter can also be set for an individual template in the template's frontmatter.

Pageable templates can then use the following variables:

```ruby
paginate       # Set to true if pagination is enabled for this site.
per_page       # The number of articles per page.

page_articles  # The list of articles to display on this page.
articles       # The complete list of articles for the template,

page_number    # The number of this page.
num_pages      # The total number of pages. Use with page_number for
               # displaying "Page X of Y"

page_start     # The number of the first article on this page.
page_end       # The number of the last article on this page.
               # Use with articles.length to show "Articles X to Y of Z"

next_page      # The page resources for the next and previous pages
prev_page      # in the sequence, or nil if there is no adjacent page.
               # including this and all other pages.
```

If `paginate` is false and `per_page` is set in the template frontmatter, the `page_articles` variable will be set to the first `per_page` items in `articles`. This simplifies the creation of templates that can be used with and without pagination enabled.

## Draft Articles

Articles can be marked as draft in the frontmatter:

``` html
---
title: Work In Progress
published: false
---

Unfinished Draft
```

Draft articles will only appear in development mode.

An articles with a date that is in the future is also considered unpublished; if you use a `cron` job to regenerate your site on a regular basis, this can be used to automatically publish articles at a specified time.

## Time Zone

To get accurate publication times in your RSS feed, and for automatically publishing articles on a precise schedule, set your blog's timezone in `config.rb`:

``` ruby
Time.zone = "Tokyo"
```

## Custom Article Collections

Middleman-Blog also supports the ability to group articles by other [frontmatter](/basics/frontmatter/) data as well. A somewhat contrived example would be the ability to group artilces by a *category* attribute:

```html
---
title: My Middleman Blog Post
date: 2013/10/13
category: HTML5
---

Hello World
```

You can configure Middleman-blog to generate `categories/html5.html` to view all articles within the HTML5 category. See the example configuration below:

```ruby
activate :blog do |blog|
  blog.custom_collections = {
    category: {
      link: '/categories/{category}.html',
      template: '/category.html'
    }
  }
end
```

This will configure a collection based on the category attribute. You can specify the url structure for the custom pages and the template to use when building them. When building custom collections a new helper (in this example, `category_path`) will be generated. This will allow you to call `category_path('html5')` and generate the URL `categories/html5.html`.

## Article Subdirectory

A subdirectory named according to a blog article without the extensions can be filled with files that will be copied to the right place in the build output. For example, the following directory structure:

```
source/2011-10-18-middleman.html.markdown
source/2011-10-18-middleman/photo.jpg
source/2011-10-18-middleman/source_code.rb
```

might be output (if [`directory_indexes`](/basics/pretty-urls/) is turned on) as:

```
build/2011/10/18/middleman/index.html
build/2011/10/18/middleman/photo.jpg
build/2011/10/18/middleman/source_code.rb
```

This allows files (e.g. images) that belong to a single blog article to be kept with that article in the source and in the output. Depending on your blog structure, this may make it possible to use relative links in your article, although you need to be careful if your article content is used elsewhere in your site, e.g. calendar and tag pages.

Note that if you want to link to anything in your article subdirectory from your blog post, you should include the directory name:

```markdown
Wrong: [My Photo][photo.jpg]
Right: [My Photo][2011-10-18-middleman/photo.jpg]
```

If you don't do this, it may still work, but other Middleman features like `:asset_hash` won't work. See [this issue](https://github.com/middleman/middleman/issues/818) for more details.

## Locale-specific articles and localization

The blogging extension can be locale-aware. First, you'll want to activate the [`:i18n`](/advanced/localization/) extension before you activate your `:blog` extension. Then, your articles will be locale-aware. This means that you can use helpers like `t()` inside them.

Blog articles can have a specific language stated in their frontmatter, or via their path using the `{lang}` variable in `blog.sources`. For example, if `blog.sources` was set to `{lang}/{year}-{title}.html` and your article was at `source/de/2013-willkommen.html.markdown`, the language for that article would be `de`.  The `{lang}` path variable can be used in your `blog.permalink` path as well.

You can access the language of an article using `current_article.lang`, and you can get the list of locale-appropriate articles in a template using `blog.local_articles`.

When generating new articles, you can generate them in a specific locale using `middleman article --lang <locale> TITLE`.

## Multiple blogs in a single site

Middleman can host multiple blogs in a single site. To make more than one blog, simply activate the `:blog` extension multiple times:

```ruby
activate :blog do |blog|
  blog.name = "cats"
  blog.prefix = "cats"
end

activate :blog do |blog|
  blog.name = "dogs"
  blog.prefix = "dogs"
end
```

This will create two blogs, one at `/cats` and one at `/dogs`. Notice that we give the blogs names. This is for helpers like `blog()` - in order for them to know what blog we want, we can pass in the blog name to the `blog` helper like `blog('dogs')`. In many situations Middleman will automatically be able to find the right blog for helpers, but you may at times need to specify the blog name when using helpers like `blog`, `tag_path`, etc. You can also specify the blog name in a page's frontmatter (such as in your tag page template) which will tell all helpers which blog to use.

Other than that, your blogs act as normal, and can be configured totally independently.