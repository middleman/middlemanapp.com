---
title: Upgrading to v4
---

# Upgrading to v4

With version 4, we're removing a lot of lesser used features in the core and replacing them with either better-supporting approaches which already existed or moving that functionality into an extension.

This upgrade documentation is a work in progress and will evolve along side the v4 betas. If the upgrade path for any of the removed features is confusing or not an option for your codebase, please let us know and we can take another look at either the docs or the decision to remove it.

Here's the list:

* Removed the `proxy` and `ignore` options for the `page` command in `config.rb`. Use the `proxy` and `ignore` commands instead of passing these options to `page`.
* Removed `with_layout` in config. Use loops of `page` instead.
* Removed Queryable Sitemap API
* Removed `css_compressor` setting, use `activate :minify_css, :compressor =>` instead.
* Removed `js_compressor` setting, use `activate :minify_javascript, :compressor =>` instead.
* Removed ability to server folders of content statically (non-Middleman projects).
* Removed "Implied Extension feature", all templates must include a full file name plus the list of desired templating extensions.
* Remove `upgrade` and `install` CLI commands.
* Remove `page` template local. Use `current_resource` instead.
* Dropped support for providing a block to `page` & `proxy`.
* Dropped support for instance variables inside templates.
* Remove deprecated `request` instance
* Remove old module-style extension support
* Moved Compass into an extension, still bundled by default.

Lots of code was touched during the v4 refactors. If you were relying on internal methods which were not mentioned above or described on this documentation site, there is a possibility things have changed. Please reach out if you have questions.

## Environments and Changes to `configure` blocks.

v4 adds the ability to differentiate between different target environments. In the past, we conflated the environment `development` and the output mode `build` as the two ways to target config changes.

What we are doing in v4 separates these. There are 2 default environments now, `development` and `production`, but you can easily add your own. There are 2 default output modes as well, `server` and `build`.

The `middleman server` command defaults to the mode of `server` and the environment of `development`.

The `middleman build` command defaults to the mode of `build` and the environment of `production`.

The `configure` command can target both environments and modes:

```
configure :server { #enable sprockets debugging }
configure :build { # run some post-build hooks }
configure :development { # enable some sass debug settings }
configure :production { activate :minify_html }
```

This adaptation will probably effect the largest number of Middleman users.

Like Rails, we support automatically loading environment specific config from a predefined path. If you have a lot of production config, create a file at `environments/production.rb` and its contents will automatically be evaluated when in production.

It is also possible to change the environment regardless of the output mode. So now, you can preview the production output in the dev server: `middleman server -e production`

The `-e` environment flag is also used for custom environments. Say you want to push some code to staging, you could use: `middleman build -e staging` and the `environments/staging.rb` could have staging-specific deploy scripts.

## File Updates in Rack servers

This refactor allows Middleman to run as a Rack server and still update on file changes normally. This makes mounting inside Rails or using the Pow server much nicer.

## Installing Templates from Git

We're removed the ability to create custom reusable templates from either `~/.middleman` or gems. Instead, `middleman init` now takes the `-T` flag which points to any Git repository. If the path does not contain a protocol `://`, then we will assume it is hosted on Github.

```
middleman init -T tdreyno/my-middleman-starter ~/Sites/new-site
```

This should make sharing templates much easier. We also allow the mapping of custom names to Git repositors for all projects submitted to our Directory site. For example:

```
middleman init -T amicus ~/Sites/new-site
```

## External Tools

We want to support as many possible tools as we can. Want to run Grunt? Maybe ClojureScript JVM in the background? How about browserify or ember-cli? That's what the goal of `external_pipeline` is. Here's an example of how Middleman v4 can control an external process, which outputs into an arbitrary directory and is then consumed by Middleman:

```
activate :external_pipeline,
  name: :ember,
  command: "cd test-app/ && ember #{build? ? :build : :serve} --environment #{config[:environment]}",
  source: "test-app/dist",
  latency: 2
```

This is pretty experimental and will need a lot of love before the final release. This feature is hosted on top of a lower-level feature which allows multiple directories to be overlaid to create the combined sitemap for Middleman. This is great for keeping things like `bower_components` separate from your source directory, but still available to Middleman:

```
files.watch :source, path: File.expand_path('bower_components', app.root)
```

## Collections

The final new feature is "Collections". Collections abstract some logic from Middleman Blog to allow you to define groups of files and paths, in pure Ruby, which can then be acted upon. This works around a common new user mistake where they assume `config.rb` is executed whenever anything changes, rather than once on startup. Collections give the illusion that anything you write in `config.rb` is always up to date.

Lets say you want to implement tagging:

```
def get_tags(resource)
  if resource.data.tags.is_a? String
    resource.data.tags.split(',').map(&:strip)
  else
    resource.data.tags
  end
end

def group_lookup(resource, sum)
  results = Array(get_tags(resource)).map(&:to_s).map(&:to_sym)

  results.each do |k|
    sum[k] ||= []
    sum[k] << resource
  end
end

tags = resources
  .select { |resource| resource.data.tags }
  .each_with_object({}, &method(:group_lookup))

collection :all_tags, tags
collection :first_tag, tags.keys.sort.first
```

This will give you an always up-to-date hash called `all_tags` and an always up-to-date array representing the current resources which have the first alphabetical tag. As you can see, all the code is normal Ruby, so you can write your implementation however you'd like. The only 2 constraints are that a collection must be made from a chained collection starting with `resources` and that the `collection` method must be called when you are done to pass the information into your templates.

```
<% collection(:tags).each do |k, items| %>
  Tag: <%= k %> (<%= items.length %>)
  <% items.each do |article| %>
    Article: <%= article.data.title %>
  <% end %>
<% end %>

First Tag: <%= collection(:first_tag) %>
```

Collections can also be used, directly in `config.rb` to keep dynamic pages up-to-date:

```
tags.each do |k, articles|
  proxy "/tags/#{k}.html", "/tags/list.html", locals: {
    articles: articles
  }
end
```

Again, Collections are very new and experimental. You can help influence the direction of the feature over the beta period.

