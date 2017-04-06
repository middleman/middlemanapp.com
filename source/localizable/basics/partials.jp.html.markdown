---
title: パーシャル
---

# パーシャル

パーシャルはコンテンツの重複を避けるためにページ全体にわたってそのコンテンツを
共有する方法です。パーシャルはページテンプレートとレイアウトで使うことができます。
上記 2 つのレイアウトをもつ例を続けましょう: 通常のページと admin ページです。
この 2 つのレイアウトには footer のように重複する内容があります。
footer パーシャルを作成し, これらのレイアウトで使ってみましょう。

パーシャルのファイル名は prefix にアンダースコアが付き, 使用するテンプレート言語
の拡張子を含みます。例として `source` フォルダに置かれる `_footer.erb` と
名付けられた footer パーシャルを示します:

```html
<footer>
  Copyright 2011
</footer>
```

次に, "partial" メソッドを使ってデフォルトのレイアウトにパーシャルを
配置します:

```erb
<html>
<head>
  <title>私のサイト</title>
</head>
<body>
  <%= yield %>
  <%= partial "footer" %>
</body>
</html>
```

admin レイアウトでは次のように:

```erb
<html>
<head>
  <title>Admin エリア</title>
</head>
<body>
  <%= yield %>
  <%= partial "footer" %>
</body>
</html>
```

すると, `_footer.erb` への変更はこのパーシャルを使うそれぞれのレイアウトや
レイアウトを使うページに表示されます。

複数のページやレイアウトに Copy&Paste する内容を見つけた場合,
パーシャルに内容を抽出するのは良い方法です。

パーシャルを使い始めると, 変数を渡すことで異なった呼び出しを
行いたくなるかもしれません。次の方法で対応出来ます:

```erb
<%= partial(:paypal_donate_button, :locals => { :amount => 1, :amount_text => "Pay $1" }) %>
<%= partial(:paypal_donate_button, :locals => { :amount => 2, :amount_text => "Pay $2" }) %>
```

すると, パーシャルの中で次のようにテキストを設定することができます:

```erb
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <input name="amount" type="hidden" value="<%= "#{amount}.00" %>" >
  <input type="submit" value="<%= amount_text %>" >
</form>
```

詳細については [Padrino Render Helpers] を参照してください。

  [Padrino Render Helpers]: http://padrinorb.com/guides/application-helpers/render-helpers/
