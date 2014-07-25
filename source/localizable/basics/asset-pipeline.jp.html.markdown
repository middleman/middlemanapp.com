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

`middleman build` コマンドを使った場合に `build` ディレクトリに結合した (1 つにまとめた) アセットファイルのみデプロイしたい場合, 結合対象のアセットファイル名にアンダースコアを使う必要があります。例えば, メインの `/source/javascripts/all.js` は次の依存関係で利用されるます:

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

`:js_dir` や `:css_dir` の他にもアセットディレクトリがある場合, Sprockets のインポートパスを追加することができます。`config.rb` に次の内容を追加してください:

```ruby
sprockets.append_path '/my/shared/assets/'
```

Sprockets supports Bower, so you can add your Bower components path directly:

```ruby
sprockets.append_path 'bower_components'
```

## Compass

Middleman は柔軟な [Compass] サポートを備えています。Compass は Sass でクロスブラウザなスタイルシートを書くためのパワフルなフレームワークです。Compass は, [Susy] のように, Middleman で使用できる拡張機能です。`image-url` のような Sprockets パスヘルパは Middleman のサイトマップにフックされるので, その他の拡張( :asset_hash のような) もスタイルシートに影響します。

[Sprockets]: https://github.com/sstephenson/sprockets
[Compass]: http://compass-style.org
[Susy]: http://susy.oddbird.net
