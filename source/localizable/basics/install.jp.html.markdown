---
title: インストール
---

# インストール

Middleman は RubyGems のパッケージマネージャを使って配布されます。つまり Middleman を使うには Ruby のランタイムと RubyGems の両方が必要だということです。

macOS には Ruby と RubyGems の両方がパッケージされていますが, Middleman の依存ライブラリの一部はインストール時にコンパイルする必要があります。macOS ではコンパイルに Xcode の Command Line Tools が必要です。Xcode がインストールされている場合 Terminal から実行してください:

```bash
$ xcode-select --install
```

Ruby と RubyGems をインストールしたら, 次のコマンドを実行してください:

```bash
$ gem install middleman
```

このコマンドは Middleman,  その依存ライブラリや Middleman を使うためのコマンドラインツールをインストールします。

このインストールプロセスはあなたの環境に新しいコマンドと 3 つの便利な機能を追加します:

```bash
$ middleman init
$ middleman server
$ middleman build
```

それぞれのコマンドの使用方法は次のセクションで説明します。[新しいサイトの作成](/jp/basics/start_new_site) へ。
