---
title: The Sitemap
---

# The Sitemap

Middleman includes a Sitemap, accessible from templates, that can give you
information about all the pages and resources in your site and how they relate
to each other. This can be used to create navigation, build search pages and
feeds, etc.

The [sitemap](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap) is a
repository of every page in your site, including HTML, CSS, JavaScript, images
- everything. It also includes any [dynamic pages] you've created using
`:proxy`. 

## Seeing the Sitemap

To understand exactly how Middleman sees your site, start the preview server
and load up http://localhost:4567/__middleman/sitemap/. You'll be able to
browse the whole sitemap and see the source path, destination (build) path,
URL, and more for each resource in the sitemap. Pay special attention to the
"path": you'll use that path to refer to files from `page`, `ignore` and
`proxy` in `config.rb`, and from `link_to` and `url_for` in your templates.

## Accessing the Sitemap from Code

Within templates `sitemap` gets you the sitemap object. From there, you can
look at every page via the
[`resources`](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#resources-instance_method)
method or grab individual resources via
[`find_resource_by_path`](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#find_resource_by_path-instance_method).
You can also always get the page object for the page you're currently in via
`current_resource`. Once you've got the list of pages from the sitemap, you can
filter on various properties using the individual page objects.

## Sitemap Resources

Each resource in the sitemap is a
[Resource](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Resource)
object. Resources can tell you all kinds of interesting things about
themselves. You can access [frontmatter] data, file extension, source and
output paths, a linkable url, etc. Some of the properties of the Resource are
mostly useful for Middleman's rendering internals, but you could imagine
filtering pages on file extension to find all `.html` files, for example.

Each page can also find other pages related to it in the site hierarchy. The
`parent`, `siblings`, and `children` methods are particularly useful in
building navigation menus and breadcrumbs.

The sitemap can also be queried via an ActiveRecord-like syntax:

```ruby
sitemap.where(:tags.include => "homepage").order_by(:priority).limit(10)
```

See [Middleman::Sitemap::Queryable](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Queryable) for more on the query interface.

## Using the Sitemap in config.rb

You can use the sitemap information to create new [dynamic pages] from
`config.rb`, but you need to be a little careful, because the sitemap isn't
populated until *after* `config.rb` has already been run. To get around this,
you need to register a callback for the application's `ready` event. As an
example, let's say we've added a "category" element to the [frontmatter] of our
pages, and we want to create category pages dynamically for each category. To
do that, we'd add this to `config.rb`:

``` ruby
ready do
  sitemap.resources.group_by {|p| p.data["category"] }.each do |category, pages|
    proxy "/categories/#{category}.html", "category.html", 
      :locals => { :category => category, :pages => pages }
  end
end
```

Then I could make a `category.html.erb` that uses the `category` and `pages`
variables to build a category listing for each category.
