require "pathname"

module GuideHelpers
  def page_title
    title = "Middleman: "
    if current_page.data.title
      title << current_page.data.title
    else
      title << t("index.sub_title")
    end
    title
  end

  def edit_guide_url
    p = Pathname(current_page.source_file).relative_path_from(Pathname(root))
    "https://github.com/middleman/middleman-guides/blob/master/#{p}"
  end

  def locale_prefix
    (I18n.locale == :en) ? "" : "/" + I18n.locale.to_s
  end
end
