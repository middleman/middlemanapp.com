---
title: カスタムテンプレート
---

# カスタムテンプレート

新しいプロジェクトを始める場合, `middleman init` コマンドはデフォルトテンプレートをフォルダに中に展開します。より包括的なテンプレートにしたい, 自分のデフォルトテンプレートを用意したい場合, カスタムテンプレートを作ることができます。

v4 のカスタムテンプレートは, 単に Github にホストされているファイルのフォルダまたは Thor コマンドです。カスタムテンプレートファイルに対して特に処理が必要ない場合は Github にテンプレートを保存することで初期化することができます:

```bash
middleman init -T username/repo-name MY_PROJECT_FOLDER
```

Github にホストしない場合, `-T` オプションに git リポジトリへのフルパスを設定できます。

## Thor テンプレート

処理を必要とするテンプレートは Thor で実現することができます。[デフォルトテンプレート](https://github.com/middleman/middleman-templates-default) はこの方法で作られているのでプロジェクト初期化時に質問をすることができます。

`Thorfile` はリポジトリの root に配置してください:

```ruby
require 'thor/group'

module Middleman
  class Generator < ::Thor::Group
    include ::Thor::Actions

    source_root File.expand_path(File.dirname(__FILE__))

    def copy_default_files
      directory 'template', '.', exclude_pattern: /\.DS_Store$/
    end
  end
end
```

この Ruby クラスの中の public メソッドは順に実行されます。上記の簡単な例ではフォルダをコピーしています。`template` ディレクトリにあなたが使用したいデフォルトテンプレートがある場合, 先述した Thor を使用しないテンプレートのように動作します。

## テンプレートディレクトリ

あなたのテンプレートを世界にシェアしたい場合, [Middleman Directory](https://directory.middlemanapp.com) に追加することができます。あなたのプロジェクトを追加すると, `init` コマンド実行時に指定するテンプレート名を短くすることができます。

例えば, Middleman の公式 blog テンプレートは `blog` で登録されているので, 次のコマンドで初期化できます:

```bash
middleman init -T blog MY_NEW_BLOG
```
