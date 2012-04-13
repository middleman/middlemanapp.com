---
title: Remote API Proxy Extension
---

# Remote API Proxy Extension

Middleman 2.1 ships with an official extension to support loading remote APIs during the development cycle. Browsers disallow AJAX request cross-domain as a security measure, but if you're building a stand-alone frontend (on the localhost domain) you need to be able to test against the real backend.

The Remote API Proxy allows you to mount a remote API, Twitter Search for example, inside your domain during development. Simply install the gem:

    :::bash
    gem install middleman-proxy

Then activate the extension in your `config.rb`:

    :::ruby
    activate :proxy

Next, we will mount the Twitter Search API inside our app in `config.rb`:

    :::ruby
    proxy '/twitter', "http://search.twitter.com"

Finally, from our front-end we can now do local AJAX to get remote search results. Here's an example using jQuery:

    :::javascript
    $.get("/twitter/search.json", { q: "@middlemanapp" }, function(data) {
      // Handle the search results for @middlemanapp
    });


## Production

If you own the API you are connecting to, chances are you will deploy your static frontend app along-side the api. Now that your API and app are on the same domain, you won't need a proxy any more.

If you don't own the API, either you can build a proxy into your backend (useful for redundancy and avoiding API downtime) or use a service like [Strobe] which provides static frontend hosting and an integrated API proxy.

[Strobe]: http://www.strobecorp.com/


## Cross-origin resource sharing (CORS)

If you own the API you are connecting to, you can also provide support for CORS by supplying the appropriate response headers to the client.

For example, with a Sinatra application:

    :::ruby
    headers 'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, PUT, POST, OPTIONS, HEAD, DELETE, PATCH',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age' => '1728000',
        'Allow' => 'GET, PUT, POST, OPTIONS, HEAD, DELETE, PATCH'

Make sure to add support in your application for OPTIONS requests from the client.

See these links for more information:

* http://www.w3.org/TR/cors/
* http://en.wikipedia.org/wiki/Cross-origin_resource_sharing
* http://enable-cors.org/
* https://developer.mozilla.org/en/http_access_control
