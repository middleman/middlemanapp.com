---
title: ファイルサイズ最適化
---

# ファイルサイズ最適化

## CSS と JavaScript の圧縮

Middleman は CSS のミニファイや JavaScript の圧縮処理を行うので, ファイル最適化
について心配することはありません。ほとんどのライブラリはデプロイを行うユーザの
ためにミニファイや圧縮されたバージョンを用意していますが, そのファイルは
読めず編集できなかったりします。Middleman はプロジェクトの中にコメント付きの
オリジナルファイルを取っておくので, 必要に応じて読んだり編集することができます。
そして, プロジェクトのビルド時には Middleman は最適化処理を行います。

`config.rb` で, サイトのビルド時に `minify_css` 機能と `minify_javascript` 機能を
有効化します。

```ruby
configure :build do
  activate :minify_css
  activate :minify_javascript
end
```

ファイル名に `.min` を含む圧縮されたファイルを使っている場合,
Middleman はそのファイルを最適化しません。作者によって事前に配慮された
圧縮をおこなう jQuery のようなライブラリにはとても良い方法です。

`config.rb` で `:minify_javascript` の
`:compressor` オプションに Uglifier のカスタムインスタンスを設定することで
JavaScript の圧縮方法をカスタマイズできます。詳細は [Uglifier's docs]
を参照してください。

例えば, 次のように
危険な最適化やトップレベル変数名のシンボル化を有効化できます:

```ruby
require "uglifier"
activate :minify_javascript,
  compressor: proc {
    ::Uglifier.new(:mangle => {:toplevel => true}, :compress => {:unsafe => true})
  }
```

`asset_hash` を有効にし, ロードバランサを使って複数サーバにサイトを構築,
JavaScript の圧縮を行う場合には, mangle オプションが無効に指定されていることを
確認してください。mangle オプションが有効な場合, Uglifier はサーバマシン毎に
異なるバージョンの圧縮した JavaScript ファイルを作ります。
そのファイルは異なるハッシュを含むファイル名で HTML の中の参照も
バージョン毎に異なります。次のように設定します:

```ruby
require "uglifier"
activate :minify_javascript, compressor: -> { Uglifier.new(:mangle => false) }
```

特定のファイルをミニファイ処理から除外したい場合, これらの拡張を有効化する際に
`:ignore` オプションを渡し, 無視するファイルを識別する 1 つ以上のパターンマッチ,
正規表現や Proc を与えます。同じように, ファイル拡張子をリネームし変更するために
`:exts` オプションを渡すこともできます。

`Gemfile` に次の gem を追加することで, JavaScript の圧縮 (さらには CoffeeScript
のビルド) を高速化できます。

```ruby
gem 'therubyracer' # より高速な JS コンパイル
gem 'oj' # より高速な JSON パーサとオブジェクトシリアライザ
```

## テキストファイルの gzip 圧縮

対応するユーザエージェントに対して [圧縮ファイルを配信する][serve compressed files]
のはより良い方法です。多くの web サーバはオンザフライでファイルを gzip にする
機能を持っていますが, ファイルが配信される度に CPU を使う必要があり,
その結果としてほとんどのサーバでは最大圧縮が実行されません。 Middleman は通常の
ファイルと一緒に gzip バージョンの HTML, CSS や JavaScript を作ることができ,
Web サーバに GZIP ファイルを直接配信するように命じることができます。
まず, `:gzip` 拡張を有効化します:

```ruby
activate :gzip
```

そして, GZIP ファイルを配信するようにサーバを設定します。 Nginx を使用する場合,
[gzip_static] モジュールを確認してください。Apache の場合, 少しトリッキーな
ことをしなければなりません。例として [Gist][apache_gist] を確認してください。

## 画像圧縮

ビルド時に画像も圧縮したい場合, [`middleman-imageoptim`] を試してみましょう。

## HTML 圧縮

Middleman は HTML 出力を圧縮する公式の拡張機能を提供しています。
`Gemfile` に `middleman-minify-html` を追加します:

```ruby
gem "middleman-minify-html"
```

`bundle install` を実行し, `config.rb` を開いて次を追加します:

```ruby
activate :minify_html
```

ソースを確認すると HTML が圧縮されていることがわかります。

## `srcset` を使ったレスポンシブ画像

HTML に最近追加されたものに `img` や `picture` タグの
`srcset` 属性があります。この属性は viewport (`1024w, 800w, 600w, や 320w`
のような幅指定) やブラウザの解像度 (`1x, 2x, 3x, ...` のような値) によって
異なる画像を異なるサイズでブラウザに
ロードできます。

```html
<img src="img/100px.jpg" srcset="img/300px.jpg 3x, img/200px.jpg 2x, img/100px.jpg 1x">
<img src="img/100px.jpg" srcset="img/300px.jpg 300w, img/200px.jpg 200w, img/100px.jpg 100w">
```

`:asset_hash` オプションと一緒に `secset` を使用したい場合, `image_path`
ヘルパーを使用する必要があります。

```erb
<img src="<%= image_path('100px.jpg') %>" srcset="<%= image_path('300px.jpg') %> 3x, <%= image_path('200px.jpg') %> 2x, <%= image_path('100px.jpg') %> 1x">
```

[Can I Use] で確認できるように `srcset` 属性はまだすべてのブラウザでサポートされていません。
`srcset` をサポートしないブラウザの場合フォールバックとして `src` 属性を使います。
注意するポイントとして, 一部のブラウザではフォールバックとして `srcset` 属性の
最初の値を使用します。上記の例で属性値の最初に最大の画像を入れているのはこのためです。

  [Uglifier's docs]: https://github.com/lautis/uglifier
  [serve compressed files]: https://developer.yahoo.com/performance/rules.html#gzip
  [gzip_static]: https://www.nginx.com/resources/wiki/extending/compiling/
  [apache_gist]: https://gist.github.com/2200790
  [middleman-imageoptim]: https://github.com/plasticine/middleman-imageoptim
  [Can I Use]: http://caniuse.com/
