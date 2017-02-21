---
title: 外部パイプライン
---

# 外部パイプライン

私たちはフロントエンド言語とツールの爆発的増加のただ中にいます。開発が始まって以来, Middleman はフロントエンドの依存関係の管理とコンパイルに対するソリューションとして Rails のアセットパイプライン機能を備えていました。

ここ数年で, コミュニティは Rails から離れ NPM のタスクランナー (Gulp, Grunt) や依存管理 (Browserify, Webpack), 公式ツール (ember-cli, react-native) やトランスパイラ(ClojureScript, Elm) に焦点を合わせるようになりました。

Middleman はこれらすべてのソリューションや言語に対応することはできません。そこで私たちはこれらのツールが Middleman の中で動作できるようにすることにしました。この機能は `external_pipeline` (外部パイプライン) と呼ばれ, Middleman の複数のサブプロセスで動作します。一時フォルダにコンテンツを出力し Middleman のサイトマップに取り込むことで実現しています。

# Ember の例

簡単な Ember の例は次のようになります:

```ruby
activate :external_pipeline,
  name: :ember,
  command: "cd test-app/ && ember #{build? ? :build : :serve} --environment #{config[:environment]}",
  source: "test-app/dist",
  latency: 2
```

これは Middleman プロジェクトの中に Ember のフロントエンド用コードを含む `test-app` フォルダがある場合です。build モードでは, 静的ビルドするように Ember に命令しています。dev モードでは dev ビルドを取得できます。

Ember はコードを `test-app/dist` にコンパイルし, このコードは Middleman サイトマップに取り込まれます。

# Webpack の例

```ruby
activate :external_pipeline,
  name: :webpack,
  command: build? ? './node_modules/webpack/bin/webpack.js --bail' : './node_modules/webpack/bin/webpack.js --watch -d',
  source: ".tmp/dist",
  latency: 1
```

# Broccoli の例

Broccoli は node エコシステムの強力なアセットパイプラインツールです。無数のプラグインを使うことで多くのプリプロセッサニーズに対応することができます: CSS (SCSS, compass), ミニファイ (uglifyJS 他), モジュールローダ, トランスパイル (babel 他) など用意されています。

Broccoli についてはこちらを参照してください: https://github.com/broccolijs/broccoli

*config.rb*

```ruby
activate :external_pipeline,
  :name => 'broccoli',
  :command => (build? ? 'broccoli build pipeline-build' : 'broccoli-timepiece pipeline-build'),
  :source => 'pipeline-build',
  :latency => 2
```


*Brocfile.js*

Brocfile 例です (babel, SCSS や同様のプラグインで拡張可能)。

```javascript
/* globals module,require,process */

var Funnel            = require('broccoli-funnel');
var mergeTrees        = require('broccoli-merge-trees');
var concatFiles       = require('broccoli-concat');
var uglifyJavaScript  = require('broccoli-uglify-js');

var env = process.env.BROCCOLI_ENV || 'production';

var SOURCE_DIR = 'assets';
var OUTPUT_DIR = 'res';

var VENDOR_JS = [
  'jquery-1.11.0.js',
];

var VENDOR_CSS = [
  'normalize-2.1.2.css',
];


// JavaScript
var jsVendorTree = concatFiles(SOURCE_DIR + '/vendor/js', {
  outputFile: 'vendor.js',
  headerFiles: VENDOR_JS,
});

var jsOurTree = new Funnel(SOURCE_DIR + '/js');

// vendor merge vendor and our js
var jsTree = mergeTrees([jsVendorTree, jsOurTree]);

// concat vendor and our js
jsTree = concatFiles(jsTree, {
  outputFile: 'js/all.js',
  headerFiles: ['vendor.js'], // headerFiles are ordered
  inputFiles: ['**/*.js'], // inputFiles are un-ordered
  sourceMapConfig: { enabled: (env === 'development') },
});

if (env !== 'development') {
  jsTree = uglifyJavaScript(jsTree);
}


// CSS
var cssVendorTree = concatFiles(SOURCE_DIR + '/vendor/css', {
  outputFile: 'vendor.css',
  headerFiles: VENDOR_CSS,
});

var cssOurTree = new Funnel(SOURCE_DIR + '/css');

// merge vendor and our css
var cssTree = mergeTrees([cssVendorTree, cssOurTree]);

// concat vendor and our css
cssTree = concatFiles(cssTree, {
  outputFile: 'css/all.css',
  headerFiles: ['vendor.css'], // headerFiles are ordered
  inputFiles: ['**/*.css'], // inputFiles are un-ordered
  sourceMapConfig: { enabled: (env === 'development') },
});


// images
var imagesTree = new Funnel(SOURCE_DIR + '/images', {
  destDir: 'images',
});


// merge everything
var everythingTree = mergeTrees([jsTree, cssTree, imagesTree]);

var finalTree = new Funnel(everythingTree, {
  destDir: OUTPUT_DIR,
});

module.exports = finalTree;
```


*package.json*

```json
{
  "name": "assets",
  "version": "1.0.0",
  "description": "Website assets",
  "private": true,
  "engines": {
    "node": ">= 0.10.0"
  },
  "author": "Middleman Team",
  "devDependencies": {
    "babel-preset-es2015": "^6.18.0",
    "broccoli": "0.16.9",
    "broccoli-babel-transpiler": "5.5.0",
    "broccoli-concat": "^3.0.1",
    "broccoli-funnel": "1.0.3",
    "broccoli-merge-trees": "1.1.2",
    "broccoli-timepiece": "rwjblue/broccoli-timepiece",
    "broccoli-uglify-js": "0.2.0"
  }
}
```
