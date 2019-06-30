function showHideReset() {
  var val = this.value === undefined || this.value === "" ? "none" : "block";
  this.parentNode.parentNode.querySelector(
    ".searchbox__reset"
  ).style.display = val;
}

anchors.add(
  ".js-anchor-links h2, .js-anchor-links h3, .js-anchor-links h4, .js-anchor-links h5, .js-anchor-links h6"
);

var ds = docsearch({
  apiKey: "a1e1ce4270154213659b1056053824cd",
  indexName: "middleman",
  inputSelector: "#search-input"
});

var $input = document.querySelector(".searchbox__input");
var $reset = document.querySelector(".searchbox__reset");

// Focus input on reset
$reset.addEventListener("click", function() {
  ds.autocomplete.autocomplete.setVal("");
  $input.focus();
  showHideReset.call($input);
});

$input.addEventListener("input", showHideReset);
