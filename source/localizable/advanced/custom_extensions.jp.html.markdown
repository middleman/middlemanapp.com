---
title: カスタム拡張
---

# カスタム拡張

Middleman の拡張機能は Middleman の特定のポイントにフックし, 新しい機能を追加しコンテンツを操作する Ruby のクラスです。このガイドではどのようなことが可能か説明しますが, すべてのフックや拡張ポイントを探すには Middleman のソースや middleman-blog のようなプラグインのソースを読むべきです。

## 拡張の雛形を用意

新しい拡張の雛形は `extension` コマンドで用意できます。このコマンドは必要なファイルを作成します。

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

このモジュールは `config.rb` からアクセスできなければなりません。`config.rb` に直接定義するか, 別の Ruby ファイルに定義し `config.rb` で `require` します。

もちろんモジュールが読み込まれてから `config.rb` で有効化しなければなりません:

```ruby
activate :my_feature
```

[`register`](http://rubydoc.info/gems/middleman-core/Middleman/Extensions#register-class_method) メソッドは有効化される拡張機能に名前を与えます。拡張機能を有効化するときに限りブロックを与えることもできます。

`MyFeature` 拡張では, `registered` メソッドは `activate` コマンドが実行されるとすぐに呼び出されます。 `app` 変数は
[`Middleman::Application`](http://rubydoc.info/gems/middleman-core/Middleman/Application) クラスのインスタンスです。

`activate` は拡張機能を設定するのためにオプションのハッシュ (`register` に渡される) やブロックを渡すことができます。`options` クラスメソッドでオプションを定義することで `options` でアクセスすることができます:

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

`activate` へ渡すオプションはグローバルまたはシングルトン変数を設定することが好ましいです。

## config.rb にメソッドを追加

拡張の中のメソッドは `expose_to_config` メソッドを使うことで `config.rb` で利用できるようになります。

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

この他のテンプレートにメソッドを追加する方法はヘルパです。ヘルパはテンプレート以外の拡張機能で使用することができません。この方法は module にグループ化されたメソッド群をヘルパにする場合に適した方法です。ほとんどの場合, 先述の expose メソッドを使用した方がよいです。

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

これでテンプレートの中で, `make_a_link` メソッドにアクセスできるようになります。ERB テンプレートでの使用例を示します:

```erb
<h1><%= make_a_link("http://example.com", "クリックしてください") %></h1>
```

## サイトマップ拡張

サイトマップ拡張を作ることで [サイトマップ](/jp/advanced/sitemap/) でページを変更したり追加したりできます。 [ディレクトリインデックス](/jp/basics/pretty_urls/) 拡張はページをディレクトリインデックス版に再ルーティングするためにこの機能を使い, [ブログ拡張](/jp/basics/blogging/) はタグやカレンダーページを作成するためにいくつかのプラグインを使っています。詳細は [`Sitemap::Store` クラス](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#register_resource_list_manipulator-instance_method) を参照してください。

**Note:** `manipulate_resource_list` は "継ぎ手" にあたる処理です。パイプラインの次の処理に渡すために完全なリソースを返す必要があります。

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

Middleman には拡張によってフックできる場所があります。いくつか例を示しますが, ここに記述するよりも数多くあります。

### after_configuration

コードを実行するために `config.rb` が読み込まれるまで待ちたい場合があります。例えば, `:css_dir` 変数に依存する場合, 設定されるまで待つべきです。次の例ではこのコールバックを使っています:

```ruby
class MyFeature < Middleman::Extension
  def after_configuration
    puts app.config[:css_dir]
  end
end
```

### `after_build`

このコールバックはビルドプロセスが完了した後にコードを実行するために使われます。[middleman-smusher](https://github.com/middleman/middleman-smusher) 拡張はビルド完了後に build フォルダのすべての画像を圧縮するためにこの機能を使います。ビルド後に展開したスクリプトを結合することも考えられます。

```ruby
class MyFeature < Middleman::Extension
  def after_build(builder)
    builder.thor.run './my_deploy_script.sh'
  end
end
```

[`builder`](http://rubydoc.info/gems/middleman-core/Middleman/Cli/Build) パラメータは CLI のビルドを実行するクラスで, そこから [Thor のアクション](http://rubydoc.info/github/wycats/thor/master/Thor/Actions) を使用できます。
