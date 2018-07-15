---
title: Directory Structure
---

# Directory Structure

The default Middleman installation consists of a directory structure which looks
like this:

```
mymiddlemansite/
├── .gitignore
├── Gemfile
├── Gemfile.lock
├── config.rb
└── source
    ├── images
    │   └── .keep
    ├── index.html.erb
    ├── javascripts
    │   └── site.js
    ├── layouts
    │   └── layout.erb
    └── stylesheets
        └── site.css.scss
```

## Main Directories

Middleman makes use of the `source`, `build`, `data` and `lib` directories for
specific purposes. Each of these directories are children of the main Middleman
directory.

### `source` Directory

The `source` directory contains your main website source files to be built,
including your templates JavaScript, CSS and images.

### `build` Directory

The `build` directory is where your static website files will be compiled and
exported to.

### `data` Directory

Local Data allows you to create YAML or JSON files in a folder called `data` and
makes this information available in your templates. The `data` folder should be
placed in the root of your project (i.e. in the same folder as your project's
`source` folder). See the [Data Files] docs for more information.

### `lib` Directory

The `lib` directory enables you to include external Ruby modules which contain
[helpers] for building your application. If you use Rails then you will be
familiar with this layout.

  [Data Files]: /advanced/data-files/
  [helpers]: /basics/helper-methods/
