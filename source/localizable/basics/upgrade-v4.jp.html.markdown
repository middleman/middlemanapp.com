---
title: v4 へのアップグレード
---

# v4 へのアップグレード

v4 では, コアのあまり使われない機能を削除し, より良いアプローチに変更するか拡張機能に置き換えています。

このアップグレードに関するドキュメントは作業中のもので, v4 ベータのアップデートにともない変更していきます。機能削除に関するアップグレードの説明が分かりづらかったり変更にともない書き換えなければいけないコードに関しての記載がない場合, 私たちに知らせてください。ドキュメントを見直すかその部分を削除します。

変更リストです:

* `config.rb` で使用する `page` コマンドの `proxy` と `ignore` オプションは削除されました。`page` のオプションに代わりに `proxy` または `ignore` コマンドを使用してください。
* 設定の `with_layout` が削除されました。代わりに `page` のループを使ってください。
* Queryable Sitemap API は削除されました。
* `css_compressor` の設定は代わりに `activate :minify_css, :compressor =>` を使用してください。
* `js_compressor` の設定は代わりに `activate :minify_javascript, :compressor =>` を使用してください。
* 非 Middleman プロジェクトのフォルダ内の静的コンテンツを配信する機能は削除されました。
* "暗黙の拡張子機能" は削除されました。すべてのテンプレートは完全なファイル名 + 利用したい拡張子リストが必ず含まなければなりません。
* CLI の `upgrade` と `install` コマンドは削除されました。
* テンプレートの中で使用されていた `page` 変数が削除されました。`current_resource` を使用してください。
* `page` と `proxy` のブロック引数サポートはドロップされました。
* テンプレートの中のインスタンス変数利用のサポートはドロップされました。
* 非推奨になっていた `request` インスタンスは削除されました。
* 古いモジュールスタイルの拡張のサポートは削除されました。
* Compass は拡張機能になりました。デフォルトでは同梱されます。

v4 へのリファクタリングによって多くのコードが変更されています。上記リストまたは当サイトに記載されていない内部メソッドを利用していた場合には, 変更されている可能性があります。質問がある場合には気軽に聞いてください。

## 開発環境と `configure` ブロックの変更

v4 では異なるターゲット環境を区別する機能が追加されています。これ以前は, 設定の変更が可能な開発環境の`development` とファイルを出力モードの `build` の 2 つの環境を組み合わせていました。

v4 ではこれらの環境を分離します。`development` と `production` の 2 つのデフォルト開発環境がありますが, 必要な環境を簡単に追加することもできます。`server` と `build` の 2 つの出力モードもあります。

`middleman server` はデフォルトで出力モードとして `server` と開発環境として `development` を利用します。

`middleman build` はデフォルトで出力モードとして `build` と開発環境として `production` を利用します。

`configure` コマンドを使って開発環境と出力モードともに設定できます:

```
configure :server { # sprockets デバッグの有効化 }
configure :build { # ビルド後のフックで処理 }
configure :development { # sass のデバッグ設定 }
configure :production { activate :minify_html }
```

この変更はほとんどの Middleman ユーザに影響します。

Rails のように, あらかじめ決められているパスから環境固有の設定を自動的に読み込みます。production の設定がある場合, `environments/production.rb` にファイルを作ることで, production 環境の場合に自動的に読み込まれます。

出力モードに関係なく開発環境を変更することも可能です。例えば, サーバ起動中に production の出力をプレビューすることができます: `middleman server -e production`

開発環境フラグ `-e` は独自に用意された開発環境モードでも使用できます。ステージングサーバにコードをプッシュしたい場合には `middleman build -e staging` コマンドを使うことでステージング環境のデプロイ処理が書かれた `environments/staging.rb` が使用できます。

## Rack サーバ内でのファイルアップデート

リファクタリングによって, Middleman は Rack サーバとして動作し, 通常通りファイルの変更に対応することができます。この変更によって Rails 内へのマウントや Pow の使い勝手が良くなります。

## Git によるテンプレートのインストール

`~/.middleman` や gem から再利用可能なテンプレートを作成する機能を削除しました。代わりに, `middleman init` は Git リポジトリを指し示す `-T` フラグを利用できるようになります。与えられたパスに `://` のようなプロトコルが含まれない場合, Github にホストされているものだと仮定し処理します。

```
middleman init -T tdreyno/my-middleman-starter ~/Sites/new-site
```

この変更によりテンプレートの共有がより簡単になります。さらに Directory サイトに登録された Git リポジトリは登録された名称でアクセスできるようにマッピングされています。例えば:

```
middleman init -T amicus ~/Sites/new-site
```

## 外部ツール

私たちはできるだけ多くのツールをサポートしたいと考えています。Grunt を使いたい? バックグラウンドで ClojureScript JVM? browserify や ember-cli はどうでしょう? `external_pipeline` を使うことで実現できます。Middleman v4 がどうやって外部プロセスをコントロールするかの例です。任意のディレクトリにファイルを出力し, Middleman によって利用されます。

```
activate :external_pipeline,
  name: :ember,
  command: "cd test-app/ && ember #{build? ? :build : :serve} --environment #{config[:environment]}",
  source: "test-app/dist",
  latency: 2
```

これはかなり実験的なもので, 最終的なリリースの前にはたくさんの変更が必要になるでしょう。この機能は Middleman のサイトマップを作るために複数のディレクトリを組み合わせる下位レベルの機能によって提供されています。`bower_component` のようなディレクトリを source ディレクトリから分けておくのは良い習慣です。Middleman ではこれが可能です。

```
files.watch :source, path: File.expand_path('bower_components', app.root)
```

## コレクション

最後に紹介する機能は "コレクション" です。コレクションは Ruby のコードでファイルやパスのグループを定義できるように Middleman Blog から一部のロジックを抽出します。これはどこか変更されるたびに `config.rb` が実行されると思ってしまう新しいユーザのミスを回避します。コレクションはあなたが `config.rb` に書いたものは常に最新のものだと思わせます。

タグ付けを実装したいとしましょう:

```
def get_tags(resource)
  if resource.data.tags.is_a? String
    resource.data.tags.split(',').map(&:strip)
  else
    resource.data.tags
  end
end

def group_lookup(resource, sum)
  results = Array(get_tags(resource)).map(&:to_s).map(&:to_sym)

  results.each do |k|
    sum[k] ||= []
    sum[k] << resource
  end
end

tags = resources
  .select { |resource| resource.data.tags }
  .each_with_object({}, &method(:group_lookup))

collection :all_tags, tags
collection :first_tag, tags.keys.sort.first
```

この設定は常に最新の `all_tags` ハッシュと常に最新のアルファベット順で 1 番最初にくるタグをもつリソースを表す配列を与えます。見るとわかるように, コードは Ruby で書かれているので, あなたのやりたいように実装することができます。2 つだけ制約があります。1 つはコレクションは `resources` から始まるコレクションチェーンから作られること。もう 1 つは `collection` メソッドはテンプレートに情報を渡した後に呼び出されなければなりません。

```
<% collection(:tags).each do |k, items| %>
  Tag: <%= k %> (<%= items.length %>)
  <% items.each do |article| %>
    Article: <%= article.data.title %>
  <% end %>
<% end %>

最初のタグ: <%= collection(:first_tag) %>
```

コレクションは動的ページを作るために `config.rb` に直接書くこともできます:

```
tags.each do |k, articles|
  proxy "/tags/#{k}.html", "/tags/list.html", locals: {
    articles: articles
  }
end
```

繰り返しますが, コレクションは新しく実験的です。あなたはベータ期間中にこの機能に影響を与えることができます。
