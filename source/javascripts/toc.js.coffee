# Generates a Table of Contents from headers
$ ->
  # from h2 => 2
  headerLevel = (header) ->
    parseInt(header.get(0).nodeName[1])

  # Remove the first element of a jQuery array
  jqShift = (jq) ->
    [].shift.call(jq)

  # split the list of headers into lists that all start with the given level
  chunkLevels = (headers) ->
    level = headerLevel(headers.first()) if headers.length
    chunks = []
    while headers.length
      result = [ jqShift(headers) ]
      currentLevel = level + 1
      while headers.length
        header = headers.first()
        currentLevel = headerLevel(header)

        if currentLevel > level
          result.push(jqShift(headers))
        else
          break

      result = $(result)
      if result.length > 1
        result = [ $(jqShift(result)), chunkLevels(result) ]
      else
        result = [ result ]
      chunks.push result

    chunks

  # Turn the nested levels arrays into a nested list
  toToc = (levels) ->
    ul = $('<ul/>')
    for headers in levels
      header = headers[0]
      li = $('<li/>')
      li.append("<a href=\"##{header.attr('id')}\">#{header.text()}</a>")

      if headers.length > 1
        li.append toToc(headers[1])

      ul.append li

    ul

  headers = $(':header[id^="toc_"]:not(h1)')
  levels = chunkLevels(headers)
  toc = toToc(levels)
  nav = $('<nav id="generated-toc"/>').append(toc)

  $('#toc_0').after(nav)