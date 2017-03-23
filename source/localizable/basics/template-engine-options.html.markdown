---
title: Template Engine Options
---

# Template Engine Options

You can set options for the various template engines in your `config.rb`:

```ruby
set :haml, { :ugly => true, :format => :html5 }
```

## Markdown

You can choose your favorite Markdown library and set options for it in
`config.rb`:

```ruby
set :markdown_engine, :redcarpet
set :markdown, :fenced_code_blocks => true, :smartypants => true
```

When using RedCarpet, Middleman will handle links and image tags with its own
helpers, meaning things like `:relative_links` and `:asset_hash` will do what
you expect. However, the default Markdown engine is Kramdown because it's easier
to install.

## Other Templating Languages

Here is the list of Tilt-enabled templating languages and the RubyGems which
must be installed (and required in `config.rb`) for them to work (this list is
from [Tilt]):

ENGINE                  | FILE EXTENSIONS        | REQUIRED LIBRARIES
------------------------|------------------------|----------------------------
Slim                    | .slim                  | slim
Erubis                  | .erb, .rhtml, .erubis  | erubis
Less CSS                | .less                  | less
Builder                 | .builder               | builder
Liquid                  | .liquid                | liquid
RDiscount               | .markdown, .mkd, .md   | rdiscount
Redcarpet               | .markdown, .mkd, .md   | redcarpet
BlueCloth               | .markdown, .mkd, .md   | bluecloth
Kramdown                | .markdown, .mkd, .md   | kramdown
Maruku                  | .markdown, .mkd, .md   | maruku
RedCloth                | .textile               | redcloth
RDoc                    | .rdoc                  | rdoc
Radius                  | .radius                | radius
Markaby                 | .mab                   | markaby
Nokogiri                | .nokogiri              | nokogiri
CoffeeScript            | .coffee                | coffee-script
Creole (Wiki markup)    | .wiki, .creole         | creole
WikiCloth (Wiki markup) | .wiki, .mediawiki, .mw | wikicloth
Yajl                    | .yajl                  | yajl-ruby
Stylus                  | .styl                  | stylus

  [Tilt]: https://github.com/rtomayko/tilt/
