# Installation

Middleman is distributed using the RubyGems package manager. This means you
will need both the Ruby language runtime installed and RubyGems to begin using
Middleman.

Mac OS X comes prepackaged with both Ruby and Rubygems, however, some of the
Middleman's dependencies need to be compiled during installation and on OS X
that requires Xcode. Xcode can be installed via the [Mac App
Store](http://itunes.apple.com/us/app/xcode/id497799835?ls=1&mt=12).
Alternately, if you have a free Apple Developer account, you can just install
Command Line Tools for Xcode from their [downloads
page](https://developer.apple.com/downloads/index.action).

Once you have Ruby and RubyGems up and running, execute the following from the
command line:

``` bash gem install middleman ```

This will install Middleman, its dependencies and the command-line tools for
using Middleman.

The installation process will add one new command to your environment, with 3
useful features:

```bash
$ middleman init
$ middleman server
$ middleman build
```

The uses of each of these commands will be covered in the next section, [Start
a New Site](/basics/start_new_site).
