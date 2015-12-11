---
title: 開発サイクル
---

# 開発サイクル

## Middleman Server

Middleman は開発のスタート時点から開発コードとプロダクションコードを分離します。
これにより開発中にプロダクションでは
不要もしくは望ましくないツール群 ([Haml](http://haml-lang.com) や
[Sass](http://sass-lang.com) などのような) を開発中に利用することが
できます。開発サイクルや静的サイトのビルドを
これらの環境に依存することができます。

Middleman を使う時間の大半は開発サイクルに
なります。

コマンドラインから, プロジェクトフォルダの中でプレビューサーバを
起動してください:

``` bash
$ cd my_project
$ bundle exec middleman server
```

このコマンドはローカルの Web サーバを起動します: `http://localhost:4567/`

`source` フォルダでファイルを作成編集し, プレビュー Web サーバ上で
反映された変更を確認することができます。

コマンドラインから `CTRL-C` を使ってプレビューサーバを停止できます。

### 飾りのない middleman コマンド

コマンド指定なしの `middleman` の使用はサーバの起動と同じです。

``` bash
$ bundle exec middleman
```

このコマンドは `middleman server` と同じ動作をします。

## LiveReload

Middleman にはサイト内のファイルを編集するたびにブラウザを自動的にリロードする拡張がついています。
まず Gemfile に `middleman-livereload` があることを確認してください。続いて `config.rb` を開いて次の行を追加してください。

``` ruby
activate :livereload
```

これであなたのブラウザはページ内容に変更があると自動的にリロードされます。

### CSS のリロード
デフォルト設定では middleman は `stylesheets/all.css` の import された CSS が変更された場合リロードします。
`:livereload_css_target` に異なるファイル名を設定することができます。`nil` を指定するとすべての CSS ファイルでリロードが行われます。

[HTML5 Boilerplate]: http://html5boilerplate.com
[SMACSS]: http://smacss.com
