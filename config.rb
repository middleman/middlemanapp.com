require "builder"

set :layout, :article

activate :directory_indexes

set :markdown, :tables => true, :autolink => true, :gh_blockcode => true, :fenced_code_blocks => true, :with_toc_data => true
set :markdown_engine, :redcarpet

activate :relative_assets

# Live reload
activate :livereload

helpers do
  def gzip_css_on_build(key, media = "screen")
    o = stylesheet_link_tag(key, { :media => media})
    o.sub!(".css", ".css.gz") if build?
    o
  end

  def gzip_js_on_build(key)
    o = javascript_include_tag(key)
    o.sub!(".js", ".js.gz") if build?
    o
  end
end

activate :fjords do |config|
  config.username = Bundler.settings["fjords_username"]
  config.password = Bundler.settings["fjords_password"]
  config.domain = "middlemanapp.com"
end

# Build-specific configuration
configure :build do
  activate :gzip

  # For example, change the Compass output style for deployment
  activate :minify_css

  # Minify Javascript on build
  activate :minify_javascript
end
