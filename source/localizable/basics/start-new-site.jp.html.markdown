---
title: 新しいサイトの作成
---

# 新しいサイトの作成

開発を始めるに Middleman が動作するプロジェクトフォルダを作る必要があります。

<iframe width="560" height="315" src="https://www.youtube.com/embed/Mi86OOf_Dkg?rel=0" frameborder="0" allowfullscreen></iframe>

すでに存在するフォルダを使うか, `middleman init` コマンドで Middleman が
作成するフォルダを使うことができます。

```bash
$ middleman init
```

カレントディレクトリに Middleman のスケルトンプロジェクトを作ります。

```bash
$ middleman init my_new_project
```

`my_new_project` ディレクトリに Mmiddleman のスケルトンプロジェクトを作ります。

## スケルトン

新しいプロジェクトごとに基本的な Web 開発向けのスケルトンを作ります。
この一般的なフォルダ構成やファイルの自動生成は
どのプロジェクトでも利用できるものです。

真新しいプロジェクトは `source` フォルダと `config.rb` ファイルを含みます。
source フォルダは Web サイトを作る場所です。スケルトンプロジェクトは
JavaScript, CSS や画像のフォルダを含みますが, あなたの好みに合わせて
変更することができます。

`config.rb` には [Middleman の設定][settings] が含まれます。

### `Gemfile`

Middleman は gem 依存関係の管理に Bundler の `Gemfile` を使えるように
配慮してくれます。新しいプロジェクトを作ると, Middleman はあなたが使用する
Middleman のバージョンを指定した `Gemfile` を生成します。これにより Middleman を
特定のリリースシリーズに固定します (例えば 4.0.x シリーズ)。プロジェクトで
使用するプラグインや追加ライブラリはすべて `Gemfile` にリストアップされるべき
であり,起動時に Middleman はそれらのプラグインやライブラリを自動的に `require`
します。

### `config.ru`

config.ru ファイルは Rack 対応の Web サーバによってどのようにサイトが読み
込まれるか記述します。開発モードで Middleman サイトを Heroku のような Rack
対応ホストにホスティングしたい場合, 次の内容の `config.ru` ファイルを
プロジェクトルートに配置することで対応できます。

```ruby
require 'middleman/rack'
run Middleman.server
```

Middleman は *静的* サイト生成のために作られていることを忘れないでください。
この方法は一時的な利用方法に過ぎません。

  [settings]: /jp/advanced/configuration/
