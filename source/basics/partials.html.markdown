# Partials

Partials are a way of sharing content across pages to avoid duplication.
Partials can be used in page templates and layouts. Let's continue our above
example of having two layouts: one for normal pages and one for admin pages.
These two layouts could have duplicate content, such as a footer. We will
create a footer partial and use it in both layouts.

Partial files are prefixed with an underscore and include the templating
language extension you are using. Here is an example footer partial named
`_footer.erb` that lives in the `source` folder:

``` html
<footer>
  Copyright 2011
</footer>
```

Now, we can include this partial in the default layout using the "partial"
method:

``` html
<html>
<head>
  <title>My Site</title>
</head>
<body>
  <%= yield %>
  <%= partial "footer" %>
</body>
</html>
```

And in the admin layout:

``` html
<html>
<head>
  <title>Admin Area</title>
</head>
<body>
  <%= yield %>
  <%= partial "footer" %>
</body>
</html>
```

Now, any changes to `_footer.erb` will appear at the bottom of both layouts and
any pages which use those layouts.

If you find yourself copying and pasting content into multiple pages or
layouts, it's probably a good idea to extract that content into a partial.

After you start using partials, you may find you want to call it in different
ways by passing variables. You can do this by:

``` html
<%= partial(:paypal_donate_button, :locals => { :amount => 1, :amount_text => "Pay $1" }) %>
<%= partial(:paypal_donate_button, :locals => { :amount => 2, :amount_text => "Pay $2" }) %>
```

Then, within the partial, you can set the text appropriately as follows:

``` html
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <input name="amount" type="hidden" value="<%= "#{amount}.00" %>" >
  <input type="submit" value="<%= amount_text %>" >
</form>
```

Read the [Padrino partial helper] documentation for more information.

[Padrino partial helper]: http://www.padrinorb.com/api/classes/Padrino/Helpers/RenderHelpers.html
