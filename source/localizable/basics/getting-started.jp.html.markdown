---
title: はじめに
---

# はじめに

Middleman はモダンな Web 開発環境のあらゆるショートカットやツールを採用した静的 Web サイトを作成するためのコマンドラインツールです。

Middleman はコマンドラインを熟知していることを前提としています。 Ruby と Web フレームワークの Sinatra がこのツールのベースになっています。この 2 つについて熟知していれば Middleman が存在しないかのように動作する理由を理解するには充分でしょう。

## インストール

Middleman は RubyGems パッケージマネージャを使って配布されます。これは Middleman を使うには Ruby のランタイムと RubyGems の両方が必要であることを意味します。

Mac OS X には Ruby と RubyGems の両方がパッケージされていますが, Middleman の依存ライブラリの一部はインストール時にコンパイルする必要があり OS X ではコンパイルのために Xcode を必要とします。 Xcode は [Mac App Store](http://itunes.apple.com/us/app/xcode/id497799835?ls=1&mt=12) からインストールできます。もしくは無料の Apple Developer アカウントを持っていれば, この [ダウンロードページ](https://developer.apple.com/downloads/index.action) から Xcode 用のコマンドラインツールをインストールすることができます。

Ruby と RubyGems をインストールしたら, コマンドラインから次を実行します:

``` bash
gem install middleman
```

このコマンドは Middleman,  その依存ライブラリや Middleman を使うためのコマンドラインツールをインストールします。

このインストールプロセスはあなたの環境に新しいコマンド 1 つと 3 つの便利な機能を追加します:

* middleman init
* middleman server
* middleman build

それぞれのコマンドの使用方法は続けて説明します。

## 新しいサイトの開発を始める: middleman init

開発を始めるに Middleman が動作するプロジェクトフォルダを作る必要があります。`middleman init` コマンドを使うことで, すでに存在するフォルダか Middleman が作成するフォルダを指定することができます。

コマンドで新しいサイト用のフォルダを指定することで, Middleman はそのフォルダの中にプロジェクトのスケルトンを作ります (場合によってはフォルダも)。

``` bash
middleman init my_new_project
```

### スケルトン

新しいプロジェクトごとに基本的な Web 開発向けのスケルトンを作ります。この一般的なフォルダ構成やファイルの自動生成はどのプロジェクトでも利用できるものです。

真新しいプロジェクトは `source` フォルダと `config.rb` ファイルを含みます。 source フォルダは Web サイトを作る場所です。スケルトンプロジェクトは javascript, CSS や画像のフォルダを含みますが, あなたの好みに合わせて変更することができます。

`config.rb` ファイルには Middleman の設定やコンパイル時の圧縮や "ブログモード" などの複雑な機能を有効化する方法がコメントアウトされたドキュメントとして含まれます。

#### Gemfile

Middleman は gem の依存関係の管理に Bundler の Gemfile を使えるように配慮します。新しいプロジェクトを作ると, Middleman はあなたが使用している Middleman のバージョンを指定した Gemfile を生成します。これにより Middleman を特定のリリースシリーズに固定します (例えば 3.x シリーズ)。もちろん `Gemfile` の `:git` オプションを使用して Github から最新版の Middleman を使用することもできます。プロジェクトで使用するプラグインや追加ライブラリはすべて Gemfile にリストアップされるべきであり, 起動時に Middleman はそれらのプラグインやライブラリを自動的に `require` します。 

#### config.ru

config.ru ファイルは Rack 対応の Web サーバによってどのようにサイトが読み込まれるか記述します。このファイルは Middleman で作るサイトの開発時に Heroku のような Rack ベースのサーバにホスティングしたいユーザの便宜のために提供されています。しかし Middleman は「静的」サイト生成のために作られていることを忘れないでください。

プロジェクト内に `config.ru` ファイルの雛形を含めるには, init コマンドに `--rack` フラグを追加してください:

``` bash
middleman init my_new_project --rack
```

すでにプロジェクトの初期化が完了しいて, 後から pow やその他開発サーバと連携させるために config.ru が欲しい場合は, 次の内容を記述してください:

```
require 'rubygems'
require 'middleman/rack'

run Middleman.server
```

### プロジェクトテンプレート

デフォルトの基本スケルトンに加え, Middleman は [HTML5 Boilerplate], [SMACSS], や [Mobile Boilerplate](http://html5boilerplate.com/mobile/) ベースのオプションテンプレートが付属します。Middleman 拡張 ([middleman-blog](/jp/basics/blogging/) のような) は同じように独自のテンプレートを使用することができます。テンプレート変更は `-T` や `--template` コマンドラインフラグを使用してアクセスできます。例えば, HTML5 Boilerplate ベースのプロジェクトを始める場合, 次のコマンドを使用します:

``` bash
middleman init my_new_boilerplate_project --template=html5
```

最後に, `~/.middleman/` フォルダの中に独自のカスタムテンプレートのスケルトンを入れたフォルダを作ることができます。例えば, 私は `~/.middleman/mobile` フォルダを作り, モバイルプロジェクトで利用するファイルをフォルダ用意することができます。

help フラグとともに middleman init コマンドを使用すると, 使用可能なテンプレートのリストが表示されます:

``` bash
middleman init --help
```

このコマンドは私の独自モバイルテンプレートを表示し, 私は以前と同じように新しいプロジェクトを作ることができます:

``` bash
middleman init my_new_html5_project --template=html5
```

### 用意されているプロジェクトテンプレート

Middleman は基本的なプロジェクト用のテンプレートをいくつか用意しています:

**[HTML5 Boilerplate]**

``` bash
middleman init my_new_mobile_project --template=html5
```

**[SMACSS]**

``` bash
middleman init my_new_smacss_project --template=smacss
```

**[Mobile Boilerplate](http://html5boilerplate.com/mobile/)**

``` bash
middleman init my_new_mobile_project --template=mobile
```

### コミュニティプロジェクトテンプレート

こちらにもいくつか [コミュニティで開発されたテンプレート](http://directory.middlemanapp.com/#/templates/all) があります。

## 開発サイクル (middleman server)

Middleman はスタート時点から開発コードとプロダクションコードを分離します。これにより開発中にプロダクションでは不要もしくは望ましくないツール群 ([Haml](http://haml-lang.com) や [Sass](http://sass-lang.com) などのような) を開発中に利用することができます。開発サイクルや静的サイトをこれらの環境に依存することができます。

Middleman を使う時間の大半は開発サイクルになります。

コマンドラインから, プロジェクトフォルダの中でプレビューサーバを起動してください:

``` bash
cd my_project
bundle exec middleman server
```

このコマンドはローカルの Web サーバを起動します: `http://localhost:4567/`

`source` フォルダにファイルを作成編集し, プレビュー Web サーバ上で反映された変更を確認することができます。

コマンドラインから `CTRL-C` を使ってプレビューサーバを停止できます。

### 飾りのない middleman コマンド

コマンド指定なしの `middleman` の使用はサーバの起動と同じです。

``` bash
bundle exec middleman
```

このコマンドは `middleman server` とまったく同じ動作をします。

## 静的サイトのエクスポート (middleman build)

最後に, 静的サイトのコードを出力する準備ができているか "ブログモード" で静的ブログをホストするような場合, サイトをビルドします。コマンドラインを使い, プロジェクトフォルダの中から `middleman build` を実行してください。

``` bash
cd my_project
bundle exec middleman build
```

このコマンドは `source` フォルダにあるファイル毎に静的ファイルを作ります。テンプレートファイルがコンパイルされ, その静的ファイルがコピーされ, 有効化されたビルド時の機能 (圧縮のような) が実行されます。 Middleman は自動的に前回のビルドに残っているが今回は生成されないファイルを削除します。

[HTML5 Boilerplate]: http://html5boilerplate.com/
[SMACSS]: http://smacss.com/
