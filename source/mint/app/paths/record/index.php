<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Record
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 

// Prevent caching (was in js.php)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_GET['js']))
{
	include(MINT_ROOT.'app/paths/record/js.php');
}
else if (isset($_GET['record']))
{
	$Mint->record();
	
	if (!$Mint->paranoid && (isset($_GET['debug']) || $Mint->cfg['debug']))
	{
		echo $Mint->observe($_GET);
		echo $Mint->observe($_COOKIE);
		echo $Mint->getFormattedBenchmark();
		echo '<hr />';
		echo $Mint->observe($Mint);
	}
	if (isset($_GET['serve_img']))
	{
		header("Content-type: image/gif");
		include(MINT_ROOT.'app/images/loaded.gif');
	}
	if (isset($_GET['serve_js']))
	{
		header('Content-type: text/javascript');
		echo '/*Minted*/';
	}
}
// mysql_close(); // crashes PHP 5.3
?>