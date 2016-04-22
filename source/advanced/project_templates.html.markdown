---
title: Project Templates
---

# Project Templates

When starting a new project, `middleman init` will create a folder with our default project template. Unlike previous versions of middleman, custom project templates are simply a folder of files, or a Thor command, in a git repository. If you want to use a non-default project template then you must use the `-T` option and use a path to a git repository. Make sure you have git installed.

## GitHub Template

Pass the GitHub `username/repo-name` to the `init` command.

```bash
middleman init MY_PROJECT_FOLDER -T username/repo-name
```

## Local Template

Pass `file://` followed by the path to your local git repository. **Note**: There are *three* slashes `///`.

```bash
middleman init MY_PROJECT_FOLDER -T file:///path/to/local/repo/
```


## Template Directory

In addition to the default project template, the Middleman community has created a lot of custom templates. There are a number of community-developed project templates in the [Directory](https://directory.middlemanapp.com/).

If you would like to have your template added to the Directory, please read the instructions on the [Middleman Directory](https://github.com/middleman/middleman-directory) GitHub page. When you add your project, you can provide a short name to simplify the `init` command. For example, our official [Middleman Blog](https://github.com/middleman/middleman-blog) template registered the `blog` name, so it can be initialized like this:

```bash
middleman init MY_NEW_BLOG -T blog
```


## Thor Template

Templates that require process can be implemented with Thor. The [default template](https://github.com/middleman/middleman-templates-default) is implemented this way so it can ask questions as initialization.

Place a `Thorfile` at the root of your repository:

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

Inside this Ruby class, all public methods will be executed in order. The simplest example above just copies a folder. So if you placed your default template in the `template` directory, it would work exactly like the non-Thor option above.
