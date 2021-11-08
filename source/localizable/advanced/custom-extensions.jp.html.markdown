---
title: カスタム拡張
---

# カスタム拡張

Middleman の拡張機能は Middleman の特定のポイントにフックし,
新しい機能を追加しコンテンツを操作する Ruby のクラスです。このガイドでは
どのようなことが可能か説明しますが, すべてのフックや拡張ポイントを探すには
Middleman のソースや middleman-blog のようなプラグインのソースを
読むべきです。

## 拡張の雛形を用意

新しい拡張の雛形は `extension` コマンドで用意できます。このコマンドは
必要なファイルを作成します。

```bash
middleman extension middleman-my_extension

# create  middleman-my_extension/.gitignore
# create  middleman-my_extension/Rakefile
# create  middleman-my_extension/middleman-my_extension.gemspec
# create  middleman-my_extension/Gemfile
# create  middleman-my_extension/lib/middleman-my_extension/extension.rb
# create  middleman-my_extension/lib/middleman-my_extension.rb
# create  middleman-my_extension/features/support/env.rb
# create  middleman-my_extension/fixtures
```

## 拡張の基本

基本的な拡張機能は次のようになります:

```ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end
  alias :included :registered
end

::Middleman::Extensions.register(:my_feature, MyFeature)
```

このモジュールは `config.rb` からアクセスできなければなりません。`config.rb` に
直接定義するか, 別の Ruby ファイルに定義し `config.rb` で
`require` します。

もちろんモジュールが読み込まれてから `config.rb` で有効化しなければなりません:

```ruby
activate :my_feature
```

[`register`][register_class_method] メソッドは有効化される拡張機能に
名前を与えます。拡張機能を有効化するときに限りブロックを
与えることもできます。

`MyFeature` 拡張では, `registered` メソッドは `activate` コマンドが実行されると
すぐに呼び出されます。 `app` 変数は [`Middleman::Application`][application_class]
クラスのインスタンスです。

`activate` は拡張機能を設定するのためにオプションのハッシュ (`register` に
渡される) やブロックを渡すことができます。`options` クラスメソッドでオプションを
定義することで `options` でアクセスすることができます:

```ruby
class MyFeature < Middleman::Extension
  # この拡張機能のオプション
  option :foo, false, 'Controls whether we foo'

  def initialize(app, options_hash={}, &block)
    super

    puts options.foo
  end
end

# 拡張機能を設定する 2 つの方法
activate :my_feature, foo: 'whatever'
activate :my_feature do |f|
  f.foo = 'whatever'
  f.bar = 'something else'
end
```

`activate` へ渡すオプションはグローバルまたはシングルトン変数を設定することが
好ましいです。

## `config.rb` にメソッドを追加

拡張の中のメソッドは `expose_to_config` メソッドを使うことで `config.rb` で
利用できるようになります。

```ruby
class MyFeature < Middleman::Extension
  expose_to_config :say_hello

  def say_hello
    puts "Hello"
  end
end
```

## テンプレートにメソッドを追加

config と同じように, テンプレートに対して追加することができます:

```ruby
class MyFeature < Middleman::Extension
  expose_to_template :say_hello

  def say_hello
    "Hello Template"
  end
end
```

## ヘルパの追加

この他のテンプレートにメソッドを追加する方法はヘルパです。ヘルパは
テンプレート以外の拡張機能で使用することができません。この方法は
module にグループ化されたメソッド群をヘルパにする場合に適した方法です。
ほとんどの場合, 先述の expose メソッドを使用した方がよいです。

```ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end

  helpers do
    def make_a_link(url, text)
      "<a href='#{url}'>#{text}</a>"
    end
  end
end
```

これでテンプレートの中で, `make_a_link` メソッドにアクセスできるようになります。
ERB テンプレートでの使用例を示します:

```erb
<h1><%= make_a_link("http://example.com", "クリックしてください") %></h1>
```

## サイトマップ拡張

サイトマップ拡張を作ることで [サイトマップ][sitemap] でページを変更したり
追加したりできます。 [ディレクトリインデックス][directory_indexes] 拡張は
ページをディレクトリインデックス版に再ルーティングするためにこの機能を使い,
[ブログ拡張][blog extension] はタグやカレンダーページを作成するためにいくつかの
プラグインを使っています。詳細は [`Sitemap::Store` クラス][sitemap_store_class] を参照してください。

**Note:** `manipulate_resource_list` は "継ぎ手" にあたる処理です。
パイプラインの次の処理に渡すために完全なリソースを返す必要があります。

```ruby
class MyFeature < Middleman::Extension
  def manipulate_resource_list(resources)
    resources.each do |resource|
      resource.destination_path.gsub!("original", "new")
    end

    resources
  end
end
```

## コールバック

Middleman には拡張によってフックできる場所があります。いくつか例を示しますが,
ここに記述するよりも数多くあります。

### `after_configuration`

コードを実行するために `config.rb` が読み込まれるまで待ちたい場合があります。
例えば, `:css_dir` 変数に依存する場合, 設定されるまで待つべきです。
次の例ではこのコールバックを使っています:

```ruby
class MyFeature < Middleman::Extension
  def after_configuration
    puts app.config[:css_dir]
  end
end
```

### `after_build`

このコールバックはビルドプロセスが完了した後にコードを実行するために使われます。
[middleman-smusher] 拡張はビルド完了後に build フォルダのすべての画像を
圧縮するためにこの機能を使います。ビルド後に展開したスクリプトを結合することも
考えられます。

```ruby
class MyFeature < Middleman::Extension
  def after_build(builder)
    builder.thor.run './my_deploy_script.sh'
  end
end
```

[`builder.thor`][build] パラメータは CLI のビルドを実行するクラスで,
そこから [Thor のアクション][Thor action] を使用できます。

  [middleman-blog]: https://github.com/middleman/middleman-blog
  [register_class_method]: http://rubydoc.info/gems/middleman-core/Middleman/Extensions#register-class_method
  [application_class]: http://rubydoc.info/gems/middleman-core/Middleman/Application
  [sitemap]: /jp/advanced/sitemap/
  [directory_indexes]: /jp/advanced/pretty-urls/
  [blog extension]: /jp/basics/blogging/
  [sitemap_store_class]: http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#register_resource_list_manipulator-instance_method
  [middleman-smusher]: https://github.com/middleman/middleman-smusher
  [build]: http://rubydoc.info/gems/middleman-core/Middleman/Cli/Build
  [Thor actions]: http://rubydoc.info/github/wycats/thor/master/Thor/Actions

### その他のコールバック

1. `initialized`: config.rb のパース前, 拡張機能の register 前
1. `configure`: `configure` ブロックが呼び出される前 (環境設定で 1 度, モード設定でもう 1 度)
1. `before_extensions`: `ExtensionManager` がインスタンスかされる前
1. `before_instance_block`: configuration コンテキストにブロックが渡される前
1. `before_sitemap`: `SiteMap::Store` がインスタンス化される前, つまりサイトマップが初期化される前
1. `before_configuration`: 設定がパースされる前に呼び出され, 主に拡張機能に利用
1. `after_configuration_eval`: 設定がパースされた後に呼び出され, pre-extension コールバックの前
1. `ready`: すべての準備が完了した時
1. `before_build`: サイトのビルドプロセスの前
1. `before_shutdown`: アプリケーションの終了をユーザーに通知する `shutdown!` メソッドの前
1. `before`: Rack リクエストの前
1. `before_server`: `PreviewServer` が用意される前
1. `reload`: リロードイベントでアプリケーションが初期化される前
