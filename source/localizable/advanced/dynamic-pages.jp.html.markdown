---
title: 動的ページ
---

# 動的ページ

## Proxy の定義

Middleman にはテンプレートファイルと 1 対 1 の関係を持たない
ページを生成する機能があります。この機能によって, 変数に応じて
複数のファイルを作り出す 1 テンプレートを使うことができるようになります。
Proxy を作るには, `config.rb` で `proxy` メソッドを使い, 作りたいページのパス,
使いたいテンプレートのパスを与えます(テンプレートファイル自体の拡張子は除く)。
次の例は `config.rb` の設定例の 1 つです:

```ruby
# source/about/template.html.erb が存在することを想定
["tom", "dick", "harry"].each do |name|
  proxy "/about/#{name}.html", "/about/template.html", :locals => { :person_name => name }
end
```

プロジェクトがビルドされる際に, 4 つのファイルが出力されることになります:

* '/about/tom.html' (テンプレート中の `person_name` は "tom")
* '/about/dick.html' (テンプレート中の `person_name` は "dick")
* '/about/harry.html' (テンプレート中の `person_name` は "harry")
* '/about/template.html' (テンプレート中の `person_name` は nil)

ほとんどの場合, `person_name` 変数なしにテンプレートを出力したくはないでしょう。
Middleman に `person_name` 変数なしを無視するように指定できます::

```ruby
["tom", "dick", "harry"].each do |name|
  proxy "/about/#{name}.html", "/about/template.html", :locals => { :person_name => name }, :ignore => true
end
```

これで `about/tom.html`, `about/dick.html` や `about/harry.html` だけが
出力されるようになります。

## 無視するファイル

`config.rb` に `ignore` メソッドを追加することで,
ビルド時に任意のパスを無視することも可能です。

```ruby
ignore "/ignore-this-template.html"
```

正確なファイルパス, ファイル名のパターンマッチや正規表現を ignore で指定することができます。

## きれいな URL (ディレクトリインデックス)

動的ページと [ディレクトリインデックス][Directory Indexes] を併用するには
`/index.html` が続いたプロキシパスを指定します。

次の例では, プロキシパスは `/about/#{name}/index.html`
になります:

```ruby
["tom", "dick", "harry"].each do |name|
  proxy "/about/#{name}/index.html", "/about/template.html", :locals => { :person_name => name }, :ignore => true
end
```

このプロジェクトがビルドされると, 動的ページによって 3 ファイルが生成されます:

* `/about/tom/index.html` (テンプレート中の `person_name` は "tom")
* `/about/dick/index.html` (テンプレート中の `person_name` は "dick")
* `/about/harry/index.html` (テンプレート中の `person_name` は "harry")
 
  [Directory Indexes]: /jp/advanced/pretty-urls/
