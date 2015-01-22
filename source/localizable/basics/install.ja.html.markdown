---
title: インストール
---

# インストール

Middleman は RubyGems のパッケージマネージャを使って配布されます。つまり
Middleman を使うには Ruby のランタイムと RubyGems の両方が必要だと
いうことです。

Mac OS X には Ruby と RubyGems の両方がパッケージされていますが, Middleman の依存ライブラリの一部はインストール時に
コンパイルする必要があり OS X ではそのコンパイルに Xcode を必要とします。 Xcode は [Mac App 
Store](http://itunes.apple.com/us/app/xcode/id497799835?ls=1&mt=12) から
インストールできます。
もしくは無料の Apple Developer アカウントをお持ちであれば,
この [ダウンロードページ](https://developer.apple.com/downloads/index.action) から
Xcode 用のコマンドラインツールをインストールすることができます。

Ruby と RubyGems をインストールが完了したら, コマンドラインから次を
実行します:

```bash
$ gem install middleman
```

このコマンドは Middleman,  その依存ライブラリや Middleman を使うためのコマンドラインツールを
インストールします。

このインストールプロセスはあなたの環境に新しいコマンド 1 つと 3 つの
便利な機能を追加します:

```bash
$ middleman init
$ middleman server
$ middleman build
```

それぞれのコマンドの使用方法は次のセクションで説明します。[新しい
サイトの作成](/ja/basics/start_new_site) へ。
