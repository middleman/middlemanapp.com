---
title: ビルド & デプロイ
---

# 静的サイトのエクスポート

## "middleman build" でサイトをビルド

静的サイトのコードを出力する準備ができている, または "ブログモード" で
静的ブログをホストするような場合, サイトをビルドする必要があります。
コマンドラインを使い, プロジェクトフォルダの中から `middleman build` を実行してください:

```bash
$ cd my_project
$ bundle exec middleman build
```

このコマンドは `source` フォルダにあるファイル毎に静的ファイルを作ります。
テンプレートファイルがコンパイルされ, その静的ファイルがコピーされ, 有効化された
ビルド時の機能 (圧縮のような) が実行されます。 Middleman は
自動的に前回のビルドから残っていて
今回は生成されないファイルを削除します。

## サイトをデプロイ

サイトをビルドすることで, 必要なものはすべて `build` ディレクトリに
用意されます。静的なビルドデータをデプロイする方法はほぼ無限にあります。
ここでは私たち独自のソリューションを紹介します。
web 検索や [デプロイ拡張ディレクトリ](https://directory.middlemanapp.com/#/extensions/deployment)
を探すことで `middleman` プロジェクトのデプロイの選択肢を探すことができます。
あなたが `middleman` プロジェクトをデプロイするツールの作者であれば,
[ここ](https://directory.middlemanapp.com/#/extensions/deployment) から
PR をしてください。

ビルドファイルをデプロイする便利なツールがあります。
[`middleman-deploy`](https://github.com/middleman-contrib/middleman-deploy) です。
このツールは rsync, ftp, sftp や git を用いてデプロイを行うことができます。

```bash
$ middleman build [--clean]
$ middleman deploy [--build-before]
```

## プロダクション環境のアセットハッシュ & CDN 設定

プロダクション環境では一般的にアセットファイル名にハッシュ文字列を付与し CDN でそのファイルを提供します。
Middleman を使うことで簡単に対応することができます:

```ruby
configure :build do
  activate :minify_css
  activate :minify_javascript

  # アセットファイルの URL にハッシュを追加 (URL ヘルパの使用が必要)
  activate :asset_hash

  activate :asset_host, :host => '//YOURDOMAIN.cloudfront.net'
end
```
