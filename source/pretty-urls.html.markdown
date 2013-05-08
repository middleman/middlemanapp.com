---
title: Pretty URLs (Directory Indexes)
---

# Pretty URLs (Directory Indexes)

By default Middleman will output the files exactly as you have described them in your project. For example a `about-us.html.erb` file in the `source` folder will be output as `about-us.html` when you build the project. If you were to place this project in the root of a web server at `example.com`, then this page would be accessible at: `http://example.com/about-us.html`



Middleman provides the Directory Indexes extension to tell Middleman to create a folder for each `.html` file and place the built template file as the index of that folder. In your `config.rb`:

``` ruby
activate :directory_indexes
```

Now when the above project is built, the `about-us.html.erb` file will be output as `about-us/index.html`. When served by a web server that supports "index files" (like Apache, or Amazon S3), the page would be available at:

``` ruby
http://example.com/about-us
```

If you prefer a different file be output, you can use the `index_file` variable. For example, IIS uses default.html:

``` ruby
set :index_file, "default.html"
```

Or, you may want a PHP file:

``` ruby
set :index_file, "index.php"
```

## Opt-out

If there are pages which you don't want automatically renamed, you can opt-out:

``` ruby
page "/i-really-want-the-extension.html", :directory_index => false
```

`page` works with regexes or file globs if you want to turn off indexes for many files at once.

You can also add a `directory_index: false` key to your page's [Frontmatter](/frontmatter/) to disable directory indexes.

## Manual Indexes

If your template file is already named `index.html` it will pass through Middleman untouched. For example, `my-page/index.html.erb` will generate `my-page/index.html` as you would expect.