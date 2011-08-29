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

$response = $Mint->_gateway('Compatibility Suite Ping','dt='.time());

if ($response != 'CONNECTED')
{
	$h1 = '<em>What?</em> I am having trouble hearing you!';
	$p	= 'Your server could not connect to the gateway.';
}
else
{
	$h1 = 'Read you loud and clear, good buddy!';
	$p	= 'Connected to the gateway successfully';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Mint: Gateway Connectivity</title>
<style type="text/css" title="text/css" media="screen">
/* <![CDATA[ */
body
{
	position: relative;
	background-color: #FFF;
	margin: 0;
	padding: 48px 0;
	font: 76%/1.6em "Lucida Grande", Verdana, Arial, sans-serif;
	color: #333;
	text-align: center;
}

div
{
	width: 400px;
	margin: 0 auto;
	text-align: left;
}

h1
{
	font-weight: normal;
	line-height: 1.2em;
}

/* ]]> */
</style>
</head>
<body>
<div>
<h1>Mint says, &#8220;<?php echo $h1; ?>&#8221;</h1>

<p><?php echo $p; ?></p>

</div>
</body>
</html>