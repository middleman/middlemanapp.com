function headerLevel(header) {
  return parseInt(header.get(0).nodeName[1]);
}

function jqShift(jq) {
  return [].shift.call(jq);
}

function chunkLevels(headers) {
  let level;

  if (headers.length) {
    level = headerLevel(headers.first());
  }
  let chunks = [];
  while (headers.length) {
    let result = [jqShift(headers)];
    let currentLevel = level + 1;
    while (headers.length) {
      const header = headers.first();
      currentLevel = headerLevel(header);
      if (currentLevel > level) {
        result.push(jqShift(headers));
      } else {
        break;
      }
    }
    result = $(result);
    if (result.length > 1) {
      result = [$(jqShift(result)), chunkLevels(result)];
    } else {
      result = [result];
    }
    chunks.push(result);
  }
  return chunks;
}

function toToc(levels) {
  const ul = $('<ul/>');
  for (let _i = 0, _len = levels.length; _i < _len; _i++) {
    const headers = levels[_i];
    const header = headers[0];
    const li = $('<li/>');
    li.append("<a href=\"#" + (header.attr('id')) + "\">" + (header.text()) + "</a>");
    if (headers.length > 1) {
      li.append(toToc(headers[1]));
    }
    ul.append(li);
  }
  return ul;
}

export default function() {
  const headers = $('main :header:not(h1)');
  const levels = chunkLevels(headers);
  const toc = toToc(levels);
  const nav = $('<nav id="generated-toc"/>').append(toc);
  return $('main h1').eq(0).after(nav);
}
