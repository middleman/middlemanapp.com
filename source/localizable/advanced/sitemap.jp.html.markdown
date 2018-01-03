---
title: サイトマップ
---

# サイトマップ

Middleman にはテンプレートからアクセスできる,
サイト内のすべてのページとリソース, 相互にどのように関係するか情報を持つ
サイトマップがあります。これはナビゲーションの作成, 検索ページや
フィードの作成に使うことができます。

<iframe width="560" height="315" src="https://www.youtube.com/embed/oqUGm-LD_BM?rel=0" frameborder="0" allowfullscreen></iframe><br>

[サイトマップ][sitemap] はページごとの HTML, CSS, JavaScript, 画像など
すべての情報のリポジトリです。`:proxy` を使って作る [動的ページ][dynamic pages]
も含みます。

## サイトマップを確認する

Middleman がどのようにサイトを見ているか正確に理解するために, プレビューサーバを
起動しブラウザで `http://localhost:4567/__middleman/sitemap/` を開きます。
完全なサイトマップやソースへのパス, ビルド先のパス, URL など各リソースを
確認できます。"path" には特に注意してください:
config.rb の `page`, `ignore` や `proxy`,  `link_to` や `url_for` から
ファイルを参照するために使われます。

## コードからサイトマップにアクセス

テンプレートの中では `sitemap` がサイトマップオブジェクトです。
サイトマップオブジェクトから, ページごとに [`resources`] メソッドを使うか
[`find_resource_by_path`] を使って個別のリソースを取得できます。
`current_resource` を使ってカレントページのページオブジェクトを取得することも
できます。サイトマップからページリストを取得できれば, 個々のページオブジェクトを
使って, 各種プロパティをフィルタリングできます。

## サイトマップのリソース

サイトマップの各リソースは [Resource] オブジェクトです。Resource オブジェクトは
あらゆる種類の情報を伝えます。[frontmatter] データ, ファイル拡張子, ソースと
出力先のパス, リンク URL などにアクセスできます。Resource オブジェクトの
プロパティは Middleman の内部レンダリングにとても便利です。例えば,
すべての `.html` ファイルを見つけるためにファイル拡張子でページ
フィルタリングすることが考えられます。

それぞれのページはサイト階層の中で関連する他のページを探すこともできます。
`parent`, `siblings` や `children` メソッドはナビゲーションメニューや
パンくずリストを作る場合に特に便利です。

## config.rb の中でサイトマップを使う

サイトマップの情報を使って `config.rb` から新しい [動的ページ][dynamic pages] を
作ることができます。ただし, サイトマップは `config.rb` が読み込まれた *後* まで
用意されないので少し注意が必要です。これに対応するために, アプリケーションの
`ready` イベントにコールバックを登録する必要があります。例として, ページの
[frontmatter] に "category" が追加されているものとして, カテゴリーごとに
動的にカテゴリーページを作ります。
`config.rb` に次の内容を追加:

```ruby
ready do
  sitemap.resources.group_by {|p| p.data["category"] }.each do |category, pages|
    proxy "/categories/#{category}.html", "category.html",
      :locals => { :category => category, :pages => pages }
  end
end
```

そして, 取得したカテゴリごとにページをビルドするために, `category` と `pages`
変数を使う `category.html.erb` を作ります。

  [sitemap]: http://www.rubydoc.info/gems/middleman-core/Middleman/Sitemap
  [dynamic pages]: /jp/advanced/dynamic-pages/
  [`resources`]: http://www.rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#resources-instance_method
  [`find_resource_by_path`]: http://www.rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#find_resource_by_path-instance_method
  [Resource]: http://www.rubydoc.info/gems/middleman-core/Middleman/Sitemap/Resource
  [frontmatter]: /jp/basics/frontmatter/
