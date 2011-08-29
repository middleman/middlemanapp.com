<?php
/******************************************************************************
 Mint
 
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Attaches Mint JavaScript to all PHP-parsed pages automatically 
 ******************************************************************************/
function Minted($page) 
{
	$mint	= '<script src="/mint/?js" type="text/javascript"></script>';
	$pages	= array(); // Add pages (relative to the public site root) that Mint should ignore
	
	if 
	(
		(strpos($page,'<frameset') !== false && strpos($page,'<body') === false) || 
		(!empty($pages) && in_array((isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF']))?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME'], $pages))
	)
	{
		return $page;
	}
	
	$replace = array
	(
		'</head>',
		'</HEAD>'
	);
	return str_replace($replace, "{$mint}\r</head>", $page);
}
ob_start("Minted");
?>