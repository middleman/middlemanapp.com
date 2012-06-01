---
title: Sass and Compass
---

# Sass and Compass

Probably the biggest advantage in using Middleman to accelerate your development is access to higher-level languages such as Sass, Haml and CoffeeScript which are quicker to code, but still compile to normal CSS, HTML and Javascript for deployment.

If you are unfamiliar, Sass is a CSS preprocessor which provides a simpler syntax for authoring stylesheets and includes higher-level features such as variables, includes, mixins and selector inheritance. You can read more about Sass, and it's CSS-superset language SCSS, on the [Sass Lang website].

Middleman allows you to author Sass and SCSS files by appending the file extension for the language you wish to use to a CSS file. Here are some examples.

A Sass file in `source/stylesheets/login.css.sass` using the whitespace-aware Sass syntax:

    body
      background: white
      color: black
      
    #login
      text-align: center
      font-size: 20px
      button
        float: right

The same styles could also be expressed in the CSS-superset SCSS format in a file at `source/stylesheets/login.css.scss`:

    body {
      background: white;
      color: black; }

    #login {
      text-align: center;
      font-size: 20px;
      button {
        float: right; }
    }

Both of these files will compile to the following `build/stylesheets/login.css` file:

    :::css
    body {
      background: white;
      color: black; }

    #login {
      text-align: center;
      font-size: 20px; }
      #login button {
        float: right; }

## Compass

Compass is a set of mixins (Sass macros) to make common tasks more convenient (and include browser-specific hacks), functions for manipulating colors, a sprite generation library and much more. You can read more on the [official Compass website].

When using Middleman, Compass is automatically available in all Sass and SCSS files. Here are a few, examples of using Compass.

CSS3 and Floats:

    @import "compass"
    
    #main
      +float-left
      border: 1px solid black
      +border-radius(5px)
    #sidebar
      +float-right
      +box-shadow(black 0 1px 2px)

The same code in SCSS:

    @import "compass";
    
    #main {
      @include float-left;
      border: 1px solid black;
      @include border-radius(5px); }
    #sidebar {
      @include float-right;
      @include box-shadow(black 0 1px 2px); }

Grids (using [Susy 1.0.x](http://susy.oddbird.net/)):

    @import "susy"

    $total-columns  : 12;             // a 12-column grid
    $column-width   : 4em;            // each column is 4em wide
    $gutter-width   : 1em;            // 1em gutters between columns
    $grid-padding   : $gutter-width;  // grid-padding equal to gutters
    
    #page
      +container
      #header, #footer
        clear: both
      #content
        +span-columns(8, 12)
      #sidebar
        +span-columns(4 omega, 12)

[Sprite] generation:

    @import "compass"
    @import "icon/*.png"
    +all-icon-sprites

[Sass Lang website]: http://sass-lang.com/
[official Compass website]: http://compass-style.org/
[Susy]: http://susy.oddbird.net/
[Sprite]: http://compass-style.org/help/tutorials/spriting/
