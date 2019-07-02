---
title: テンプレートエンジンオプション
---

# テンプレートエンジンオプション

`config.rb` にテンプレートエンジンのオプションを設定することができます:

```ruby
set :haml, { :format => :html5 }
```

## Markdown

`config.rb` で一番好きな Markdown ライブラリを選び, オプションを設定することが
できます:

```ruby
set :markdown_engine, :redcarpet
set :markdown, :fenced_code_blocks => true, :smartypants => true
```

RedCarpet を使う場合, Middleman はヘルパを用いて `:relative_links` や
`:asset_hash` が行うようにリンクや画像タグを処理します。しかし,
デフォルトの Markdown エンジンはインストールが簡単なことから Kramdown に
なっています。

## その他のテンプレート言語

[Tilt] 対応のテンプレート言語と RubyGems のリストです。
動作させるにはインストール (`config.rb` で読み込む) しなければ
なりません。

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

  [Tilt]: https://github.com/rtomayko/tilt/
