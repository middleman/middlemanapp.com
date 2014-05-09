---
title: テンプレート
---

# テンプレート

Middleman は HTML の開発を簡単にするために多くのテンプレート言語へのアクセスを提供します。テンプレート言語はページ内で変数やループを使えるようにするシンプルなものから, ページを HTML に変換するまったく異なったフォーマットを提供するものにまで及びます。 Middleman は ERb, Haml, Sass, Scss や CoffeeScript のサポートを搭載しています。Tilt が有効な gem であればその他にも多くのエンジンが有効化できます。[次のリストを参照してください](#他のテンプレート言語)。

## テンプレートの基礎

デフォルトのテンプレート言語は ERb です。ERb は変数の追加, メソッド呼び出し, ループの使用や if 文を除き, そのままの HTML です。このガイドの次のセクションでは使用例として ERb を使います。

Middleman で使うテンプレートはそのファイル名にテンプレート言語の拡張子を含みます。ERb で書かれたシンプルな index ページはファイル名の `index.html` と ERb の拡張子を含む `index.html.erb` という名前になります。

まず, このファイルには単純な HTML が書かれています:

``` html
<h1>ようこそ</h1>
```

思いつきでループを追加することができます:

``` html
<h1>ようこそ</h1>
<ul>
  <% 5.times do |num| %>
    <li>カウント <%= num %></li>
  <% end %>
</ul>
```

## レイアウト

レイアウト機能はテンプレート間で共有する, 個別ページを囲むための共通 HTML の使用を可能にします。PHP 開発経験のある開発者であればページ毎に, その上部と下部に "header" や "footer" への参照をもつ使い方をしたことがあるでしょう。Ruby と Middleman では逆のアプローチを取ります。"layout" は "header" や "footer" 両方を含むことで個別ページのコンテンツを囲みます。

最も基本的なレイアウトは共有コンテンツとそのテンプレートの内容を配置する `yield` を含みます。

 ERb を使ったレイアウトの例です:

``` html
<html>
<head>
  <title>私のサイト</title>
</head>
<body>
  <%= yield %>
</body>
</html> 
```

ERb で書かれたページテンプレートが与えられます:

``` html
<h1>Hello World</h1>
```

組み合わされた最終的な HTML 出力は次のようになります:

``` html
<html>
<head>
  <title>私のサイト</title>
</head>
<body>
  <h1>Hello World</h1>
</body>
</html>
```

ファイル拡張子とパーサに関しては, レイアウト機能はビルドプロセスの中でテンプレートと異なる機能をもっているので, 正しい拡張子を与えるよう注意する必要があります。次がその理由です:

セクション毎の異なるテンプレートを集めるような場合, ファイル拡張子は重要です。例えば, レイアウトファイルを `layout.html.erb` と名付けることで, 言語パーサにこのファイルは erb として扱えと命じることになり, html に変換されます。

ある意味で, 拡張子を右から左に解釈することは, ファイルが左端の拡張子形式のファイルとしてパース処理されることを知らせます。例の場合, ファイルが与えられた時に erb から html に変換し, ファイルをビルドします。

テンプレートとは異なり, レイアウトは html にレンダリングされるべきではありません。レイアウトのファイル名の左端の拡張子に `.html` を与えた場合, ビルド時のエラーの原因になります。したがって, 例えば `layout.erb` のような形式で拡張子をつける必要があります。

### カスタムレイアウト

デフォルトでは, Middleman はそのサイトのあらゆるページに同じレイアウトを適用します。しかし, 複数のレイアウトを使い, どのページがその他のレイアウトを使うのか指定したい場合があります。例えば, それぞれ独自のレイアウトをもつ "public" サイトと "admin" サイトがあるような場合です。

デフォルトのレイアウトは `source` フォルダの中で "layout" と名付けられ, 使用するテンプレート言語の拡張子を持ちます。デフォルトでは `layout.erb` です。あなたが作るその他のレイアウトは `source/layouts` フォルダに置かれます。

admin 用の新しいレイアウトを作るには, `source/layouts` フォルダに新たに "admin.erb" ファイルを追加します。次の内容だとします:

``` html
    <html>
    <head>
      <title>管理エリア</title>
    </head>
    <body>
      <%= yield %>
    </body>
    </html>
```

次に, どのページがこのレイアウトを使用するのか指定する必要があります。次の 2 つの方法で指定することができます。ページの大きなグループにこのレイアウトを適用したい場合, `config.rb` に "page" コマンドを使うことができます。 `source` フォルダの中に "admin" というフォルダがあり "admin" の中のテンプレートは admin レイアウトを使うとしましょう。 `config.rb` は次のようになります:

``` ruby
page "/admin/*", :layout => "admin"
```

ページのパスにワイルドカードを使うことで admin フォルダ以下のすべてのページが admin レイアウトを使うように指定しています。

ページで直接指定することもできます。例えば, source フォルダに `login.html.erb` が置かれているが, admin レイアウトを適用したい場合です。ページテンプレートの例として次を使います。

``` html
<h1>Login</h1>
<form>
  <input type="text" placeholder="Email">
  <input type="password">
  <input type="submit">
</form>
```

この特別なページに次のようにカスタムレイアウトを指定できます:

``` ruby
page "/login.html", :layout => "admin"
```

これは login ページが admin レイアウトを使うように指定しています。 `config.rb` ですべて指定する代わりに, [Frontmatter] を使ってテンプレートのページ毎にレイアウトを指定することもできます。`login.html.erb` ページ自身にレイアウトを指定する例です。

``` html
---
layout: admin
---

<h1>Login</h1>
<form>
  <input type="text" placeholder="Email">
  <input type="password">
  <input type="submit">
</form>
```

### 入れ子レイアウト

入れ子レイアウトはレイアウトの積み重ねを作成できます。この機能を理解する最も簡単なユースケースは `middleman-blog` 拡張です。ブログ記事はサイト全体のコンテンツの部分集合です。追加された内容と構造を含みますが, 最終的にサイト全体の構造によって囲まれる必要があります (header, footer など) 。

シンプルなデフォルトのレイアウトは次のようになります:

``` html
<html>
  <body>
    <header>ヘッダ</header>
    <%= yield %>
    <footer>フッタ</footer>
  </body>
</html>
```

blog 記事が `blog/my-article.html.markdown` に置かれているとします。すべての blog 記事が デフォルトの `layout` に代わり `article_layout` を使うように指定します。 `config.rb` の記述です:

``` ruby
page "blog/*", :layout => :article_layout
```

`layouts/article_layout.erb` は次のようになります:

``` html
<% wrap_layout :layout do %>
  <article>
    <%= yield %>
  </article>
<% end %>
```

通常のレイアウトと同じように, `yield` はテンプレートの出力内容が配置される場所です。この例では次の出力になります:

``` html
<html>
  <body>
    <header>ヘッダ</header>
    <article>
      <!-- テンプレート/ブログ記事の内容 -->
    </article>
    <footer>フッタ</footer>
  </body>
</html>
```

### 完全なレイアウト無効化

いくつかの場合では, まったくレイアウトを使いたくない場合があります。 `config.rb` でデフォルトのレイアウトを無効化することで対応できます。

``` ruby
set :layout, false

# もしくは個別のファイルで:
page '/foo.html', :layout => false
```

## パーシャル

パーシャルはコンテンツの重複を避けるためにページ全体にわたってそのコンテンツを共有する方法です。パーシャルはページテンプレートとレイアウトで使うことができます。上記 2 つのレイアウトをもつ例を続けましょう: 通常のページと admin ページです。この 2 つのレイアウトには footer のように重複する内容があります。 footer パーシャルを作成し, これらのレイアウトで使ってみましょう。

パーシャルのファイル名は prefix にアンダースコアが付き, 使用するテンプレート言語の拡張子を含みます。例として `source` フォルダに置かれる `_footer.erb` と名付けられた footer パーシャルを示します:

``` html
<footer>
  Copyright 2011
</footer>
```

次に, "partial" メソッドを使ってデフォルトのレイアウトにパーシャルを配置します:

``` html
<html>
<head>
  <title>私のサイト</title>
</head>
<body>
  <%= yield %>
  <%= partial "footer" %>
</body>
</html>
```

admin レイアウトでは次のように:

``` html
<html>
<head>
  <title>管理エリア</title>
</head>
<body>
  <%= yield %>
  <%= partial "footer" %>
</body>
</html>
```

すると, `_footer.erb` への変更はこのパーシャルを使うそれぞれのレイアウトやレイアウトを使うページに表示されます。

複数のページやレイアウトに Copy&Paste する内容を見つけた場合, パーシャルに内容を抽出するのは良い方法です。

パーシャルを使い始めたら, 変数を渡すことで異なった呼び出しを行いたいかもしれません。次の方法で対応出来ます:

``` html
<%= partial(:paypal_donate_button, :locals => { :amount => 1, :amount_text => "Pay $1" }) %>
<%= partial(:paypal_donate_button, :locals => { :amount => 2, :amount_text => "Pay $2" }) %>
```

すると, パーシャルの中で次のようにテキストを設定することができます:

``` html
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <input name="amount" type="hidden" value="<%= "#{amount}.00" %>" >
  <input type="submit" value="<%= amount_text %>" >
</form>
```

詳細については [Padrino partial helper] のドキュメントを読んでください。

## テンプレートエンジンオプション

`config.rb` にテンプレートエンジンのオプションを設定することができます:

```ruby
set :haml, { :ugly => true, :format => :html5 }
```

## Markdown

`config.rb` で一番好きな Markdown ライブラリを選び, オプションを設定することができます:

```ruby
set :markdown_engine, :redcarpet
set :markdown, :fenced_code_blocks => true, :smartypants => true
```

RedCarpet を使う場合, Middleman はヘルパを用いて `:relative_links` や `:asset_hash` が行うようにリンクや画像タグを処理します。しかし, デフォルトの Markdown エンジンはインストールが簡単なことから Kramdown になっています。


### 他のテンプレート言語

Tilt 対応のテンプレート言語と RubyGems のリストです。動作させるにはインストール (`config.rb` で読み込む) しなければなりません。

エンジン                | ファイル拡張子         | 必要なライブラリ
------------------------|------------------------|----------------------------
Slim                    | .slim                  | slim
Erubis                  | .erb, .rhtml, .erubis  | erubis
Less CSS                | .less                  | less
Builder                 | .builder               | builder
Liquid                  | .liquid                | liquid
RDiscount               | .markdown, .mkd, .md   | rdiscount
Redcarpet               | .markdown, .mkd, .md   | redcarpet
BlueCloth               | .markdown, .mkd, .md   | bluecloth
Kramdown                | .markdown, .mkd, .md   | kramdown
Maruku                  | .markdown, .mkd, .md   | maruku
RedCloth                | .textile               | redcloth
RDoc                    | .rdoc                  | rdoc
Radius                  | .radius                | radius
Markaby                 | .mab                   | markaby
Nokogiri                | .nokogiri              | nokogiri
CoffeeScript            | .coffee                | coffee-script
Creole (Wiki markup)    | .wiki, .creole         | creole
WikiCloth (Wiki markup) | .wiki, .mediawiki, .mw | wikicloth
Yajl                    | .yajl                  | yajl-ruby
Stylus                  | .styl                  | stylus

[Haml]: http://haml-lang.com/
[Slim]: http://slim-lang.com/
[Markdown]: http://daringfireball.net/projects/markdown/
[these guides are written in Markdown]: https://raw.github.com/middleman/middleman-guides/master/source/guides/basics-of-templates.html.markdown
[Frontmatter]: /jp/basics/frontmatter/
[Padrino partial helper]: http://www.padrinorb.com/api/classes/Padrino/Helpers/RenderHelpers.html
