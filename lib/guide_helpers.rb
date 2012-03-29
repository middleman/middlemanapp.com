module GuideHelpers
  def page_title
    title = "Middleman: "
    if data.page.title
      title << data.page.title
    else
      title << "Hand-crafted frontend development"
    end
    title
  end
  
  def is_guide_page?
    request.path =~ /guides/
  end
  
  def edit_guide_url
    file_name = request.path.split("guides/").last
    "https://github.com/middleman/middleman-guides/blob/2x/source/guides/#{file_name}.markdown"
  end
end
