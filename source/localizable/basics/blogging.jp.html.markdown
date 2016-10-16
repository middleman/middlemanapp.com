---
title: ブログ機能
---

# ブログ機能

Middleman にはブログ, つまり記事投稿とタグ付けに対応した公式拡張があります。
`middleman-blog` は拡張機能の 1 つで, 使用するにはインストールする必要があります。
単に `Gemfile` で gem を指定するだけです:

``` ruby
gem "middleman-blog", "~> 4.0"
```

そして `config.rb` で拡張機能を有効化します:

``` ruby
activate :blog do |blog|
  # ブログ機能のオプションを設定
end
```

また, `middleman-blog` を一度インストールすれば
ブログ機能がセットアップされた新しいプロジェクトを作れるようになります:

``` bash
middleman init MY_BLOG_PROJECT --template=blog
```

すでに Middleman のプロジェクトがある場合,
サンプルの
[`index.html`](https://github.com/middleman/middleman-templates-blog/tree/master/template/source/index.html.erb),
[`tag.html`](https://github.com/middleman/middleman-templates-blog/tree/master/template/source/tag.html.erb),
[`calendar.html`](https://github.com/middleman/middleman-templates-blog/tree/master/template/source/calendar.html.erb)
や
[`feed.xml`](https://github.com/middleman/middleman-templates-blog/tree/master/template/source/feed.xml.builder)
を作る必要があるため, blog テンプレートオプションとともに `middleman init` を再実行するか,自分で作ってください。
[何が作られるのか](https://github.com/middleman/middleman-templates-blog/tree/master/template/source) は
GitHub で確認できます。

ブログ拡張にはたくさんの設定オプションがあります。プレビューサーバを起動し
`http://localhost:4567/__middleman/config/` にアクセスするとすべてのオプションが
確認できます。

## 記事

Middleman 自体がそうであるように, ブログ拡張は個別ファイルにフォーカスしています。
それぞれの記事はファイルごとに好きなテンプレート言語を使用できます。
記事のデフォルトのファイル名構造は `{year}-{month}-{day}-{title}.html` です。
新しい記事を作りたい場合, 正しいパスに配置し, 動作させるために
基本的な [Frontmatter](/jp/basics/frontmatter/) を記述します。
`config.rb` で `:blog` が有効化されている場合, どのファイル形式で Middleman が記事を探すのか変更する
`blog.sources` オプションを設定できます。

Middleman に関する新しい投稿を作るとしましょう。
`source/2011-10-18-middleman.html.markdown` を作ります。このファイルの最小限の内容は
Frontmatter に `title` を追加したものです:

``` html
---
title: Middleman ブログの投稿
---

Hello World
```

必要な場合, 同日の複数投稿に対応するために, Frontmatter に
`date` として日付と時刻を指定することができます。タグページを作るために
Frontmatter に `tags` リストを含めることもできます。

## 記事の生成

ショートカットとして, `middleman article TITLE` を実行できます。
Middleman は新しい記事を正しいファイル名で正しい場所に作ります。このコマンドには
`--date`, `--lang` や `--blog` オプションを渡すことができます。

独自のテンプレートを使いたい場合, `middleman article` コマンドで使われる
ERb テンプレートのパス (プロジェクトルートからの相対パス) を `blog.new_article_template` に
設定することができます。このテンプレートでは新しい記事を生成するための `@title`,
`@slug`, `@date` や `@lang` といったインスタンス変数を使うことができます。

### パスのカスタマイズと URL

ブログのデフォルトのパスは `/` (Web サイトのルート) ですが,
`config.rb` で上書きできます:

``` ruby
activate :blog do |blog|
  blog.prefix = "blog"
end
```

あらゆるリンク設定 (`permalink`, `sources`, `taglink`, `year_link`,
`month_link`, `day_link`) に `prefix` が追加されるので, 他の設定でこれを
繰り返す必要はありません。テンプレートの置かれた場所 (`calendar_template`,
`year_template`, `month_template`, `day_template`, や `tag_template`) には
`prefix` が追加 *されない* ので注意してください。テンプレートを記事と同じ場所に
配置したいのであれば, これらの設定に同じ prefix を追加してください。

投稿を閲覧のためのパーマリンクは次のように簡単に変更できます:

``` ruby
activate :blog do |blog|
  blog.permalink = "blog/{year}/{title}.html"
end
```

これであなたの記事は次の URL で閲覧できます: `blog/2011/blog.html`。 パーマリンクは
投稿が保存されているディレクトリからは完全に独立しています。
デフォルトのパーマリンクのパスは `{year}/{month}/{day}/{title}.html` です。 パーマリンクは
記事の日付要素 (`{year}`, `{month}`,` {date}`),
記事タイトル (URL に最適化するように変形された `{title}`) や
`{lang}` によって構成することができます。

また, 記事で使用される他の Frontmatter データを使うこともできます。
例えば, `category` が記事の Frontmatter に定義されている場合に
パーマリンクに含めることができます:

```html
---
title: Middleman のブログ投稿
date: 2013/10/13
category: HTML5
---

Hello World
```

``` ruby
activate :blog do |blog|
  blog.permalink = "blog/{category}/{title}.html"
end
```

上記記事の URL は `blog/html5/my-middleman-blog-post.html` になります。

`blog.sources` に指定した URL の定義要素は
`blog.permalinks` で使うことができます。例えば次のような設定です:

``` ruby
activate :blog do |blog|
  blog.sources = "{category}/{year}-{month}-{day}-{title}.html"
  blog.permalink = "{category}/{year}/{month}/{day}/{title}.html"
end
```

`cats/2013-11-12-best-cats.html` に記事のソースを置くことができ,
Frontmatter で `category` を指定しなくても
`cats/2013/11/12/best-cats.html` に書き出されます。
`current_article.metadata[:page]['category']` を介して
ソースパスから抽出されたカテゴリにアクセスすることもできます。

### きれいな URL (ディレクトリインデックス)

ブログ記事を HTML ファイルとしてではなくディレクトリとして
表示させたい場合には,[きれいな URL](/jp/basics/pretty-urls/) 機能を
有効化することも検討しましょう。

**Note:** `directory_indexes` を有効化する場合, ブログ拡張を有効化した *後に* 設定するように
してください。

## レイアウト

`config.rb` ですべての記事に使われる [レイアウト](/jp/basics/layouts/) を
指定することができます。

``` ruby
activate :blog do |blog|
  blog.layout = "blog_layout"
end
```

レイアウトに挿入する前に記事ごとにちょっとした構造で囲みたい場合,
記事レイアウトを構成してからメインのレイアウトで囲むために,
Middleman の [入れ子レイアウト](/jp/basics/layouts/#入れ子レイアウト) 機能を
使うことができます。

## 記事一覧

ブログの記事一覧にはテンプレートから
[`blog.articles`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogData#articles-instance_method)
でアクセスでき,
[`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle) のリストを取得できます。

[`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle)
ごとに便利なメソッドを持っています。
同時にさらに多くの情報
([Frontmatter](/jp/basics/frontmatter) の
[`data`](http://rubydoc.info/gems/middleman-core/Middleman/CoreExtensions/FrontMatter/ResourceInstanceMethods#data-instance_method)
のような) をもつ [サイトマップ](/jp/advanced/sitemap) の
[`Resource`](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Resource) でもあります。
レイアウトやその記事の中からは `current_article` を介して
現在の記事を取得することができます。

例えば, 最新 5 件の記事と要約の一覧を表示する例です:

``` html
<% blog.articles[0...5].each do |article| %>
  <article>
    <h1>
      <a href="<%= article.url %>"><%= article.title %></a>
      <time><%= article.date.strftime('%b %e %Y') %></time>
    </h1>

    <%= article.summary %>

    <a href="<%= article.url %>">もっと読む</a>
  </article>
<% end %>
```

タグアーカイブのタグデータへアクセスすることもできます:

``` html
<ul>
  <% blog.tags.each do |tag, articles| %>
    <li>
      <h5><%= tag %></h5>
      <ul>
        <% articles.each do |article| %>
          <li><a href="<%= article.url %>"><%= article.title %></a></li>
        <% end %>
      </ul>
    </li>
  <% end %>
</ul>
```

同様にカレンダー形式の一覧もできます:

``` html
<ul>
  <% blog.articles.group_by {|a| a.date.year }.each do |year, articles| %>
    <li>
      <h5><%= year %></h5>
      <ul>
        <% articles.each do |article| %>
          <li><a href="<%= article.url %>"><%= article.title %></a></li>
        <% end %>
      </ul>
    </li>
  <% end %>
</ul>
```

Frontmatter に `public` フラグを追加している場合:

``` html
<h1>Public Articles</h1>
<% blog.articles.select {|a| a.data[:public] }.each do |article| %>
  ...
<% end %>
```

## ヘルパ

テンプレートを簡単に作るために使用できる
[いくつかのヘルパ](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers)
があります。これらは現在ページの記事を取得, 現在ページをブログ記事か判定,
タグやカレンダーページへのパスを作成といったことができます。

## タグ

タグを使った記事の整理なしに何がブログ機能でしょうか?
単に記事の [Frontmatter](/jp/basics/frontmatter/) に `tag` を
追加するだけです。
[`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle)
の
[`tag`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle#tags-instance_method)
メソッドを使うことでタグを呼び出すことができます。さらに,
[`blog.tags`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogData#tags-instance_method)
から記事に関連するタグリストを取得できます。`config.rb` で `blog.tag_template` に
テンプレートを設定した場合
([デフォルトの config.rb](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/config.tt) 参照),
タグごとにページをレンダリングできます。タグテンプレートはローカル変数を持ちます。
現在のタグがセットされた `tagname` とそのタグの記事リストがセットされた `articles`です。
また特定のタグページへのリンクを作るために
[`tag_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#tag_path-instance_method)
ヘルパを使うことができます。

デフォルトテンプレートでは [`tag.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/tag.html.erb)
を作り, タグごとにページを `tags/{tag}.html` を作ります。
上記の例にいくつかのタグを追加すると次のようになります:

``` html
---
title: Middleman のブログ投稿
date: 2011/10/18
tags: blogging, middleman, hello, world
---

Hello World
```

これで `tags/blogging.html` で表示された記事を確認できます。

パスは `config.rb` で変更できます:

``` ruby
activate :blog do |blog|
  blog.taglink = "categories/{tag}.html"
end
```

これで `categories/blogging.html` で記事一覧を確認できます。

## カレンダーページ

多くのブログエンジンは年月日ごとの全記事を載せたページを
作ります。Middleman は
[`calendar.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/calendar.html.erb)
テンプレートと `blog.calendar_template` 設定を使ってこれを実現します。
デフォルトのテンプレートは
[`calendar.html`](https://github.com/middleman/middleman-blog/blob/master/lib/middleman-blog/template/source/calendar.html.erb)
を作ります。このテンプレートでは `year`, `month` と `day` 変数が設定され,
その日付の記事一覧を出力します。

特定形式のカレンダーページにしたい場合 (例えば年別で日別は不要) や
カレンダーページの種類ごとに異なるテンプレートを使いたい場合,
`blog.year_template`, `blog.month_template` や `blog.day_template` を
個別に設定できます。`blog.calendar_template` の設定は
これらすべてを設定するショートカットです。

テンプレート内で, カレンダーページへのリンクを作るために,
[`blog_year_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_year_path-instance_method),
[`blog_month_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_month_path-instance_method)
や
[`blog_day_path`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/Helpers#blog_day_path-instance_method)
ヘルパを使うことができます。
`blog.year_link`, `blog.month_link` や `blog.day_link` の設定で
これらのリンクがどう表示されるのかカスタマイズできます。
デフォルトでは, カレンダーページは年月日ごとに `/2012.html`, `/2012/03.html` や `/2012/03/15.html` の
ように表示されます。


## 要約

例えば、トップページのように記事ページへのリンクをともなった記事の要約を表示できるよう,
Middleman は記事を切り取る機能を提供しています。
ブログ拡張は記事の中から `READMORE` という区切り文字列を探し,
トップページにはこの区切り文字列の前までの内容を表示します。
記事ページでは `READMORE` は取り除かれます。

ブログ拡張が検索し切り取る区切り文字列は
`config.rb` で設定できます:

``` ruby
activate :blog do |blog|
  blog.summary_separator = /SPLIT_SUMMARY_BEFORE_THIS/
end
```

次に, トップページのテンプレート (または要約を表示したい場所) に
次の行を追加することで記事ページヘのリンクをともなった要約を
表示することができます:

``` erb
<%= article.summary %>
<%= link_to 'もっと読む…', article %>
```

`READMORE` (または設定したテキスト) が削除された状態で
記事へリンクします。

[`BlogArticle`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle) の
[`summary`](http://rubydoc.info/github/middleman/middleman-blog/master/Middleman/Blog/BlogArticle#summary-instance_method)
属性からテンプレートの中で要約を使うことができます。

`summary` は要約を切り出す長さやテキストが切り捨てられた際に表示する
文字列を与えられたメソッドです:

```erb
<%= article.summary(250, '>>') %>
```

250 文字以下の要約と続く ">>" が出力されます。
デフォルトの要約の長さは 250 文字です。無効化したい場合には
`blog.summary_length` に `nil` を設定してください。

HTML 対応の要約の提供には, 要約を使うために `gem 'nokogiri'` を `Gemfile` に
追加しなければならないので注意してください。もし `summary_separator` (READMORE) 機能を使い,
オプションの length パラメータを *使わない* 場合には, この gem は不要です。

もし要約を生成する独自のメソッドがある場合,
`blog.summary_generator` に `Proc` をセットできます。レンダリングされたブログの記事,
切り取りたい長さ、および省略文字列を受け取り, 要約を生成します。

## ページネーション

長い記事の一覧は複数ページに分割できます。テンプレートは
次の設定がされているとページ分割されます。

```html
---
pageable: true
---
```

Frontmatter での有効化に加え, ページネーションは `config.rb` の中で有効化できます:

```ruby
activate :blog do |blog|
  blog.paginate = true
end
```

デフォルト設定では 2 ページ目以降は `page/{num}` (`2011/page/2.html` のような)
リンクになります。これはページあたりの記事数とともに
`config.rb` でカスタマイズ可能です。例えば:

```ruby
activate :blog do |blog|
  blog.paginate = true
  blog.page_link = "p{num}"
  blog.per_page = 20
end
```

上記の設定は, ページあたり 20 記事でリンクは `/2012/p2.html` のような
結果になります。`per_page` パラメータは
Frontmatter でテンプレートごとに設定できます。

ページネーション対応のテンプレートでは次の変数を設定することができます:

```ruby
paginate       # ページネーションを有効化する場合には true を設定
per_page       # ページあたりの記事数

page_articles  # 記事一覧をこのページで表示する
articles       # テンプレートのための完全な記事一覧

page_number    # ページ番号
num_pages      # 総ページ数。次のように page_numer と一緒に
               # "Page X of Y" を表示する

page_start     # このページの最初の記事の番号
page_end       # このページの最後の記事の番号
               # article.length を使って "Articles X to Y of Z" を表示"

next_page      # シーケンス内の次と前のページのためのリソース
prev_page      # 隣接するページが存在しない場合 nil
               # このページと他のページすべてを含む
```

`paginate` が false で `per_page` がテンプレートの Frontmatter に設定されている場合,
`page_articles` 変数は `article` の最初の `per_page` 分のアイテムが
設定されます。これが有効になっていると改ページせずに使用できる
テンプレートを作ることができます。

## 下書き

Frontmatter で下書きを指定できます:

``` html
---
title: 作成中
published: false
---

公開されない下書き
```

下書きは development モードの場合のみ出力します。

未来の日付の記事も未公開になります。
定期的にサイトをビルドするように `cron` ジョブを使うことで,
指定した時刻に自動的に記事が公開されます。
この挙動は `publish_future_dated` を `true` に設定することで無効化できます。

## タイムゾーン

RSS フィードの正確な公開時刻の取得や,
自動で正確なスケジュールで記事を公開するために, ブログのタイムゾーンを
`config.rb` で設定できます:

``` ruby
Time.zone = "Tokyo"
```

## カスタム記事コレクション

Middleman ブログは [Frontmatter](/jp/basics/frontmatter/) に定義した
データによって記事をグループ分けする機能に対応しています。
次の一般的な例では *category* 属性を使って記事をグループ分けします。

```html
---
title: Middleman のブログ投稿
date: 2013/10/13
category: HTML5
---

Hello World
```

HTML5 のカテゴリに属するすべての記事を表示する `categories/html5.html` を
生成するように Middleman ブログを設定できます。次の設定例を参照してください:

```ruby
activate :blog do |blog|
  blog.custom_collections = {
    category: {
      link: '/categories/{category}.html',
      template: '/category.html'
    }
  }
end
```

category 属性に基づいてコレクションを設定します。
ビルドする際のカスタムページの URL 構造や使用するテンプレートを指定することができます。
カスタムコレクションを生成する場合, コレクションページにアクセスする新しいヘルパ (この例では
`category_path`) が生成されます。これにより
`category_path('html5')` を呼び出すことができ `categories/html5.html` という URL を返します。
テンプレートは自動的に現在のコレクション (この例では `category`) を
ローカル変数として取得します。

## 記事のサブディレクトリ

ブログ記事に紐付いた拡張子なしのサブディレクトリは, ビルド時に
正しい場所へ複製されたファイルが入っています。
例えば, 次のディレクトリ構造です:

```
source/2011-10-18-middleman.html.markdown
source/2011-10-18-middleman/photo.jpg
source/2011-10-18-middleman/source_code.rb
```

この出力 ([`directory_indexes`](/jp/basics/pretty-urls/) が有効化された場合) は次のようになります:

```
build/2011/10/18/middleman/index.html
build/2011/10/18/middleman/photo.jpg
build/2011/10/18/middleman/source_code.rb
```

単一のブログ記事に属しているファイル (例えば画像) は
source 内で一緒に保管し出力することができます。
ブログ構造に依存し, 記事の中で相対リンクの使用を可能にしますが,
記事の内容がサイトの他の部分, 例えばカレンダーやタグページ,
で使われる場合には注意が必要です。

ブログ記事から記事のサブディレクトリにあるものにリンクしたい場合,
ディレクトリ名を含むことに注意してください:

```markdown
Wrong: [私の写真][photo.jpg]
Right: [私の写真][2011-10-18-middleman/photo.jpg]
```

この方法で動作するはずですが, `:asset_hash` のようなその他の Middleman の機能では動作しません。
詳細については [この issue](https://github.com/middleman/middleman/issues/818) を確認してください。

## ロケール指定記事と特定言語の表示

ブログ機能はロケールに対応しています。まず, `:blog` 拡張を有効化する *前に*
[`:i18n`](/jp/advanced/localization/) 拡張を有効化します。
これであなたの記事はロケールに対応します。つまり記事の中で
`t()` のようなヘルパが使えるということです。

記事は特定の言語を Frontmatter で指定するか,
`blog.sources` の `{lang}` 変数を使ったパスを介してもつことができます。
例えば, `blog.sources` が `{lang}/{year}-{title}.html` と設定され, 記事が
`source/de/2013-willkommen.html.markdown` だった場合, その記事の言語は
`de` になります。`{lang}` パス変数は `blog.permalink` パスとして
使うことができます。

`current_article.lang` を使ってその言語の記事にアクセスできます。
さらに `blog.local_articles` を使うことでテンプレートにロケールにあった記事一覧を
取得することができます。

新しい記事を作る場合, `middleman article --lang <locale> TITLE` とすることで
ロケールを指定した記事を生成できます。

## 複数ブログ

Middleman は 1 つのサイトの中で複数のブログを設置できます。1 つ以上のブログを作るには,
単に `:blog` 拡張を複数回有効化するだけです:

```ruby
activate :blog do |blog|
  blog.name = "cats"
  blog.prefix = "cats"
end

activate :blog do |blog|
  blog.name = "dogs"
  blog.prefix = "dogs"
end
```

この設定は `/cats` と `/dogs` 2 つのブログを作ります。
ブログの名前を指定することに注意してください。
`blog()` のようなヘルパがあるためです (ブログ名を知らせることで, `blog` ヘルパに `blog('dogs')` のように
 ブログ名を与えることができます)。
多くの場合には Middleman は自動的にヘルパの対象になるブログを探しますが,
`blog`, `tag_path` などのヘルパを使う場合にはブログごとに名前を指定する必要があります。
ページ (タグページテンプレートのような) の Frontmatter でヘルパがどのブログを使うかブログ名で
指定することもできます。

それ以外は, ブログは通常通りで完全に独立して
構成することができます。
