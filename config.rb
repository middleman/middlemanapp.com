require "builder"

set :layout, :article

activate :livereload
activate :i18n
activate :directory_indexes
activate :autoprefixer

set :markdown, :tables => true, :autolink => true, :gh_blockcode => true, :fenced_code_blocks => true, :with_toc_data => true
set :markdown_engine, :redcarpet

redirect "basics/pretty_urls.html", to: "advanced/pretty_urls.html"

configure :development do
  set :debug_assets, true
end

configure :build do
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
