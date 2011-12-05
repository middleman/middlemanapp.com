---
title: Template Helpers
---

# Template Helpers

Template helpers are methods which can be used in your dynamic templates to simplify common HTML tasks. Most of the basic methods should be very familiar to anyone who has used rails view helpers. These helpers are all built on the Padrino Framework, [view the full documentation here].

## Output Helpers

Output helpers are a collection of important methods for managing, capturing and displaying output in various ways and is used frequently to support higher-level helper functions. There are three output helpers worth mentioning: `content_for`, `capture_html`, and `concat_content`.

The `content_for` functionality supports capturing content and then rendering this into a different place such as within a layout. One such example is including assets onto the layout from a template:

    :::erb
    <% content_for :assets do %>
      <%= stylesheet_link_tag 'index', 'custom' %>
    <% end %>
    
Added to a template, this will capture the includes from the block and allow them to be yielded into the layout:

    :::erb
    <head>
      <title>Example</title>
      <%= stylesheet_link_tag 'style' %>
      <%= yield_content :assets %>
    </head>
    
This will automatically insert the contents of the block (in this case a stylesheet include) into the location the content is yielded within the layout.

You can also check if a `content_for` block exists for a given key using `content_for?`:

    :::erb
    <% if content_for?(:assets) %>  
      <div><%= yield_content :assets %></div>
    <% end %>
  
Also supports arguments yielded to the content block

    :::erb
    yield_content :head, param1, param2
    content_for(:head) { |param1, param2| ...content... }
     
## Tag Helpers

Tag helpers are the basic building blocks used to construct html "tags" within a view template. There are three major functions for this category: `tag`, `content_tag` and `input_tag`.

The tag and `content_tag` are for building arbitrary html tags with a name and specified options. If the tag contains "content" within then `content_tag` is used. For example:

    :::erb
    <%= tag :img, :src => "/my_image.png" %>
      # => <img src='/my_image.png'>
    
    <%= content_tag :p, :class => "stuff" do %>
      Hello
    <% end %>
      # => <p class='stuff'>Hello</p>
  
The input_tag is used to build tags that are related to accepting input from the user:

    :::ruby
    input_tag :text, :class => "demo" 
      # => <input type='text' class='demo'>
    input_tag :password, :value => "secret", :class => "demo"
      # => <input type='password' value='secret' class='demo'>
    
## Asset Helpers

Asset helpers are intended to help insert useful html onto a view template such as hyperlinks, mail_to links, images, stylesheets and javascript. An example of their uses would be on a simple view template:

    :::erb
    <html>
    <head>
      <%= stylesheet_link_tag 'layout' %>
      <%= javascript_include_tag 'application' %>
      <%= favicon_tag 'images/favicon.png' %>
    </head>
    <body>
      <p><%= link_to 'Blog', '/blog', :class => 'example' %></p>
      <p>Mail me at <%= mail_to 'fake@faker.com', "Fake Email Link", 
                          :cc => "test@demo.com" %></p>
      <p><%= image_tag 'padrino.png', :width => '35', 
               :class => 'logo' %></p>
    </body>
    </html>
 
## Form Helpers

Form helpers are the "standard" form tag helpers you would come to expect when building forms. A simple example of constructing a non-object form would be:

    :::erb
    <% form_tag '/destroy', :class => 'destroy-form', :method => 'delete' do %>
      <% field_set_tag do %>
        <p>
          <%= label_tag :username, :class => 'first' %>
          <%= text_field_tag :username, :value => params[:username] %>
        </p>
        <p>
          <%= label_tag :password, :class => 'first' %>
          <%= password_field_tag :password, :value => params[:password] %>
        </p>
        <p>
          <%= label_tag :strategy %>
          <%= select_tag :strategy, :options => ['delete', 'destroy'],
              :selected => 'delete' %>
        </p>
        <p>
          <%= check_box_tag :confirm_delete %>
        </p>
      <% end %>
      <% field_set_tag(:class => 'buttons') do %>
        <%= submit_tag "Remove" %>
      <% end %>
    <% end %>
    
## Format Helpers

Format helpers are several useful utilities for manipulating the format of text to achieve a goal.
The four format helpers are `escape_html`, `distance_of_time_in_words`, `time_ago_in_words`, and `js_escape_html`.

The `escape_html` and `js_escape_html` function are for taking an html string and escaping certain characters.
`escape_html` will escape ampersands, brackets and quotes to their HTML/XML entities. This is useful to sanitize user content before displaying this on a template. `js_escape_html` is used for passing javascript information from a js template to a javascript function.

    :::ruby
    escape_html('<hello>&<goodbye>') # => &lt;hello&gt;&amp;&lt;goodbye&gt;

There is also an alias for `escape_html` called `h` for even easier usage within templates.

Format helpers also includes a number of useful text manipulation functions such as `simple_format`, `pluralize`, `word_wrap`, and `truncate`.

    :::ruby
    simple_format("hello\nworld") 
      # => "<p>hello<br/>world</p>"
    pluralize(2, 'person') 
      # => '2 people'
    word_wrap('Once upon a time', :line_width => 8) 
      # => "Once upon\na time"
    truncate("Once upon a time in a world far far away", :length => 8) 
      # => "Once upon..."
    truncate_words("Once upon a time in a world far far away", :length => 4)
      # => "Once upon a time..."
    highlight('Lorem dolor sit', 'dolor') 
      # => "Lorem <strong class="highlight">dolor</strong> sit"

## Lorem Ipsum & Placehold.it helpers

The [Frank project], a static tool also inspired by Sinatra, has a wonderful set of helpers for generating random text content and placeholder images. I'm adapted this code for Middleman (god bless the MIT license).

To use placeholder text:

    lorem.sentence      # returns a single sentence
    lorem.words 5       # returns 5 individual words
    lorem.word
    lorem.paragraphs 10 # returns 10 paragraphs 
    lorem.paragraph
    lorem.date          # accepts a strftime format argument
    lorem.name
    lorem.first_name
    lorem.last_name
    lorem.email

And to use placeholder images:

    lorem.image('300x400')
      #=> http://placehold.it/300x400
    lorem.image('300x400', :background_color => '333', :color => 'fff')
      #=> http://placehold.it/300x400/333/fff
    lorem.image('300x400', :random_color => true)
      #=> http://placehold.it/300x400/f47av7/9fbc34d
    lorem.image('300x400', :text => 'blah')
      #=> http://placehold.it/300x400&text=blah

## Custom Defined Helpers

In addition to the helpers provided by Middleman out of the box, you can also add your own helper methods and classes that will be accessible within any controller or view automatically.

To define a helper method, use the `helpers` block in `config.rb`:

    :::ruby
    helpers do
      def some_method
        # ...do something here...
      end
    end

[view the full documentation here]: http://www.padrinorb.com/guides/application-helpers
[Frank project]: https://github.com/blahed/frank