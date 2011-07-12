xml.instruct!
xml.feed "xmlns" => "http://www.w3.org/2005/Atom" do
  xml.title "Middleman Blog"
  xml.subtitle "Hand-crafted for static frontend development"
  xml.id "http://middlemanapp.com"
  xml.link "href" => "http://middlemanapp.com"
  xml.link "href" => "http://middlemanapp.com/blog/feed.xml", "rel" => "self"
  xml.updated data.blog.articles.first.date.to_time.iso8601
  xml.author { xml.name "Thomas Reynolds" }

  data.blog.articles.each do |article|
    xml.entry do
      xml.title article.title
      xml.link "rel" => "alternate", "href" => article.url
      xml.id article.url
      xml.published article.date.to_time.iso8601
      xml.updated article.date.to_time.iso8601
      xml.author { xml.name "Thomas Reynolds" }
      xml.summary article.summary, "type" => "html"
      xml.content article.body, "type" => "html"
    end
  end
end