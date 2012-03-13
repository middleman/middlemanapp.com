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
  
  def edit_guide_url
    "https://github.com/middleman/middleman-guides/blob/master/source/#{current_page.path}.markdown"
  end
end
