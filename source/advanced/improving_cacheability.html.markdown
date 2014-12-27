---
title: Improving Cacheability
---

# Improving Cacheability

To make your website render as quickly as possible, you should serve any
assets, like JavaScript, CSS, or images, with proper headers that instruct web
browsers to [cache them for a very long
time](https://code.google.com/speed/page-speed/docs/caching.html). This means
that when users visit your site again (or even just go to another page in your
site) they don't have to re-download those assets. However, setting a
far-future `Expires` or `Cache-Control` header can cause problems when you
change your assets but users are still using their cached versions. Middleman
has two approaches to solve this problem for you.

## Uniquely-named assets

The most effective technique for preventing users from using outdated files is
to change the asset's filename every time you change one of your assets. Since
that would be a pain to do by hand, Middleman comes with an `:asset_hash`
extension that does it for you. First, activate the extension in your
`config.rb`:

``` ruby
activate :asset_hash
```

Now, refer to your assets as normal, with their original filename. You can use
helpers like `image_tag` as well. However, when your site is built, each asset
will be produced with a bit of extra text at the end of the filename that is
tied to the content of the file, and all of your other files (HTML, CSS,
JavaScript, etc) will be changed to reference that unique-ified filename
instead of the original one. Now you can serve your assets with a "never
expire" policy, but be sure that when you change them, they'll show up as a
different filename.

However, because this extension works by rewriting your files to reference the
renamed assets, it's possible the extension might mess up and miss a reference,
or do something you don't want to your code. In that case, you might have to
fall back to the older cache buster method.

If you want to exclude any files from being renamed, pass the `:ignore` option
when activating `:asset_hash`, and give it one or more globs, regexes, or procs
that identify the files to ignore. Likewise, you can pass an `:exts` option to
change which file extensions are renamed.

## Configuring your server

Configuring your server to use far-future `Expires` and `Cache-Control` headers
is different depending on which server you use. See Google's [page speed
docs](https://code.google.com/speed/page-speed/docs/caching.html) for links on
how to configure your particular server, and run [Google Page
Speed](https://code.google.com/speed/page-speed/docs/extension.html) or
[YSlow](https://addons.mozilla.org/en-US/firefox/addon/yslow/) to check that
you've configured things correctly.
