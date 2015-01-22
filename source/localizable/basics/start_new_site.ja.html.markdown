---
title: 新しいサイトの作成
---

# 新しいサイトの作成

開発を始めるに Middleman が動作するプロジェクトフォルダを作る必要があります。
すでに存在するフォルダを使うか, `middleman init` コマンドで Middleman が作成する
フォルダを使うことができます。

コマンドで新しいサイト用のフォルダを指定することで, Middleman はそのフォルダの中に
プロジェクトのスケルトンを作ります (場合によってはフォルダ自体も)。

``` bash
$ middleman init my_new_project
```

### スケルトン

新しいプロジェクトごとに基本的な Web 開発向けのスケルトンを作ります。
この一般的なフォルダ構成やファイルの自動生成は
どのプロジェクトでも利用できるものです。

真新しいプロジェクトは `source` フォルダと `config.rb` ファイルを含みます。
source フォルダは Web サイトを作る場所です。スケルトンプロジェクトは
javascript, CSS や画像のフォルダを含みますが, あなたの好みに合わせて
変更することができます。

`config.rb` ファイルには Middleman の設定や
コンパイル時の圧縮や "ブログモード" などの複雑な機能を有効化する方法が
コメントアウトされたドキュメントとして含まれます。

#### Gemfile

Middleman は gem の依存関係の管理に Bundler の Gemfile を使えるように配慮してくれます。
新しいプロジェクトを作ると, Middleman はあなたが使用している
Middleman のバージョンを指定した Gemfile を生成します。
これにより Middleman を特定のリリースシリーズに固定します (例えば 3.0.x シリーズ)。
もちろん `Gemfile` の `:git` オプションを使用して Github から最新版の
Middleman を使用することもできます。プロジェクトで使用するプラグインや
追加ライブラリはすべて Gemfile にリストアップされるべきであり,
起動時に Middleman はそれらのプラグインやライブラリを自動的に `require` します。

#### config.ru

config.ru ファイルは Rack 対応の Web サーバによってどのようにサイトが読み込まれるか
記述します。このファイルは Middleman で作るサイトの開発時に Heroku のような Rack ベースのサーバにホスティングしたい
ユーザの便宜のために提供されています。
しかし Middleman は *静的* サイト生成のために作られていることを忘れないでください。

プロジェクト内に `config.ru` ファイルの雛形を含めるには, init コマンドに `--rack`
フラグを追加してください:

``` bash
$ middleman init my_new_project --rack
```

すでにプロジェクトの初期化が完了しいて, 後から pow やその他開発サーバと連携させるために
config.ru が欲しい場合は, 次の内容を記述してください:

```
require 'rubygems'
require 'middleman/rack'
run Middleman.server
```
