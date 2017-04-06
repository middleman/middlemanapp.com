---
title: ディレクトリ構造
---

# ディレクトリ構造

デフォルトの Middleman インストールではディレクトリ構造は
次のようになります:

```
mymiddlemansite/
+-- .gitignore
+-- Gemfile
+-- Gemfile.lock
+-- config.rb
+-- source
    +-- images
    ¦   +-- background.png
    ¦   +-- middleman.png
    +-- index.html.erb
    +-- javascripts
    ¦   +-- all.js
    +-- layouts
    ¦   +-- layout.erb
    +-- stylesheets
        +-- all.css
        +-- normalize.css
```

## 主要なディレクトリ

Middleman は特定の目的のために `source`, `build`, `data` と `lib` ディレクトリを
利用します。各ディレクトリは Middleman のルートディレクトリに
存在します。

### `source` ディレクトリ

`source` ディレクトリには利用するテンプレートの JavaScript, CSS や画像を含む,
 ビルドされる web サイトのソースファイルが置かれます。

### `build` ディレクトリ

`build` ディレクトリは静的サイトのファイルがコンパイルされ出力される
ディレクトリです。

### `data` ディレクトリ

ローカルデータ機能によって `data` ディレクトリの中に YAML や JSON ファイルを作成
し, これらのファイルの情報をテンプレートの中で利用することができます。`data`
フォルダはプロジェクトの `source` フォルダと同じように,プロジェクトのルートに
置かれます。詳細については [ローカルデータ][Data Files] を確認してください。

### `lib` ディレクトリ

`lib` ディレクトリには, アプリケーションを構築するための
[テンプレートヘルパ][helpers] を含む外部 Ruby モジュールを配置することが
できます。Rails を利用されているのであれば, この配置方法は同じです。

  [Data Files]: /jp/advanced/data-files/
  [helpers]: /jp/basics/helper-methods/
