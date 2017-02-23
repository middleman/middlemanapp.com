set :layout, :article

activate :i18n
activate :directory_indexes
activate :autoprefixer
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

proxy "_redirects", "netlify-redirects", ignore: true

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

helpers do
  def active_link_to(caption, url, options = {})
    if current_page.url == "#{url}/"
      options[:class] = "doc-item-active"
    end

    link_to(caption, url, options)
  end
end

page "/localizable/community/built_using_middleman", layout: :example
