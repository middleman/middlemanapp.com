---
title: 動的ページ
---

# 動的ページ

## Proxy の定義

Middleman にはテンプレートファイルと 1 対 1 の関係を持たないページを生成する機能があります。この機能が意味するのは, 変数に応じて複数のファイルを作り出す 1 テンプレートを使うことができるということです。Proxy を作るには, `config.rb` で `proxy` メソッドを使い, 作りたいページのパス, 使いたいテンプレートのパスを与えます(テンプレートファイル自体の拡張子は除く)。 次は `config.rb` の設定例の 1 つです:

``` ruby
# source/about/template.html.erb が存在することを想定
["tom", "dick", "harry"].each do |name|
  proxy "/about/#{name}.html", "/about/template.html", :locals => { :person_name => name }
end
```

プロジェクトがビルドされる際に, 4 つのファイルが出力されます:

* '/about/tom.html' (テンプレートの中の `person_name` は "tom" として)
* '/about/dick.html' (テンプレートの中の `person_name` は "dick" として)
* '/about/harry.html' (テンプレートの中の `person_name` は "harry" として)
* '/about/template.html' (テンプレートの中の `person_name` は nil になる)

ほとんどの場合, `person_name` 変数なしにテンプレートを出力したくないでしょう。 Middleman にこれを無視するように指定できます::

``` ruby
["tom", "dick", "harry"].each do |name|
  proxy "/about/#{name}.html", "/about/template.html", :locals => { :person_name => name }, :ignore => true
end
```

これで `about/tom.html`, `about/dick.html` や `about/harry.html` だけが出力されます。

## 無視するファイル

`config.rb` に `ignore` メソッドを追加することで, サイトビルド時に任意のパスを無視することも可能です。

``` ruby
ignore "/ignore-this-template.html"
```

正確なファイルパス, ファイル名のパターンマッチングや正規表現を ignore に与えることができます。
