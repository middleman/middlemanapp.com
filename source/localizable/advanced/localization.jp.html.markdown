---
title: 多言語化 (i18n)
---

# 多言語化 (i18n)

`:i18n` 拡張はサイトに多言語化対応機能を提供します。`config.rb` で次のように有効化します:

``` ruby
activate :i18n
```

デフォルト設定では, この拡張機能は対応したい言語のロケール名を表す名前の YAML ファイルをプロジェクトルートの `locales` フォルダから探します。 YAML ファイルはサイトの中で多言語化する必要がある文字列ごとのキーと値のセットです。テンプレートで文字列を表示するため参照される キーは, その値は言語ごとに異なるでしょうが, ロケールごとに同じ内容を書かなければなりません。 2 つの YAML ファイルの例です:

`locales/en.yml`:

``` yaml
---
en:
  hello: "Hello"
```

`locales/es.yml`:

``` yaml
---
es:
  hello: "Hola"
```

多言語化するテンプレートは, デフォルト設定では `source/localizable` フォルダの中に置きます (このオプションの変更方法はページ下部で) 。このフォルダにあるテンプレートごとに `I18n` ヘルパにアクセスします。このヘルパを使うと,  YAML ファイルからキーを参照し, 言語固有の値をテンプレートに差し込みます。簡単な `source/localizable/hello_world.html.erb` テンプレートの例です:

``` html
    <%= I18n.t(:hello) %> World
```

この場合 2 つのファイルとして出力されます:

* /hello_world.html の内容は: "Hello World"
* /es/hello_world.html の内容は: "Hola World"

テンプレートで `I18n.t` のショートカットとして `t` を使うこともできます:

``` html
    <%= t(:hello) %> World
```


## 多言語化された場合のパス

それぞれ個別の言語表示にはその言語の名前空間のパスでアクセスできます。デフォルト設定では, 第一言語はサイトのルートに置かれます (このオプションの変更方法はページ下部で) 。デフォルトのパス設定ではパスの中で単純に言語名 ( YAML ファイル名) を使用します:

* /en/index.html
* /es/index.html
* /fr/index.html

`:path` オプションで変更できますが, URL は YAML ファイルの名前を含むことを覚えておいてください:

``` ruby
activate :i18n, :path => "/langs/:locale/"
```

パスは次のようになります:

* /langs/en/index.html
* /langs/es/index.html
* /langs/fr/index.html

パスの一部に YAML ファイル名 を使いたくない場合, 違う値で書き換えることができます。

``` ruby
activate :i18n, :path => "/langs/:locale/",
  :lang_map => { :en => :english, :es => :spanish, :fr => :french }
```

パスは次のようになります:

* /langs/english/index.html
* /langs/spanish/index.html
* /langs/french/index.html

## パスの多言語化

ページの内容に加えファイル名も多言語化したい場合があります。言語固有の URL 書き換えを行う場合 YAML ファイルの中で `paths` キーを使うことで対応できます。

`source/localizable/hello.html.erb` があるとします。デフォルト設定では, 次のように出力されます:

* /hello.html
* /es/hello.html

スペイン語の場合に限りファイル名を `hola.html` に書き換えたい場合, `locales/es.yml` の中で `paths` キーを使うことができます:

``` yaml
---
es:
  hello: "Hola"
  paths:
    hello: "hola"
```

次のように出力されます:

* /hello.html
* /es/hola.html

## 多言語化対象のテンプレート

デフォルト設定では, `source/localizable` の中身が複数の言語でビルドされ, その他のテンプレートはそのままビルドされます。このフォルダの名前を `:templates_dir` オプションで変更することができます:

``` ruby
# `source/language_specific` を探す
activate :i18n, :templates_dir => "language_specific"
```

## 多言語化対象の指定

`locales/` フォルダのファイルを自動で探し出すより, 対応する言語のリストを指定したい場合, `:langs` オプションを指定できます:

``` ruby
activate :i18n, :langs => [:en] # :en 以外のすべての言語を無視
```

## デフォルト (ルート) 言語

デフォルト設定では, 第一言語 ( `:langs` で指定されるか, `locales/` フォルダにあるもの) が "標準の" 言語になり, サイトのルートに置かれます。 2 つの言語が与えられた場合, `:en` で多言語化されるファイルがルートに置かれます:

* source/localizable/index.html.erb
  * build/index.html は英語
  * build/es/index.html はスペイン語

`:mount_at_root` を使うことで, この設定を変更したり特定言語のルート指定を無効化できます:

``` ruby
activate :i18n, :mount_at_root => :es # スペイン語をルートに設定
# または
activate :i18n, :mount_at_root => false # すべての言語ファイル URL に prefix がつく
```

## 完全に多言語化されたテンプレート

ロケールごとの YAML ファイルに大きなテキストブロックを書くのは非効率です。この問題に対応するため, Middleman にはテンプレート全体を多言語化する方法があります。例えば, `index.html` を作りたいとして, `index.en.html.erb` と `index.es.html.erb` 2 つのテンプレートを作ることができます。サイトがビルドされると, 次のように出力されます:

* build/index.html は英語
* build/es/index.html はスペイン語

この多言語化の方法を使う場合, `localizable` フォルダの中にファイルを置くようにしてください。
