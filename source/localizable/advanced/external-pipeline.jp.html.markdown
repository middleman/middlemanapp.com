---
title: 外部パイプライン
---

# 外部パイプライン

私たちはフロントエンド言語とツールの爆発的増加のただ中にいます。開発が始まって以来, Middleman はフロントエンドの依存関係の管理とコンパイルに対するソリューションとして Rails のアセットパイプライン機能を備えていました。

ここ数年で, コミュニティは Rails から離れ NPM のタスクランナー (Gulp, Grunt) や依存管理 (Browserify, Webpack), 公式ツール (ember-cli, react-native) やトランスパイラ(ClojureScript, Elm) に焦点を合わせるようになりました。

Middleman はこれらすべてのソリューションや言語に対応することはできません。そこで私たちはこれらのツールが Middleman の中で動作できるようにすることにしました。この機能は `external_pipeline` (外部パイプライン) と呼ばれ, Middleman の複数のサブプロセスで動作します。一時フォルダにコンテンツを出力し Middleman のサイトマップに取り込むことで実現しています。

# Ember の例

簡単な Ember の例は次のようになります:

```
activate :external_pipeline,
  name: :ember,
  command: "cd test-app/ && ember #{build? ? :build : :serve} --environment #{config[:environment]}",
  source: "test-app/dist",
  latency: 2
```

これは Middleman プロジェクトの中に Ember のフロントエンド用コードを含む `test-app` フォルダがある場合です。build モードでは, 静的ビルドするように Ember に命令しています。dev モードでは dev ビルドを取得できます。

Ember はコードを `test-app/dist` にコンパイルし, このコードは Middleman サイトマップに取り込まれます。

# Webpack の例

```
activate :external_pipeline,
  name: :webpack,
  command: build? ? './node_modules/webpack/bin/webpack.js --bail' : './node_modules/webpack/bin/webpack.js --watch -d',
  source: ".tmp/dist",
  latency: 1
```
