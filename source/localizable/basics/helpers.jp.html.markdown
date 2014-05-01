---
title: テンプレートヘルパ
---

# テンプレートヘルパ

テンプレートヘルパはよくある HTML の作業を簡単にするため, 動的テンプレートの中で使用できるメソッドです。基本的なメソッドのほとんどは Rails のビューヘルパを利用したことのある人にはお馴染みのものです。すべてのヘルパは Padrino フレームワークによって組み込まれています。[完全なドキュメントはこちらを参照してください。][view the full documentation here]

## リンクヘルパ

Padrino はリンクタグを作るために `link_to` メソッドを提供します。基本的な使い方では `link_to` がリンク名とリンク URL を引数に取ります:

``` html
<%= link_to '私のサイト', 'http://mysite.com' %>
```

`link_to` はより複雑な内容のリンクを生成できるように, ブロックをとることもできます:

``` html
<% link_to 'http://mysite.com' do %>
  <%= image_tag 'mylogo.png' %>
<% end %>
```

Middleman は `link_to` に [サイトマップ](/jp/advanced/sitemap/) を把握させることで強化します。 source フォルダ (ファイル拡張子からテンプレート言語拡張子を除いた状態で) のページへの参照を与えると, `link_to` は [`:directory_indexes`](/jp/basics/pretty-urls/) 拡張が有効化されていたとしても, 正しいリンクを生成します。例えば, `source/about.html` ファイルがあり `:directory_indexes` が有効化されている場合, 次のようにリンクします:

``` html
<%= link_to 'About', '/about.html' %>

結果: <a href='/about/'>About</a>
```

現在ページから相対パスで参照することもできます。リンクを現在ページからの相対パスにしたいと考える人もいるでしょう。 `:relative => true` を渡すことで, `link_to` は相対 URL になります。

`:directory_indexes` が有効化され, source/foo/index.html.erb の中から相対パスを得る場合:

``` html
<%= link_to 'About', '/about.html', :relative => true %>
```

結果:

``` html
<a href='../about/'>About</a>
```

`link_to` で作られるあらゆる URL を相対パスにしたい場合, `config.rb` に次を追加します:

``` ruby
set :relative_links, true
```

相対パスにしたくないリンクに `:relative => false` を追加することで個別に上書きすることもできます。

`link_to` ヘルパが与えられた URL の属するページの決定に失敗した場合, 指定された URL を変更せず使います。この場合 `:relative_links` オプションは無視されますが, `:relative => true` を指定している場合はエラーが発生します。

[サイトマップ](/jp/advanced/sitemap/) リソースの [`url` メソッド][`url` method] ([Blog 機能](/jp/basics/blogging/) の BlogArticle に継承される) は *出力 URL* を返すことに注意してください。`link_to` ヘルパは対応する page/article の *ソースパス* に一致させることができず, 相対 URL に変換できない場合があります。

`link_to` に出力 URL を与える代わりに, `link_to` の URL 引数として Resource/Blogarticle の [`path` 属性][`path` attribute] を介した *ソースパス* を与えるか, 単にリソース自体のパスを与えます。どちらの方法も `link_to` に相対 URL を生成させます:

``` html
<ul>
  <% blog.articles.each do |article| %>
    <li>
      <%= link_to article.title, article.path, :relative => true %> <%# 第 2 引数の `article.path` に注目 %>
    </li>
  <% end %>
</ul>

<ul>
  <% sitemap.resources.select{|resource| resource.data.title}.each do |resource| %>
    <li>
      <%= link_to resource.data.title, resource, :relative => true %> <%# 第 2 引数の `resource` に注目 %>
    </li>
  <% end %>
</ul>
```

リンクのクエリパラメータやURLフラグメントを次のように含めることができます:

```ruby
<%= link_to '私のフォーム', '/form.html', :query => { :foo => 'bar' }, :fragment => 'deep' %>
```

結果:

```html
<a href='/form.html?foo=bar#deep'>私のフォーム</a>
```

リンクタグなしのページ URL が必要な場合, `url_for` を使ってください。これは `link_to` や `form_tag` の処理の中でも使われています。


## 出力ヘルパ

出力ヘルパは様々な方法で出力を管理, キャプチャ, 表示する重要なメソッドの集合で, より高いレベルのヘルパ機能をサポートするために使われます。説明すべき出力ヘルパが 3 つあります: `content_for`, `capture_html` や `concat_content`。

`content_for` はコンテンツのキャプチャを行い, レイアウトの中など異なった場所でのレンダリングをサポートします。その 1 つの例はテンプレートからレイアウトに assets を含めるものです:

``` html
<% content_for :assets do %>
  <%= stylesheet_link_tag 'index', 'custom' %>
<% end %>
```

テンプレートに追加することで, ブロックに含まれる部分をキャプチャしレイアウトで yield を用いて出力します:

``` html
<head>
  <title>例</title>
  <%= stylesheet_link_tag 'style' %>
  <%= yield_content :assets %>
</head>
```

自動的にブロックの内容 (この場合はスタイルシートが含まれる) をレイアウト内で yield された場所に挿入します。

`content_for?` にキーを与えることで `content_for` ブロックが存在するか確認することもできます:

``` html
<% if content_for?(:assets) %>
  <div><%= yield_content :assets %></div>
<% end %>
```

ブロック引数にも対応します。

``` ruby
yield_content :head, param1, param2
content_for(:head) { |param1, param2| ...content... }
```

## タグヘルパ

タグヘルパはビューテンプレート内の html "タグ" を生成するために使われる基本的なメソッドです。このカテゴリには 3 つの主要メソッドがあります: `tag`, `content_tag` と `input_tag`。

`tag` と `content_tag` はタグ名と指定されたオプションで任意の html タグを生成します。 html タグが "中身" を含む場合, `content_tag` が使われます。例:

``` html
<%= tag :img, :src => "/my_image.png" %>
  # => <img src='/my_image.png'>

<% content_tag :p, :class => "stuff" do %>
  こんにちわ
<% end %>
  # => <p class='stuff'>こんにちわ</p>
```

`input_tag` はユーザからの入力を受け付けるタグを生成するために使われます:

``` ruby
input_tag :text, :class => "demo"
  # => <input type='text' class='demo'>
input_tag :password, :value => "secret", :class => "demo"
  # => <input type='password' value='secret' class='demo'>
```

## アセットヘルパ

アセットヘルパはハイパーリンク, mail_to リンク, 画像, スタイルシートや JavaScript のような html をビューテンプレートに挿入する手助けをします。簡単なビューテンプレートでの使用方法は次のようになります:

``` html
<html>
<head>
  <%= stylesheet_link_tag 'layout' %>
  <%= javascript_include_tag 'application' %>
  <%= favicon_tag 'images/favicon.png' %>
</head>
<body>
  <p><%= link_to 'Blog', '/blog', :class => 'example' %></p>
  <p>Mail me at <%= mail_to 'fake@faker.com', "Fake Email Link",
                      :cc => "test@demo.com" %></p>
  <p><%= image_tag 'padrino.png', :width => '35',
           :class => 'logo' %></p>
</body>
</html>
```

`auto_stylesheet_link_tag` や `auto_javascript_include_tag` は現在ページのパスを元にスタイルシートや JS のタグを生成します。ページが "contact.html" の場合, "contact.css" や "contact.js" の参照になります。

## Form ヘルパ

Form ヘルパは form を作る際に使うであろう "一般的な" form タグのヘルパです。非オブジェクトの form が作られる場合の簡単な例は次のように:

``` html
<% form_tag '/destroy', :class => 'destroy-form', :method => 'delete' do %>
  <% field_set_tag do %>
    <p>
      <%= label_tag :username, :class => 'first' %>
      <%= text_field_tag :username, :value => params[:username] %>
    </p>
    <p>
      <%= label_tag :password, :class => 'first' %>
      <%= password_field_tag :password, :value => params[:password] %>
    </p>
    <p>
      <%= label_tag :strategy %>
      <%= select_tag :strategy, :options => ['delete', 'destroy'],
          :selected => 'delete' %>
    </p>
    <p>
      <%= check_box_tag :confirm_delete %>
    </p>
  <% end %>
  <% field_set_tag(:class => 'buttons') do %>
    <%= submit_tag "削除" %>
  <% end %>
<% end %>
```

## フォーマットヘルパ

フォーマットヘルパはテキストを目的に合わせて加工するための便利なメソッドです。
フォーマットヘルパの代表的なものは `escape_html`, `distance_of_time_in_words`, `time_ago_in_words` と `js_escape_html` の 4 つです。

`escape_html` と `js_escape_html` メソッドは html 文字列を取得し, 特定の文字をエスケープするものです。
`escape_html` は HTML/XML のアンパサンド, 括弧や引用符をエスケープします。テンプレートに表示する前にユーザコンテンツをエスケープするのに便利です。 `js_escape_html` は JS テンプレートから JavaScript の関数に情報を与える場合に使用されます。

``` ruby
escape_html('<hello>&<goodbye>') # => &lt;hello&gt;&amp;&lt;goodbye&gt;
```

テンプレート内で簡単に使うために `h` という `escape_html` のエイリアスもあります。

フォーマットヘルパは `simple_format`, `pluralize`, `word_wrap` や `truncate` のようなテキストの加工に便利な機能も含みます。

``` ruby
simple_format("hello\nworld")
  # => "<p>hello<br/>world</p>"
pluralize(2, '人')
  # => '2 人'
word_wrap('Once upon a time', :line_width => 8)
  # => "Once upon\na time"
truncate("Once upon a time in a world far far away", :length => 8)
  # => "Once upon..."
truncate_words("Once upon a time in a world far far away", :length => 4)
  # => "Once upon a time..."
highlight('Lorem dolor sit', 'dolor')
  # => "Lorem <strong class="highlight">dolor</strong> sit"
```

## ダミーテキスト & Placehold.it ヘルパ

Sinatra の影響も受けた静的ツール [Frank プロジェクト][Frank project] は, ランダムなテキストコンテンツとプレースホルダ画像を生成する素晴らしいヘルパセットです。 私たちはこのコードを Middleman に適合させました (MIT ライセンスを祝福します)。

例えば, ダミーテキストを 5 文出力したい場合, 次のように書けます。
`<%= lorem.sentences 5 %>`

その他のメソッドについてもテキストに関して利用できます:

``` ruby
lorem.sentence      # 1 文を返す
lorem.words 5       # 5 つの単語を返す
lorem.word
lorem.paragraphs 10 # 10 段落を返す
lorem.paragraph
lorem.date          # strftime 形式の引数を受取る
lorem.name
lorem.first_name
lorem.last_name
lorem.email
```

プレースホルダ画像の使用方法:

``` ruby
lorem.image('300x400')
  #=> http://placehold.it/300x400
lorem.image('300x400', :background_color => '333', :color => 'fff')
  #=> http://placehold.it/300x400/333/fff
lorem.image('300x400', :random_color => true)
  #=> http://placehold.it/300x400/f47av7/9fbc34d
lorem.image('300x400', :text => 'blah')
  #=> http://placehold.it/300x400&text=blah
```

## ページクラス

サイト階層に対応する `body` タグの class 属性が生成されると便利です。 `page_classes` はこれらの属性名を生成します。`projects/rockets/saturn-v.html` にページがあるとすると, レイアウトには次のように表示されます。

```erb
<body class="<%= page_classes %>">
```

結果:

```html
<body class="projects rockets saturn-v">
```

これにより簡単にページに project-specific や rocket-specific のスタイルを適用できます。

## カスタム定義ヘルパ

Middleman によって提供されるヘルパに加え, コントローラやビューの中からアクセスできる独自のヘルパやクラスを追加することができます。

ヘルパメソッドを定義するには, `config.rb` の中で `helpers` ブロックを使います:

``` ruby
helpers do
  def some_method
    # ...何らかの処理を追加...
  end
end
```

また, 外部の Ruby モジュール含むヘルパを作成し, 読み込むこともできます。 `lib` ディレクトリにファイルを配置します。例えば, `lib/custom_helpers.rb` という名前のファイルに上記のヘルパを抽出する場合には, モジュールを作ることができます:

``` ruby
module CustomHelpers
  def some_method
    # ...do something here...
  end
end
```

次に `config.rb` に追加:

``` ruby
require "lib/custom_helpers"
helpers CustomHelpers
```

より簡単な方法として, `helpers` ディレクトリにヘルパを配置し, モジュールファイルの名前をつける方法です (例: `CustomHelpers` は `helpers/custom_helpers.rb` として配置)。 Middleman は自動的にファイルを読込み, ヘルパーとして受け取ります。

[view the full documentation here]: http://www.padrinorb.com/guides/application-helpers
[Frank project]: https://github.com/blahed/frank
[`url` method]: http://rdoc.info/github/middleman/middleman/Middleman/Sitemap/Resource#url-instance_method
[`path` attribute]: http://rdoc.info/github/middleman/middleman/Middleman/Sitemap/Resource#path-instance_method
