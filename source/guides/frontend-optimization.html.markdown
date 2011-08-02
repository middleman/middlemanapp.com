---
title: Frontend Optimization
---

# Frontend Optimization

Middleman handles CSS minification and Javascript compression so you don't have to worry about it. Most libraries ship minified and compressed versions of their files for users to deploy, but these files are unreadable or editable. Middleman allows you to keep the original, commented files in our project so you can easily read them and edit them if needed. Then, when you build the project, Middleman will handle all the optimization for you.

In your config.rb, activate the "minify_css" and "minify_javacript" features during the build of your site.

    configure :build do
      activate :minify_css
      activate :minify_javascript
    end

## Compressing Images

If you also want to compress images on build, you can use the [Middleman Smusher extension] to dramatically shrink images using [Yahoo's Smush.it tool].

To install:

    gem install middleman-smusher

Then activate in your config.rb:

    configure :build do
      activate :smusher
    end

[Middleman Smusher extension]: https://github.com/tdreyno/middleman-smusher
[Yahoo's Smush.it tool]: http://www.smushit.com/ysmush.it/