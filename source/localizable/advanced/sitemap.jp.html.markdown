---
title: サイトマップ
---

# サイトマップ

Middleman にはテンプレートからアクセスできる, サイト内のすべてのページとリソース, それらが互いにどのように関係するか情報を持つサイトマップがあります。これはナビゲーションの作成, 検索ページやフィードの作成に使うことができます。

[サイトマップ](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap) はページごとの HTML, CSS, JavaScript, 画像などすべての情報のリポジトリです。`:proxy` を使って作る [動的ページ][dynamic pages] も含みます。

## サイトマップを確認する

Middleman がどのようにサイトを見ているか正確に理解するために, プレビューサーバを起動しブラウザで http://localhost:4567/__middleman/sitemap/ を開きます。完全なサイトマップやソースへのパス, ビルド先のパス, URL など各リソースを確認できます。"path" には特に注意してください: config.rb の `page`, `ignore` や `proxy`,  `link_to` や `url_for` からファイルを参照するために使います。

## コードからサイトマップにアクセス

テンプレートの中では `sitemap` がサイトマップオブジェクトです。サイトマップオブジェクトから, ページごとに [`resources`](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#resources-instance_method) メソッドを使うか [`find_resource_by_path`](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#find_resource_by_path-instance_method) を使って個別のリソースを取得できます。`current_resource` を使ってカレントページのページオブジェクトを取得することもできます。サイトマップからページリストを取得できれば, 個々のページオブジェクトを使って, 各種プロパティをフィルタリングできます。

## サイトマップのリソース

サイトマップの各リソースは [Resource](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Resource) オブジェクトです。Resource オブジェクトはあらゆる種類の情報を伝えます。[frontmatter] データ, ファイル拡張子, ソースと出力先のパス, リンク URL などにアクセスできます。Resource オブジェクトのプロパティは Middleman の内部レンダリングにとても便利です。例えば, すべての `.html` ファイルを見つけるためにファイル拡張子でページをフィルタリングすることが考えられます。

それぞれのページはサイト階層の中で関連する他のページを探すこともできます。 `parent`, `siblings` や `children` メソッドはナビゲーションメニューやパンくずリストを作る場合に特に便利です。

サイトマップは ActiveRecord ライクな構文で照会できます:

```ruby
sitemap.where(:tags.include => "homepage").order_by(:priority).limit(10)
```

クエリのインターフェイスは [Middleman::Sitemap::Queryable](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Queryable) を確認してください。

## config.rb の中でサイトマップを使う

サイトマップの情報を使って `config.rb` から新しい [動的ページ][dynamic pages] を作ることができます。ただし, サイトマップは `config.rb` が読み込まれた *後* まで用意されないので少し注意が必要です。これに対応するために, アプリケーションの `ready` イベントにコールバックを登録する必要があります。例として, ページの [frontmatter] に "category" が追加されているものとして, カテゴリーごとに動的にカテゴリーページを作ります。`config.rb` に次の内容を追加:

``` ruby
ready do
  sitemap.resources.group_by {|p| p.data["category"] }.each do |category, pages|
    proxy "/categories/#{category}.html", "category.html",
      :locals => { :category => category, :pages => pages }
  end
end
```

そして, 取得したカテゴリごとにページをビルドするために, `category` と `pages` 変数を使う `category.html.erb` を作ります。

[dynamic pages]: /jp/basics/dynamic-pages/
[frontmatter]: /jp/basics/frontmatter/
