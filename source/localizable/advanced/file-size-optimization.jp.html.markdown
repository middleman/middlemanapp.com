---
title: ファイルサイズ最適化
---

# ファイルサイズ最適化

## CSS と JavaScript の圧縮

Middleman は CSS のミニファイや JavaScript の圧縮処理を行うので, ファイル最適化について心配する必要はありません。ほとんどのライブラリはデプロイを行うユーザのためにミニファイや圧縮されたバージョンを用意していますが, そのファイルは読めなかったり編集できなかったりします。 Middleman はプロジェクトの中にコメント付きのオリジナルファイルを取っておくので, 必要に応じて読んだり編集することができます。そして, プロジェクトのビルド時には Middleman は最適化処理を行います。

`config.rb` で, サイトのビルド時に `minify_css` 機能と `minify_javascript` 機能を有効化します。

``` ruby
configure :build do
  activate :minify_css
  activate :minify_javascript
end
```

ファイル名に `.min` を含む圧縮されたファイルを使っている場合, Middleman はそのファイルを最適化しません。作者によって事前に配慮された圧縮をおこなう jQuery のようなライブラリにはとても良い方法です。

`config.rb` で `:minify_javascript` 拡張を有効化する場合, `:compressor` オプションに Uglifier のカスタムインスタンスを設定することで JavaScript の圧縮方法をカスタマイズできます。詳細は [Uglifier's docs](https://github.com/lautis/uglifier) を参照してください。例えば, 次のように危険な最適化やトップレベル変数名のシンボル化を有効化できます:

``` ruby
set :js_compressor, Uglifier.new(:toplevel => true, :unsafe => true)
```

`asset_hash` を有効にし, ロードバランサを使って複数サーバにサイトを構築, JavaScript の圧縮を行う場合には, mangle オプションが無効に指定されていることを確認してください。mangle オプションが有効な場合, Uglifier はサーバマシン毎に異なるバージョンの圧縮した JavaScript ファイルを作ります。そのファイルは異なるハッシュを含むファイル名で HTML の中の参照もバージョン毎に異なります。次のように設定します:

``` ruby
set :js_compressor, Uglifier.new(:mangle => false)
```

一部のファイルをミニファイ処理から除外したい場合, これらの拡張を有効化する際に `:ignore` オプションを渡し, 無視するファイルを識別する 1 つ以上のパターンマッチ, 正規表現や Proc を与えます。同じように, ファイル拡張子をリネームし変更するために `:exts` オプションを渡すこともできます。

`Gemfile` に次の gem を追加することで, JavaScript の圧縮(さらには CoffeeScript のビルド) を高速化できます。

```ruby
gem 'therubyracer' # 高速な JS コンパイラ
gem 'oj' # 高速な JS コンパイラ
```

## テキストファイルの GZIP 化

対応するユーザエージェントに [圧縮ファイルを配信する](http://developer.yahoo.com/performance/rules.html#gzip) のはいい考えです。多くの web サーバはオンザフライでファイルを gzip にする機能を持っていますが, ファイルが配信される度に CPU を使う必要があり, その結果としてほとんどのサーバでは最大圧縮が実行されません。 Middleman は通常のファイルと一緒に gzip バージョンの HTML, CSS や JavaScript を作ることができ,  Web サーバに GZIP ファイルを直接配信するように命じることができます。まず,  `:gzip` 拡張を有効化します:

``` ruby
activate :gzip
```

そして, GZIP ファイルを配信するようにサーバを設定します。 Nginx を使用する場合, [gzip_static](http://wiki.nginx.org/NginxHttpGzipStaticModule) モジュールを確認してください。 Apache の場合, 少しトリッキーなことをしなければなりません - 例として [Gist](https://gist.github.com/2200790) を確認してください。

## 画像圧縮

ビルド時に画像も圧縮したい場合, [`middleman-imageoptim`](https://github.com/plasticine/middleman-imageoptim) を試してみましょう。

## HTML 圧縮

Middleman は HTML 出力を圧縮する公式の拡張機能を提供しています。gem で簡単にインストールします:

``` bash
gem install middleman-minify-html
```

Gemfile に `middleman-minify-html` を追加します:

``` ruby
gem "middleman-minify-html"
```

さらに `config.rb` を開いて次を追加します:

``` ruby
activate :minify_html
```

ソースを確認すると HTML が圧縮されていることがわかります。
