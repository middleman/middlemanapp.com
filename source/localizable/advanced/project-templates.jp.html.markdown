---
title: プロジェクトテンプレート
---

# プロジェクトテンプレート

新しいプロジェクトを始める場合, `middleman init` コマンドは
[デフォルトのプロジェクトテンプレート][default project template] をフォルダに
展開します。以前のバージョンの middleman とは異なり, カスタムプロジェクトテンプレートは
git リポジトリに単にファイルを置くか Thor コマンド用意するだけです。
デフォルトのプロジェクトテンプレートを使いたくない場合, `-T` オプションに
git リポジトリのパスを指定する必要があります。

## Github テンプレート

`init` コマンドに Github の `username/repo-name` を渡します。

```bash
middleman init MY_PROJECT_FOLDER -T username/repo-name
```

## ローカルテンプレート

ローカルの Git リポジトリへのパスを `file://` で渡します。
**Note**: *3本の* スラッシュ `///` が必要です。

```bash
 +middleman init MY_PROJECT_FOLDER -T file:///path/to/local/repo/
```

## テンプレートディレクトリ

デフォルトのプロジェクトテンプレートに加え, Middleman コミュニティには
たくさんのカスタムテンプレートがあります。コミュニティによって開発された
プロジェクトテンプレートは [ディレクトリ][Directory] で確認できます。

ディレクトリにあなたが開発したテンプレートを追加したい場合は,
[Middleman ディレクトリ][directory_github] の説明を読んでください。プロジェクトを
追加すると `init` コマンド実行時に短い名前でテンプレートを指定できます。
たとえば, 公式の [Middleman ブログ][Middleman Blog] テンプレートは `blog` として
登録されているので, 次のようにプロジェクトの初期化を行うことができます:

```bash
middleman init MY_NEW_BLOG -T blog
```

## Thor テンプレート

処理を必要とするテンプレートは [Thor] で実現することができます。
[デフォルトテンプレート][default project template] はこの方法で作られているので
プロジェクト初期化時に質問をすることができます。

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

この Ruby クラスの中の public メソッドは順に実行されます。上記の簡単な例では
フォルダをコピーしています。`template` ディレクトリにあなたが使用したい
デフォルトテンプレートがある場合, 先述した Thor を使用しないテンプレートのように
動作します。

  [default project template]: https://github.com/middleman/middleman-templates-default/
  [Directory]: https://directory.middlemanapp.com/
  [directory_github]: https://github.com/middleman/middleman-directory
  [Middleman Blog]: https://github.com/middleman/middleman-blog
  [Thor]: http://whatisthor.com/
