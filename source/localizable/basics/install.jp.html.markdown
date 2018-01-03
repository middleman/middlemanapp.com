---
title: インストール
---

# インストール

Middleman は RubyGems のパッケージマネージャを使って配布されます。
つまり Middleman を使うには Ruby のランタイムと RubyGems の両方が
必要だということです。

<iframe width="560" height="315" src="https://www.youtube.com/embed/nNc5Pm4IYeE?rel=0" frameborder="0" allowfullscreen></iframe>

<iframe width="560" height="315" src="https://www.youtube.com/embed/gayJFzi0rfg?rel=0" frameborder="0" allowfullscreen></iframe><br>

macOS には Ruby と RubyGems の両方がパッケージされていますが, Middleman の
依存ライブラリの一部はインストール時にコンパイルする必要があります。macOS では
コンパイルに Xcode の Command Line Tools が必要です。Xcode がインストール
されている場合 Terminal から実行してください:

```bash
$ xcode-select --install
```

Ruby と RubyGems をインストールしたら, 次のコマンドを実行して
ください:

```bash
$ gem install middleman
```

このコマンドは Middleman,  その依存ライブラリや Middleman を使うための
コマンドラインツールをインストールします。

このインストールプロセスはあなたの環境に新しいコマンドと 3 つの便利な機能を
追加します:

```bash
$ middleman init
$ middleman server
$ middleman build
```

それぞれのコマンドの使用方法は次のセクションで説明します。
[新しいサイトの作成][Start a New Site] へ。

  [Start a New Site]: /jp/basics/start-new-site
