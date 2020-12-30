---
title: 開発サイクル
---

# 開発サイクル

## Middleman Server

Middleman は開発のスタート時点から開発コードとプロダクションコードを分離します。
これにより開発中にプロダクションでは不要もしくは望ましくないツール群
([Haml], [Sass] や [CoffeeScript] などのような) を開発中に利用することができます。
開発サイクルや静的サイトのビルドをこれらの環境に
依存することができます。

Middleman を使う時間の大半は開発サイクルに
なります。

コマンドラインから, プロジェクトフォルダの中でプレビューサーバを
起動してください:

```bash
$ cd my_project
$ bundle exec middleman server
```

このコマンドはローカルの Web サーバを起動します: `http://localhost:4567/`

`source` フォルダでファイルを作成編集し, プレビュー Web サーバ上で
反映された変更を確認することができます。

コマンドラインから <kbd>Ctrl</kbd> + <kbd>C</kbd> を使って
プレビューサーバを停止できます。

### 飾りのない `middleman` コマンド

コマンド指定なしの `middleman` の使用は `middleman server` と同じコマンドを
意味します。

```bash
$ bundle exec middleman
```

## LiveReload

Middleman にはサイト内のファイルを編集するたびにブラウザを自動的にリロードする
拡張がついています。まず Gemfile に `middleman-livereload` があることを
確認してください。続いて `config.rb` を開いて次の行を
追加してください。

```ruby
activate :livereload
```

`bundle install` を実行し追加した `middleman-livereload` をインストールしてください。
これであなたのブラウザはページ内容に変更があると自動的にリロードされます。

### CSS のリロード

デフォルト設定では middleman は `stylesheets/all.css` の import された CSS が変更
された場合リロードします。`:livereload_css_target` に異なるファイル名を設定する
ことができます。`nil` を指定するとすべての CSS ファイルでリロードが行われます。

  [Haml]: http://haml.info
  [Sass]: http://sass-lang.com
  [CoffeeScript]: http://coffeescript.org/
