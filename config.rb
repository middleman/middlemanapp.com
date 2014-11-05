require "builder"

set :layout, :article

activate :livereload

activate :i18n

activate :directory_indexes

activate :autoprefixer

set :markdown, :tables => true, :autolink => true, :gh_blockcode => true, :fenced_code_blocks => true, :with_toc_data => true
set :markdown_engine, :redcarpet

configure :development do
  activate :relative_assets
end

configure :build do
  activate :minify_css
  activate :minify_javascript
end
