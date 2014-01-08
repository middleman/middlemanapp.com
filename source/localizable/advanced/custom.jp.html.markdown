---
title: カスタム拡張
---

# カスタム拡張

Middleman の拡張機能は Middleman のさまざまなポイントにフックし, 新しい機能を追加しコンテンツを操作する Ruby のクラスです。このガイドではどのようなことが可能なのか説明しますが, すべてのフックや拡張ポイントを探すには Middleman のソースや middleman-blog のようなプラグインのソースを読むべきです。

## 拡張の基本

基本的な拡張機能は次のようになります:

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end
  alias :included :registered
end

::Middleman::Extensions.register(:my_feature, MyFeature)
```

このモジュールは `config.rb` からアクセスできなければなりません。 `config.rb` に直接定義するか, 別の Ruby ファイルに定義し `config.rb` で `require` します。

そして, モジュールが読み込まれてから `config.rb` で有効化しなければなりません:

``` ruby
activate :my_feature
```

[`register`](http://rubydoc.info/gems/middleman-core/Middleman/Extensions#register-class_method) メソッドには有効化される拡張機能の名前を与えます。拡張機能を有効化するときに限りブロックを与えることもできます。

`MyFeature` 拡張では, `registered` メソッドは `activate` コマンドが実行されるとすぐに呼び出されます。 `app` 変数は [`Middleman::Application`](http://rubydoc.info/gems/middleman-core/Middleman/Application) クラスです。

`activate` は拡張機能を設定するのためにオプションのハッシュ (`register` に渡される) やブロックを渡すことができます。`options` クラスメソッドでオプションを定義することで `options` でアクセスすることができます:

``` ruby
class MyFeature < Middleman::Extension
  # この拡張機能のオプション
  option :foo, false, 'Controls whether we foo'

  def initialize(app, options_hash={}, &block)
    super

    puts options.foo
  end
end

# 拡張機能を設定する 2 つの方法
activate :my_feature, :foo => 'whatever'
activate :my_feature do |f|
  f.foo = 'whatever'
  f.bar = 'something else'
end
```

拡張機能の設定については `set` を使ってグローバル変数を設定するという方法もありますが (次のセクション参照), 通常は `activate` へオプションを与える方が好まれます。

## 変数の設定

[`Middleman::Application`](http://rubydoc.info/gems/middleman-core/Middleman/Application) クラスは拡張機能で使用されるグローバル設定 (`set` コマンドを使用する変数) を変更するために使うことができます。

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super

    app.set :css_dir, "lib/my/css"
  end
end
```

拡張機能からアクセスできる新しい設定を作ることもできます。

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super

    app.set :my_feature_setting, %w(one two three)
  end

  helpers do
    def my_helper
      my_feature_setting.to_sentence
    end
  end
end
```

`set` は `Middleman::Application` に新しいメソッドを追加することで, 他の場所から `my_feature_setting` を介して変数の値を読み取ることができます。拡張機能に特定の値を必要とするだけの場合には, グローバル設定の代わりに `activate` のオプションを使うことを検討した方がよいでしょう。

## config.rb にメソッドを追加

`config.rb` の中で使用できるメソッドは `Middleman::Application` の単なるクラスメソッドです。`config.rb` の中で新しいメソッドを追加してみましょう:

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
    app.extend ClassMethods
  end

  module ClassMethods
    def say_hello
      puts "Hello"
    end
  end
end
```

`Middleman::Application` クラスを拡張し `app` として使用できるようにすることで, この環境に単純に "Hello" を出力する `say_hello` メソッドを追加しました。内部的には, これらのメソッドはこのアプリの中で処理されるパスやリクエストのリストを作成するために使われます。

## ヘルパの追加

ヘルパはテンプレートの中で使用できるメソッドです。ヘルパメソッドを追加するには, 次のようにします:

``` ruby
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

これでテンプレートの中で, `make_a_link` メソッドにアクセスできるようになります。 ERb テンプレートでの使用例を示します:

``` html
<h1><%= make_a_link("http://example.com", "クリックしてください") %></h1>
```


## サイトマップ拡張

サイトマップ拡張を作ることで [サイトマップ](/jp/advanced/sitemap/) でページを変更したり追加したりできます。 [ディレクトリインデックス](/jp/basics/pretty-urls/) 拡張はページをディレクトリインデックス版に再ルーティングするためにこの機能を使い, [ブログ拡張](/jp/basics/blogging/) はタグやカレンダーページを作成するためにいくつかのプラグインを使っています。詳細は [`Sitemap::Store`](http://rubydoc.info/gems/middleman-core/Middleman/Sitemap/Store#register_resource_list_manipulator-instance_method) を参照してください。

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end

  def manipulate_resource_list(resources)
    resources.each do |resource|
      resource.destination_path.gsub!("original", "new")
    end
  end
end
```

## コールバック

Middleman には拡張によってフックできる部分があります。いくつか例を示しますが, ここに記述するよりも数多くあります。

### after_configuration

コードを実行するために `config.rb` が読み込まれるまで待ちたい場合があります。例えば, `:css_dir` 変数に依存する場合, 設定されるまで待つべきです。次の例ではこのコールバックを使っています:

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
  end

  def after_configuration
    the_users_setting = app.settings.css_dir
    app.set :my_setting, "#{the_users_setting}_with_my_suffix"
  end
end
```

### before

before コールバックは Middleman がページをレンダリングする前に処理を実行することができます。別のソースからデータを返したり, 早い段階で処理を中止したい場合に便利です。

例:

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
    app.before do
      app.set :currently_requested_path, request.path_info
      true
    end
  end
end
```

この例ではリクエストごとに `:currently_requested_path` に値をセットします。"true" を返すことに注意してください。`before` を使ったブロックは true または false を返さなければいけません。

### after_build

このコールバックはビルドプロセスが完了した後にコードを実行するために使われます。 [middleman-smusher] 拡張はビルド完了後に build フォルダのすべての画像を圧縮するためにこの機能を使います。ビルド後に展開したスクリプトを結合することも考えられます。

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super
    app.after_build do |builder|
      builder.run './my_deploy_script.sh'
    end
  end
end
```

[`builder`](http://rubydoc.info/gems/middleman-core/Middleman/Cli/Build) パラメータは CLI のビルドを実行するクラスで, そこから [Thor のアクション](http://rubydoc.info/github/wycats/thor/master/Thor/Actions) を使用できます。

### compass_config

同じく, 拡張が Compass が用意する変数や設定に依存する場合, `compass_config` コールバックが使用できます。

``` ruby
class MyFeature < Middleman::Extension
  def initialize(app, options_hash={}, &block)
    super

    app.compass_config do |config|
      # config は Compass.configuration オブジェクト
      config.output_style = :compact
    end
  end
end
```

[middleman-smusher]: https://github.com/middleman/middleman-smusher
