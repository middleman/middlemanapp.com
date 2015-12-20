---
title: Redirects
---

# Redirects

Some times you need to change a file path, but maintain the old URLs for bookmarked users and search engines. For SEO reasons, it is recommended that you configure your webserver to send a "301 Redirect" header to point your visitors to the new location of the content.

However, in some cases, you will not have access to the server to add this feature. Middleman can generate HTML files which will redirect your visitors, but, these old paths are likely to still appear in search engines and will negatively impact your SEO.

To generate a redirect in Middleman:

``` ruby
redirect "/my/old/path.html", to: "/my/new/path.html"
```