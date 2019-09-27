activate :autoprefixer
activate :directory_indexes
activate :i18n
activate :syntax do |syntax|
  syntax.css_class = "syntax-highlight"
end

set :markdown, tables: true, autolink: true, gh_blockcode: true, fenced_code_blocks: true, with_toc_data: false
set :markdown_engine, :redcarpet

# Load remote data source
require 'httparty'
data.callbacks :gem_info, -> { ::HTTParty.get('https://rubygems.org/api/v1/gems/middleman.json').parsed_response }

page "/", layout: "home"
page "/advanced/*", layout: "documentation"
page "/basics/*", layout: "documentation"
page "/jp/", layout: "home"
page "/jp/advanced/*", layout: "documentation"
page "/jp/basics/*", layout: "documentation"

configure :build do
  # "Ignore" JS so webpack has full control.
  ignore { |path| path =~ /\/(.*)\.js$/ && $1 != 'site' }

  activate :minify_css
  activate :minify_javascript
end
