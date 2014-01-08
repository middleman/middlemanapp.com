---
title: Frontmatter
---

# Frontmatter

Frontmatter は YAML または JSON フォーマットでテンプレート上部に記述することができるページ固有の変数です。

## YAML フォーマット

簡単な ERb テンプレートに, 固有のページのレイアウトを変更する Frontmatter を追加します。

``` html
---
layout: "custom"
my_list:
  - one
  - two
  - three
---

<h1>リスト</h1>
<ol>
  <% current_page.data.my_list.each do |f| %>
  <li><%= f %></li>
  <% end %>
</ol>
```

Frontmatter はテンプレートの最上部に記述し, 行頭から行末まで 3 つのハイフン `---` によって, その他の部分から分離されなければなりません。このブロックの中でテンプレートの中で `current_page.data` ハッシュとして使えるデータを作ることができます。`layout` の設定は Middleman に直接渡され, レンダリングに使用されるレイアウトを変更します。`ignore`, `directory_index` やその他のページプロパティもこの方法で設定することができます。

## JSON フォーマット

Frontmatter に JSON を使うこともできます。`;;;` で区切られ次のようになります:

``` html
;;;
"layout": "custom",
"my_list": [
  "one",
  "two",
  "three"
]
;;;
```

ページ内で YAML フォーマットの Frontmatter と同じように使うことができます。
