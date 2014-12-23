---
title: アセットパイプライン
---

# アセットパイプライン

## 依存性管理

[Sprockets] は Javascript (と CoffeeScript) のライブラリを管理するためのツールで, 依存性を宣言し 3rd パーティのコードを読み込みます。Sprockets は .js や .coffee のファイルの中で,  `require` メソッドを使えるようにし, プロジェクトまたは 3rd パーティ製の gem から外部ファイルを取り込むことができます。

jQuery ライブラリを含む `jquery.js` ファイルとアプリケーションコードが含まれる `app.js` があるとします。 次のようにすることで app ファイルは動作する前に jquery を読み込むことができます:

``` javascript
//= require "jquery"

$(document).ready(function() {
  $(".item").pluginCode({
    param1: true,
    param2: "maybe"
  });
});
```

この機能は CSS ファイルの中でも動作します:

``` css
/*
 *= require base
 */

body {
  font-weight: bold;
}

```

Sass を使う場合, Sprockets のファイル読み込み方法よりも Sass の `@Import` を使用すべきです。

## 結合したアセットファイルのみデプロイ

`middleman build` コマンドを使った場合に `build` ディレクトリに結合した (1 つにまとめた) アセットファイルのみデプロイしたい場合, 結合対象のアセットファイル名にアンダースコアを使う必要があります。例えば, メインの `/source/javascripts/all.js` は次の依存関係で利用されます:

``` javascript
//= require "_jquery"
//= require "_my_lib_code"
//= require "_my_other_code"
```

そして `/source/javascripts/` ディレクトリには次のファイルが用意される必要があります: `_jquery.js`, `_my_lib_code.js`, `_my_other_code.js`。結果として `/build/javascripts/` ディレクトリには依存したコードを含む `all.js` だけがデプロイされます。

## アセット gem

`Gemfile` で読み込まれた gem からアセットを使用することができます:

```ruby
gem "bootstrap-sass", :require => false
```

`:require => false` はやや重要です。これらの多くの gem は Rails で使われるものと仮定されており, Rails や Compass 内部にフックしようとすると壊れます。gem を require することを回避し, Middleman は残り部分の面倒をみます。

一度これらの gem の依存関係を追加すると, gem から画像やフォントが自動的に読み込まれます。JavaScript や CSS はファイルの中で `require` や `@import` すると使うことができます。

アセットファイルとして追加せず, HTML から 直接 gem のスタイルシートや JS ファイルを参照したい場合, `config.rb` の中で明示的に読み込む必要があります。

```ruby
sprockets.import_asset 'jquery-mobile'
```

これで `script` タグや `javascript_include_tag` から直接参照することができます。

## Sprockets にパスを追加

`:js_dir` や `:css_dir` の他にもアセットディレクトリがある場合,
Sprockets のインポートパスを追加することができます。`config.rb` に次の内容を追加してください。

*注意* `#append_path` へのディレクトリの追加は 1 度だけだということに気をつけてください。
そうでなければ `middleman` のサイトマップで重複した入力を取得してしまうでしょう。
`# appended_paths` リストへの入力はディレクトリごとに 1 つの入力です。
重複した入力は長いビルド処理, アセットファイルをミニファイする際のコンフリクトなどの原因になります。

`config.rb` に次のように追加してください:

```ruby
# 文字列
sprockets.append_path '/my/shared/assets1/'

# Pathname もサポートします
sprockets.append_path Pathname.new('/my/shared/assets2/')
```

ディレクトリのリストを使って反復処理したい場合,
各ディレクトリが 1 度だけ追加される小さいコードスニペットを使うことができます:

```ruby
%w(path1 longer/path2 longer/path3).each do |path|
  next if sprockets.appended_paths.include? path

  sprockets.append_path path
end
```

Sprockets は Bower をサポートします。Bower のコンポーネントディレクトリのパスを直接追加します:

```ruby
sprockets.append_path 'bower_components'
```

bower で管理されたアセットファイル - 画像, フォントなど - を Middleman プロジェクトの中で利用するには,
`sprockets.import_asset` を使って bower 管理下のファイルをインポートする必要があります。
`jquery` が用意されている場合には次の書き方をします。`bower.json` の
[`main` セクション](https://github.com/bower/bower.json-spec) に
記載されているファイルを `config.rb` で次の構文を使ってインポートすることができます。

```ruby
sprockets.import_asset 'jquery'
```

特定のアセットファイルをインポートしたい場合には
`<コンポーネント名>/<アセットファイルへのパス>` のように相対パスを使う必要があります:

```ruby
sprockets.import_asset 'jquery/dist/jquery.js'
```

個別の出力パスを設定する必要がある場合には, `#import_asset` ブロックを
渡すことができます。このブロックは `Pathname` としてアセットファイルの論理パスを取得し
アセットファイルの出力パスを返します。

```ruby
sprockets.import_asset('jquery/dist/jquery.js') do |logical_path|
  Pathname.new('javascripts_new.d') + logical_path
  # => javascripts_new.d/jquery/dist/jquery.js
end
```

ブロックで中括弧を使う場合には `#import_asset` に括弧を使ってください。
そうでなければブロックに `#import_asset` ではない別のメソッドを渡してしまい
正しい出力パスが得られない場合があります。

```ruby
sprockets.import_asset('jquery/dist/jquery.js') { |logical_path| Pathname.new('javascripts_new.d') + logical_path }
```

この作業を少し自動化するには, [rake](https://github.com/jimweirich/rake) から
ファイルリストを利用することができます。この他に [hike](https://github.com/sstephenson/hike) を
使うこともできるでしょう。この方法では `sprockets.each_file` が使うことができません。
`config.rb` にある `sprockets` は 
[sprockets 環境のフェイク](https://github.com/middleman/middleman-sprockets/blob/master/lib/middleman-sprockets/config_only_environment.rb) なので
このメソッドを使うことができません。
注意してください。この動作を実現するために `gem "rake"` や `gem "hike"` を `Gemfile` に
追加する必要があるかもしれません。

```ruby
require 'rake/file_list'
require 'pathname'

bower_directory = 'vendor/assets/components'

# 検索パターンを用意
patterns = [
  '.png',  '.gif', '.jpg', '.jpeg', '.svg', # 画像
  '.eot',  '.otf', '.svc', '.woff', '.ttf', # フォント
  '.js',                                    # Javascript
].map { |e| File.join(bower_directory, "**", "*#{e}" ) }

# ファイルリストを作り, 不要ファイルを除外
Rake::FileList.new(*patterns) do |l|
  l.exclude(/src/)
  l.exclude(/test/)
  l.exclude(/demo/)
  l.exclude { |f| !File.file? f }
end.each do |f|
  # 相対パスをインポートする
  sprockets.import_asset(Pathname.new(f).relative_path_from(Pathname.new(bower_directory)))
end
```

## Helpers

`*.scss` ファイルの中で利用できるヘルパがあります:

* `image-path()`, `image-url()`
* `font-path()`, `font-url()`

これらのヘルパはアセットファイルへの正しいディレクトリ/ url をファイルパスとして追加します。例えば, `image-path('lightbox2/img/close.png')` が `images/lightbox2/img/close.png` になります。 bower 管理下のアセットファイルを参照するには `lightbox2`-component のファイルの 1 つである画像ファイル `lightbox2/img/close.png` を相対的な名前を指定する必要があります。

## Compass

Middleman は柔軟な [Compass] サポートを備えています。Compass は Sass でクロスブラウザなスタイルシートを書くためのパワフルなフレームワークです。Compass は, [Susy] のように, Middleman で使用できる拡張機能です。`image-url` のような Sprockets パスヘルパは Middleman のサイトマップにフックされるので, その他の拡張( :asset_hash のような) もスタイルシートに影響します。

[Sprockets]: https://github.com/sstephenson/sprockets
[Compass]: http://compass-style.org
[Susy]: http://susy.oddbird.net
