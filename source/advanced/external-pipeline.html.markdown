---
title: External Pipeline
---

# External Pipeline

We are in the middle of an explosion of front-end languages and tooling. Since the beginning, Middleman has defaulted to Rails' Asset Pipeline as our solution for frontend dependency management and compilation.

In the past few years, the community has moved away from Rails and is now focused on task runners (Gulp, Grunt), dependency management (Browserify, Webpack) via NPM, official tooling (ember-cli, react-native) and transpilers (ClojureScript, Elm).

Middleman cannot accommodate all these different solutions and languages, so we've decided to allow all of them to live inside Middleman. This feature is called `external_pipeline` and it allows Middleman to run multiple subprocesses which output content to temporary folders which are then merged into the Middleman sitemap.

# Ember Example

A simple Ember example looks like this:

```
activate :external_pipeline,
  name: :ember,
  command: "cd test-app/ && ember #{build? ? :build : :serve} --environment #{config[:environment]}",
  source: "test-app/dist",
  latency: 2
```

This assumes that you have a `test-app` folder in your Middleman project which contains the Ember frontend code. When in build mode, we tell Ember to do a static build. When in dev, we get a dev build.

Ember compiles this information to the `test-app/dist` folder, who's contents we merge into the Middleman sitemap.

# Webpack Example

```
activate :external_pipeline,
  name: :webpack,
  command: build? ? './node_modules/webpack/bin/webpack.js --bail' : './node_modules/webpack/bin/webpack.js --watch -d',
  source: ".tmp/dist",
  latency: 1
```