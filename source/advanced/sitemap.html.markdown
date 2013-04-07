---
title: The Sitemap
---

# The Sitemap

Middleman includes a Sitemap, accessible from templates, that can give you information about all the pages and resources in your site and how they relate to each other. This can be used to create navigation, build search pages and sitemaps, etc.

## Accessing the Sitemap

The [sitemap](http://rubydoc.info/github/middleman/middleman/master/Middleman/Sitemap/Store) is a repository of every page in your site, including HTML, CSS, JavaScript, images - everything. It also includes any [dynamic pages] you've created using `:proxy`. Within templates, `sitemap` gets you the sitemap object. From there, you can look at every page via the [`resources`](http://rubydoc.info/github/middleman/middleman/master/Middleman/Sitemap/Store#resources-instance_method) method or grab individual resources via [`find_resource_by_path`](http://rubydoc.info/github/middleman/middleman/master/Middleman/Sitemap/Store#find_resource_by_path-instance_method). You can also always get the page object for the page you're currently in via `current_resource`. Once you've got the list of pages from the sitemap, you can filter on various properties using the individual page objects.

## Sitemap Pages

Each resource in the sitemap is a [Resource](http://rubydoc.info/github/middleman/middleman/master/Middleman/Sitemap/Resource) object. Pages can tell you all kinds of interesting things about themselves. You can access [frontmatter] data, file extension, source and output paths, a linkable url, its mime type, etc. Some of the properties of the Page are mostly useful for Middleman's rendering internals, but you could imagine filtering pages on file extension to find all `.html` files, for example.

Each page can also find other pages related to it in the site hierarchy. The `parent`, `siblings`, and `children` methods are particularly useful in building navigation menus and breadcrumbs.

## Using the Sitemap in config.rb

You can use the sitemap information to create new [dynamic pages] from `config.rb` (this is how the [blog extension](/blogging/) creates tag pages), but you need to be a little careful, because the sitemap isn't populated until *after* `config.rb` has already been run. To get around this, you need to register a callback for the application's `ready` event. As an example, let's say we've added a "category" element to the [frontmatter] of our pages, and we want to create category pages dynamically for each category. To do that, we'd add this to `config.rb`:

``` ruby
ready do
  sitemap.resources.group_by {|p| p.data["category"] }.each do |category, pages|
    proxy "/categories/#{category}.html", "category.html", 
      :locals => { :category => category, :pages => pages }
  end
end
```

Then I could make a `category.html.erb` that uses the `category` and `pages` variables to build a category listing for each category.

[dynamic pages]: /dynamic-pages/
[frontmatter]: /frontmatter/
