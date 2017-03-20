---
title: Frontmatter
---

# Frontmatter

Frontmatter は YAML または JSON フォーマットでテンプレート上部に記述することができる
ページ固有の変数です。

## YAML フォーマット

簡単な ERB テンプレートに固有のページのレイアウトを変更する
Frontmatter を追加します。

```yaml
---
layout: "custom"
title: "私のタイトル"
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

Frontmatter はテンプレートの最上部に記述し, 行頭から行末まで 3 つのハイフン `---` によって, その他の部分から分離されなければなりません。このブロックの中ではテンプレート内で `current_page.data` ハッシュとして使えるデータを作ることができます。例: `title: "私のタイトル"` は `current_page.data.title` で取得できます。`layout` の設定は Middleman に直接渡され, レンダリングに使用されるレイアウトを変更します。`ignored`, `directory_index` やその他のページプロパティもこの方法で設定することができます。

## JSON フォーマット

Frontmatter に JSON を使うこともできます。`;;;` で区切られ
次のようになります:

```json
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
