---
title: Data Files
---

# Data Files

Sometimes it is useful to extract the data content of a page from the
rendering. This way some team members can concentrate on building up the
database of content, while another team member can build the structure of the
site. Local Data allows you to create `.yml`, `.yaml` or `.json` files in a
folder called `data` and makes this information available in your templates.
The `data` folder should be placed in the root of your project i.e. in the same
folder as your project's `source` folder.

Here's an example file at `data/people.yml` with the contents:

``` yaml
friends:
  - Tom
  - Dick
  - Harry
```

Now, anywhere in our template files, we will have access to this data:

``` html
<h1>Friends</h1>
<ol>
  <% data.people.friends.each do |f| %>
  <li><%= f %></li>
  <% end %>
</ol>
```

Which will render:

``` html
<h1>Friends</h1>
<ol>
  <li>Tom</li>
  <li>Dick</li>
  <li>Harry</li>
</ol>
```

Notice that the name of the `.yml` file (people) is the name of the object
which stores the data in your template: `data.people`. This works for
subdirectories as well. If you have a file `data/people/tom.yml`, you can
access it with `data.people.tom`.

You can use JSON to store your data instead of YAML. The above example could be
`data/people.json` instead:

``` json
{
  "friends": [
    "Tom",
    "Dick",
    "Harry"
  ]
}
```
