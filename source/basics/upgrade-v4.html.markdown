---
title: Upgrading to v4
---

# Upgrading to v4

With version 4, we've removed a lot of lesser used features in the core and replaced them with better-supported approaches which already existed or moving that functionality into an extension.

Here's the list of API changes:

* Removed `partials_dir` config option. Please reference all partials from the `source/` directory. `partial 'partials/my-partial'` would map to `source/partials/_my-partial.erb`.
* Removed the `proxy` and `ignore` options for the `page` command in `config.rb`. Use the `proxy` and `ignore` commands instead of passing these options to `page`.
* Removed `with_layout` in config. Use loops of `page` instead.
* Removed Queryable Sitemap API
* Removed `css_compressor` setting, use `activate :minify_css, :compressor =>` instead.
* Removed `js_compressor` setting, use `activate :minify_javascript, :compressor =>` instead.
* Removed ability to serve folders of content statically (non-Middleman projects).
* Removed "Implied Extension feature", all templates must include a full file name plus the list of desired templating extensions.
* Remove `upgrade` and `install` CLI commands.
* Remove `page` template local. Use `current_resource` instead.
* Dropped support for providing a block to `page` & `proxy`.
* Dropped support for instance variables inside templates.
* Remove deprecated `request` instance
* Remove old module-style extension support
* Moved Compass into an extension, still bundled by default.
* The `after_build` block now returns a `Middleman::Builder` instance which is completely abstracted away from the CLI and Thor. If you need a copy of Thor to run addition also tasks or do a simple `create_file`, it is available as `.thor`. For example: `after_build { |builder| builder.thor.create_file(...) }`
* Removed sprockets, add `gem "middleman-sprockets", "~> 4.0.0.rc"` to `Gemfile`

Lots of code was touched during the v4 refactor. If you were relying on internal methods which were not mentioned above or described on this documentation site, there is a possibility things have changed. Please reach out if you have questions.

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

This adaptation will probably affect the largest number of Middleman users.

Like Rails, we support automatically loading environment specific config from a predefined path. If you have a lot of production config, create a file at `environments/production.rb` and its contents will automatically be evaluated when in production.

It is also possible to change the environment regardless of the output mode. So now, you can preview the production output in the dev server: `middleman server -e production`

The `-e` environment flag is also used for custom environments. Say you want to push some code to staging, you could use: `middleman build -e staging` and the `environments/staging.rb` could have staging-specific deploy scripts.

## File Updates in Rack servers

This refactor allows Middleman to run as a Rack server and still update on file changes normally. This makes mounting inside Rails or using the Pow server much nicer.

## Installing Project Templates from Git

[Documentation](https://middlemanapp.com/advanced/project_templates/)

During `middleman init`, custom project templates from `~/.middleman` or gems are no longer supported. Project templates must be git repositories.

Project template repository on GitHub:

```bash
middleman init MY_PROJECT_FOLDER -T username/repo-name
```

Local project template repository:

```bash
middleman init MY_PROJECT_FOLDER -T file:///path/to/local/repo/
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

This feature is hosted on top of a lower-level feature which allows multiple directories to be overlaid to create the combined sitemap for Middleman. This is great for keeping things like `bower_components` separate from your source directory, but still available to Middleman:

```
import_path File.expand_path('bower_components', app.root)
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

## Extension API Improvements

### Context Methods

In v4, the Application, Template Context and Config Context are all separated to avoid polluting a single shared instance with different concerns. In the past, it was possible for templates to add instance variables to the App, which resulted in some nasty naming collisions.

Now, each context has it's own sandbox. Extensions may want to add methods to these scopes:

* `expose_to_application :external_name => :internal_name` will create an `app.external_name` method which maps to the extension's public `internal_name` method. This should probably never be used outside of Middleman core (`app.data` primarily), but it's here if you need it.

* `expose_to_config :external_name => :internal_name` will create a `external_name` method which maps to the extension's public `internal_name` method. This method will be available inside `config.rb`.

* `expose_to_template :external_name => :internal_name` will create a `external_name` method which maps to the extension's public `internal_name` method. This method will be available inside the templating engines. This is very similar to the `helpers` method (which still exists), but this version will auto-bind the method into your extension context.

### Simple Resource Creation

`manipulate_resource_list` is great, but often more complex than most extensions need. Now, we have a way to simply create a resource with string contents.

* `resources :more_pages` will call the `more_pages` method inside your extension. That method is expected to return a Hash where the keys are the output URLs and the values are either a String of a Symbol representing another internal method.

	```
	resources :more_pages

	def more_pages
		{
			"/page1.html" => :page1,
			"/page2.html" => "Hello"
		}
	end

	def page1
		"Page 1"
	end
	```

* `resources "/page1.html" => "greetings"` is a shorthand form of the above. The method takes a Hash of paths to symbols or strings.
