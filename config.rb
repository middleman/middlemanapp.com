set :layout, :article

activate :livereload
activate :i18n
activate :directory_indexes
activate :autoprefixer

set :markdown, tables: true, autolink: true, gh_blockcode: true, fenced_code_blocks: true, with_toc_data: false
set :markdown_engine, :redcarpet

# Redirect from old paths
{
  "basics/dynamic-pages.html"            => "advanced/dynamic_pages.html",
  "basics/pretty-urls.html"              => "advanced/pretty_urls.html",
  "advanced/custom.html"                 => "advanced/custom_extensions.html",
  "basics/templates.html"                => "basics/templating_language.html",
  "basics/helpers.html"                  => "basics/helper_methods.html",
  "basics/getting-started.html"          => "basics/install.html",
  "basics/asset-pipeline.html"           => "advanced/asset_pipeline.html",
  "advanced/improving-cacheability.html" => "advanced/improving_cacheability.html",
  "advanced/local-data.html"             => "advanced/data_files.html",
  "advanced/rack-middleware.html"        => "advanced/rack_middleware.html",
  "advanced/file-size-optimization.html" => "advanced/file_size_optimization.html",
  "community/built-using-middleman.html" => "community/built_using_middleman.html"
}.each do |old_path, new_path|
  ["", "jp/"].each do |prefix|
    redirect "#{prefix}#{old_path}", to: "#{prefix}#{new_path}"
  end
end

activate :external_pipeline,
  name: :webpack,
  command: build? ? './node_modules/webpack/bin/webpack.js --bail' : './node_modules/webpack/bin/webpack.js --watch -d',
  source: ".tmp/dist",
  latency: 1

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
