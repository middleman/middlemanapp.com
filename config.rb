activate :aria_current
activate :autoprefixer
activate :directory_indexes
activate :i18n
activate :syntax do |syntax|
  syntax.css_class = "syntax-highlight"
end

set :markdown, tables: true, autolink: true, gh_blockcode: true, fenced_code_blocks: true, with_toc_data: false
set :markdown_engine, :redcarpet

activate :external_pipeline,
  name: :webpack,
  command: build? ? './node_modules/webpack/bin/webpack.js --bail' : './node_modules/webpack/bin/webpack.js --watch -d',
  source: ".tmp/dist",
  latency: 1

activate :data_source do |d|
  d.sources = [
    {
      alias: "gem_info",
      path: "https://rubygems.org/api/v1/gems/middleman.json"
    }
  ]
end

["extensions", "services", "templates"].each do |resource_type|
  proxy(
    "/resources/#{resource_type}/index.html",
    "/resources/index.html",
    locals: { resource_type: resource_type },
    ignore: true,
  )
end

proxy "_redirects", "netlify-redirects", ignore: true

page "/", layout: "home"
page "/advanced/*", layout: "documentation"
page "/basics/*", layout: "documentation"
page "/jp/", layout: "home"
page "/jp/advanced/*", layout: "documentation"
page "/jp/basics/*", layout: "documentation"

configure :development do
  activate :livereload do |reload|
    reload.no_swf = true
  end
end

configure :build do
  # "Ignore" JS so webpack has full control.
  ignore { |path| path =~ /\/(.*)\.js$/ && $1 != 'site' }

  activate :minify_css
  activate :minify_javascript
end
