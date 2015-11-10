# プロジェクトテンプレート

デフォルトの基本スケルトンに加え, Middleman は
[HTML5 Boilerplate], [SMACSS], や [Mobile Boilerplate](http://html5boilerplate.com/mobile/)
ベースのオプションテンプレートが付属します。
Middleman 拡張 ([middleman-blog](/jp/basics/blogging/) のような) は同じように
独自のテンプレートを使用することができます。
テンプレート変更は `-T` や `--template` コマンドラインフラグを使用してアクセスできます。
例えば, HTML5 Boilerplate ベースのプロジェクトを始める場合, 次のコマンドを使用します:

``` bash
$ middleman init my_new_boilerplate_project --template=html5
```

最後に, `~/.middleman/` フォルダの中に独自のカスタムテンプレートの
スケルトンを入れたフォルダを作ることができます。
例えば, `~/.middleman/mobile` フォルダを作り, モバイルプロジェクトで
利用するファイルをフォルダ用意することができます。

help フラグとともに middleman init コマンドを使用すると,
使用可能なテンプレートのリストが表示されます:

``` bash
$ middleman init --help
```

このコマンドは私の独自モバイルテンプレートを表示し, 以前と同じように
新しいプロジェクトを作ることができます:

``` bash
$ middleman init my_new_mobile_project --template=mobile
```

### 用意されているプロジェクトテンプレート

Middleman は基本的なプロジェクト用のテンプレートをいくつか用意しています:

**[HTML5 Boilerplate]**

``` bash
$ middleman init my_new_html5_project --template=html5
```

**[SMACSS]**

``` bash
$ middleman init my_new_smacss_project --template=smacss
```

**[Mobile Boilerplate](http://html5boilerplate.com/mobile/)**

``` bash
$ middleman init my_new_mobile_project --template=mobile
```

### コミュニティプロジェクトテンプレート

こちらにもいくつか [コミュニティで開発されたテンプレート](https://directory.middlemanapp.com/#/templates/all) があります。

[HTML5 Boilerplate]: http://html5boilerplate.com/
[SMACSS]: http://smacss.com/
