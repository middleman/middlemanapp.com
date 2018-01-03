---
title: レイアウト
---

# レイアウト

レイアウト機能はテンプレート間で共有する, 個別ページを囲むための共通 HTML の
使用を可能にします。PHP 開発経験のある開発者であればページ毎に, その上部と下部に
"header" や "footer" への参照をもつ使い方をしたことがあるでしょう。
Ruby の世界と Middleman では逆のアプローチを取ります。
"layout" は "header" や "footer" 両方を含むことで個別ページのコンテンツを
囲みます。

<iframe width="560" height="315" src="https://www.youtube.com/embed/jHDZkYaqtSo?rel=0" frameborder="0" allowfullscreen></iframe>

最も基本的なレイアウトは共有コンテンツとそのテンプレートの内容を
配置する `yield` を含みます。

ERB を使ったレイアウトの例です:

```erb
<html>
<head>
  <title>私のサイト</title>
</head>
<body>
  <%= yield %>
</body>
</html>
```

ERB で書かれたページテンプレートが与えられます:

```erb
<h1>Hello World</h1>
```

組み合わされた最終的な HTML 出力は次のように:

```html
<html>
<head>
  <title>私のサイト</title>
</head>
<body>
  <h1>Hello World</h1>
</body>
</html>
```

ファイル拡張子とパーサに関しては, レイアウト機能はビルドプロセスの中で
テンプレートと異なる機能をもっているので, 正しい拡張子を与えるよう
注意する必要があります。次がその理由です:

セクション毎に異なるテンプレートを集めるような場合, ファイル拡張子は
重要です。例えば, レイアウトファイルを `layout.html.erb` と名付けることで,
言語パーサにこのファイルは erb として扱えと命じることになり, html に
変換されます。

ある意味で, 拡張子を右から左に解釈することは,
ファイルが左端の拡張子形式のファイルとしてパース処理されることを
知らせます。例の場合, ファイルが与えられた時に erb から html に変換し,
ファイルをビルドします。

テンプレートとは異なり, レイアウトは html にレンダリングされません。レイアウトの
ファイル名の左端の拡張子に `.html` を与えた場合, ビルド時のエラーの原因になります。
したがって, 例えば `layout.erb` のような形式で拡張子をつける必要があります。

## カスタムレイアウト

デフォルトでは, Middleman はそのサイトのすべてのページに同じレイアウトを適用
します。複数のレイアウトを使い, どのページがどのレイアウトを使うのか指定したい
場合があります。例えば, それぞれ独自のレイアウトをもつ "公開" サイトと "admin"
サイトがあるような場合です。

デフォルトのレイアウトは `source` フォルダの中で "layout" と名付けられ, 使用する
テンプレート言語の拡張子を持ちます。デフォルトでは `layout.erb` です。
あなたが作るレイアウトは `source/layouts` フォルダに置かれます。

admin 用の新しいレイアウトを作るには, `source/layouts` フォルダに
新たに "admin.erb" ファイルを追加します。次の内容だったとします:

```erb
<html>
<head>
  <title>Admin Area</title>
</head>
<body>
  <%= yield %>
</body>
</html>
```

次に, どのページがこのレイアウトを使用するのか指定する必要があります。
次の 2 つの方法で指定することができます。ページの大きなグループにこのレイアウト
を適用したい場合, `config.rb` で "page" コマンドを使うことができます。
`source` フォルダの中に "admin" フォルダがある状態で "admin" の中のテンプレートは
admin レイアウトを使うとしましょう。`config.rb` は次のようになります:

```ruby
page "/admin/*", :layout => "admin"
```

ページのパスにワイルドカードを使うことで admin フォルダ以下のすべてのページが
admin レイアウトを使うように指定しています。

ページで直接指定することもできます。例えば, source フォルダに
`login.html.erb` が置かれているが, admin レイアウトを適用したい場合です。
ページテンプレートの例として次を使います。

```html
<h1>Login</h1>
<form>
  <input type="email">
  <input type="password">
  <input type="submit">
</form>
```

この特別なページには次のようにカスタムレイアウトを指定できます:

```ruby
page "/login.html", :layout => "admin"
```

これは login ページが admin レイアウトを使うように指定しています。
`config.rb` ですべて指定する代わりに, [Frontmatter] を使ってテンプレートの
ページ毎にレイアウトを指定することもできます。
`login.html.erb` ページ自身にレイアウトを指定する例です。

```html
---
layout: admin
---

<h1>Login</h1>
<form>
  <input type="email">
  <input type="password">
  <input type="submit">
</form>
```

## 入れ子レイアウト

入れ子レイアウトはレイアウトの積み重ねを作成できます。この機能を理解する
最も簡単なユースケースは `middleman-blog` 拡張です。ブログ記事はサイト全体の
コンテンツの部分集合です。ブログ記事によって追加された内容と構造を含みますが,
最終的にサイト全体の構造によって囲まれる必要があります (header,
footer など) 。

シンプルなデフォルトのレイアウトは次のようになります:

```erb
<html>
  <body>
    <header>ヘッダ</header>
    <%= yield %>
    <footer>フッタ</footer>
  </body>
</html>
```

blog 記事が blog/my-article.html.markdown に置かれているとします。すべての
blog 記事が デフォルトの `layout` に代わり `article_layout` を使うように
指定します。 config.rb を編集:

```ruby
activate :blog do |blog|
  blog.layout = "article_layout"
end

# または:

page "blog/*", :layout => :article_layout
```

`layouts/article_layout.erb` は次のようになります:

```erb
<% wrap_layout :layout do %>
  <article>
    <%= yield %>
  </article>
<% end %>
```

**Note:** Haml や Slim では次のように等号を使う必要があります:

```haml
= wrap_layout :layout do
```

通常のレイアウトと同じように, `yield` はテンプレートの出力内容が
配置される場所です。この例では次の出力になります:

```html
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

## 完全なレイアウト無効化

いくつかの場合では, まったくレイアウトを使いたくない場合があります。
`config.rb` でデフォルトのレイアウトを無効化することで対応できます:

```ruby
set :layout, false

# もしくは個別のファイルで:
page '/foo.html', :layout => false
```

  [Frontmatter]: /jp/basics/frontmatter/
