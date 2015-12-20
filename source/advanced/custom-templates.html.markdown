---
title: Custom Templates
---

# Custom Templates

When starting a new project, `middleman init` will create a folder with our default template. If you want something more comprehensive, or want to package up your default starting project, you can create a custom template.

Custom templates in v4 are simply a folder of files or a Thor command, hosted on Github. If you don't need any custom template file processing, then simply post your template on Github and it can be initialized:

```bash
middleman init -T username/repo-name MY_PROJECT_FOLDER
```

If you are not hosted on Github, the `-T` parameter can be a full git path.

## Thor Templates

Template that require process can be implemented with Thor. The [default template](https://github.com/middleman/middleman-templates-default) is implemented this way so it can ask questions as initialization.

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

## Template Directory

If you want to share your template with the world, you can add it to the [Middleman Directory](https://directory.middlemanapp.com). When you add your project, you can provide a short name to simplify the `init` command even more.

For example, our official Middleman Blog template registed the `blog` name, so it can be initialized like this:

```bash
middleman init -T blog MY_NEW_BLOG
```
