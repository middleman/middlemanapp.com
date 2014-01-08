---
title: きれいな URL (ディレクトリインデックス)
---

# きれいな URL (ディレクトリインデックス)

デフォルト設定では Middleman はプロジェクトの中であなたが記述したとおり正確にファイルを出力します。例えば `source` フォルダの中の `about-us.html.erb` ファイルはプロジェクトのビルド時に `about-us.html` として出力されます。 `example.com` の Web サーバのルートディレクトリにプロジェクトを配置すれば, このページは次の URL でアクセスできます: `http://example.com/about-us.html`



Middleman は `.html` 毎にフォルダを作り, そのフォルダの index としてテンプレートを出力するディレクトリインデックス拡張を提供します。`config.rb` で次のように指定します:

``` ruby
activate :directory_indexes
```

このプロジェクトがビルドされた時,  `about-us.html.erb` ファイルは `about-us/index.html` として出力されます。"index ファイル" 対応の Web サーバに置かれた場合 (Apache や Amazon S3), このページは次の URL でアクセスできます:

``` ruby
http://example.com/about-us
```

別のファイル名で出力したい場合, `index_file` 変数が設定できます。例えば IIS では default.html が使用されます:

``` ruby
set :index_file, "default.html"
```

もしくは PHP ファイルにしたい場合:

``` ruby
set :index_file, "index.php"
```

#### アセットパスに関する注意事項

ディレクトリインデックスを有効化する場合, 画像ファイル名だけでアセットファイルの呼び出し (例: 画像ファイル) を行うと失敗します。次のように完全な抽象パスを使って呼び出す必要があります:

``` ruby
![すごい画像](/posts/2013-09-23-some-interesting-post/amazing-image.png)
```

わずかにこのプロセスを自動化するには, MarkDown をまずは ERB で作成します。例えば `/posts/2013-09-23-some-interesting-post.html.markdown.erb` ファイルがあるとします:

``` ruby
![すごい画像](<%= current_page.url %>some-image.png)
```

## オプトアウト

自動的に名前を変更したくないページがある場合, 除外できます:

``` ruby
page "/i-really-want-the-extension.html", :directory_index => false
```

1 度にたくさんのファイルのインデックスを無効化にしたい場合は, `page` には正規表現かファイルのパターンマッチを与えることができます。

ページ毎に [YAML 形式の Frontmatter](/jp/basics/frontmatter/) に `directory_index: false` を追加することもできます。

## 手動インデックス

テンプレートのファイル名がすでに `index.html` の場合, Middleman は手をつけません。例えば, `my-page/index.html.erb` はあなたの予想どおり `my-page/index.html` としてビルドされます。
