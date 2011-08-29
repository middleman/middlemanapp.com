<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Utility Path
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 

return;

function getFormattedPhpInfo()
{
	$html = '';
	ob_start();
	phpinfo();
	$html .= ob_get_contents();
	ob_clean();

	$safariOnly = <<<HERE
opacity: .4;
}

select:hover
{
	opacity: 1;
	cursor: pointer;
HERE;

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'AppleWebKit') === false)
	{
		$safariOnly = '';
	}
	
	$style = <<<HERE

body
{
	position: relative;
	background-color: #FFF;
	color: #333;
	font-size: 76%;
	font-family: sans-serif;
	text-align: center;
	padding: 0;
	margin: 0;
}

select
{
	position: fixed;
	top: 14px;
	width: 160px;
	margin: 0 0 0 440px;
	$safariOnly
}

p
{
	margin-top: 0;
}

pre
{
	color: #6B8DA6;
	margin: 0;
	font-family: monospace;
}

.center
{
	position: relative;
	width: 600px;
	margin: 0 auto;
	text-align: left;
	font-size: 1.0em;
}

h1 a[href]
{
	position: absolute;
	top: 0;
	left: 0;
	
	font-size: 0.25em;
	text-decoration: none;
	
	color: #FFF;
	text-shadow: 2px 2px #89B04C;
	background-color: #BCE27F;
	padding: 16px 24px 4px 6px;
	font-weight: bold;
}

h1 a[href]:hover
{
	color: #FFC;
	padding: 15px 23px 5px 7px;
}

h1,
h2,
h3
{
	font-weight: normal;
}

table:first-child h1
{
	color: #333;
	margin: 1.0em 0 0;
}

h1
{
	color: #999;
	font-size: 4.0em;
}

h2
{
	color: #333;
	font-size: 2.4em;
}

h3
{
	color: #999;
	font-size: 1.8em;
}

i
{
	font-style: normal;
	color: #999;
}

table
{
	margin-bottom: 2.0em;
	border-collapse: collapse;
}

td,
th
{
	border-bottom: 1px solid #DDD;
	padding: 8px 24px 8px 0;
	text-align: left;
	vertical-align: baseline;
}

th
{
	border-bottom: 4px solid #BCE27F;
}

tr:first-child td
{
	border-top: 4px solid #BCE27F;
}

table:first-child td,
tr.v td
{
	border: none;
}

tr.v td
{
	padding: 0.8em 1.0em;
	border: 1px solid #F2F2C2;
	background-color: #FFC;
}

tr.last-child td
{
	border-bottom-width: 2px;
}

.e,
.h
{
	font-weight: bold;
}

.e
{
	white-space: nowrap; 
	width: 200px;
}

*[style='color: #FFFFFF']
{
	text-shadow: 2px 2px #BBB;
}

HERE;

	$script = <<<HERE

<script type="text/javascript" src="app/scripts/si-object-mint.js"></script>
<script type="text/javascript" language="javascript">
// <![CDATA[

window.onload = function()
{
	var headings = document.getElementsByTagName('h2');
	var menu = '';
	
	menu += '<select onchange="SI.Scroll.to(this.options[this.selectedIndex].value);">';
	menu += '<option value="top">Top</option>';
	for (var i = 0; i < headings.length; i++) {
		menu += '<option value="' + headings[i].id + '">' + headings[i].innerHTML + '</option>';
		}
	menu += '</select>';
	document.getElementById('container').innerHTML += menu;
}

// ]]>
</script>

HERE;
	$html = str_replace('<h1 class="p">', '<h1 id="top" class="p">', $html);
	$html = str_replace('<div class="center">', '<div id="container" class="center">', $html);
	$html = preg_replace("#<h2>(<a[^>]*>)?([^<]+)(</a>)?</h2>#ie", "'<h2 id=\"'.preg_replace('#[^a-z0-9]+#i', '-', '$2').'\">$2</h2>'", $html);
	$html = preg_replace("#<tr>(.+)</tr>\s*</table>#i", "<tr class=\"last-child\">$1</tr>\r</table>", $html);
	$html = preg_replace('#(<style type="text/css"><!--).*(//--></style>)#is', "$1$style$2$script", $html);
	$html = preg_replace('#</?(br|hr|img)[^>]*>#is', '', $html);
	
	return $html;
}

echo getFormattedPhpInfo();
?>