---
title: 設定
---

# 設定

## Middleman の設定を知る

Middleman は驚くほどカスタマイズ可能で, 拡張機能はさらに機能を提供します。設定ごとに完璧なドキュメントを用意するより, 私たちは Middleman にどのような設定ができるか示す機能を与えました。

プレビューサーバを立ち上げ, `http://localhost:4567/__middleman/config/` にアクセスすると, あらゆる設定や使用可能な拡張機能を確認できます。設定名, 簡単な説明, デフォルト値やあなたのサイトでどう設定されているのかを含みます。

## 設定変更

ほとんどの設定の変更方法は `config.rb` で `set` を指定することです:

```ruby
set :js_dir, 'js'
```

新しい構文を使うこともできます:

```ruby
config[:js_dir] = 'js'
```

これらの書き方は Middleman のほとんどのグローバル設定に使われます。

## 拡張機能の設定

拡張機能は有効化されたタイミングで設定されます。ほとんどの拡張では, `activate` する際にハッシュのオプションを渡すか, オプションを設定するためにブロックを使います:

```ruby
activate :asset_hash, :exts => %w(.jpg) # .jpg のみ有効化

# もしくは:

activate :asset_hash do |opts|
  opts.exts += $(.ico)
end
```

## 環境に応じた設定

ビルド時または develop 環境でのみ適用したい設定がある場合, ブロックの中で設定することができます:

```ruby
configure :development do
  set :debug_assets, true
end

configure :build do
  activate :minify_css
end
```
