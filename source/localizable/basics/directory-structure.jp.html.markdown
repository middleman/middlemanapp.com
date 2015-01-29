---
title: ディレクトリ構造
---

# ディレクトリ構造

デフォルトの Middleman インストールではディレクトリ構造は次のようになります:

``` ruby
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

### source ディレクトリ

`source` ディレクトリには利用するテンプレートの JavaScript, CSS や画像を含む,
 ビルドされる web サイトのソースファイルが置かれます。

### build ディレクトリ

`build` ディレクトリは静的サイトのファイルがコンパイルされ出力される
ディレクトリです。

### data ディレクトリ

ローカルデータ機能によって `data` ディレクトリの中に `.yml`, `.yaml` または `.json` ファイルを
作成し, これらのファイルの情報をテンプレートの中で利用することができます。
`data` フォルダはプロジェクトの `source` フォルダと同じように, プロジェクトのルートに置かれます。
詳細については [ローカルデータ](/jp/advanced/local-data/) を
確認してください。

### Lib ディレクトリ

`lib` ディレクトリには, アプリケーションを構築するための [テンプレートヘルパ](/jp/basics/helpers/) を含む
外部 Ruby モジュールを配置することができます。Rails を利用されているのであれば,
この配置方法は同じです。

