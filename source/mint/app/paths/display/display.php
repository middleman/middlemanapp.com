<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Display Path
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include(MINT_ROOT.'app/includes/head.php'); ?>
<?php
if (isset($_SERVER['HTTP_USER_AGENT']))
{
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('#AppleWebKit/([.0-9]+)#', $ua, $m))
	{
		$version = $m[1];
		if ($version < 528.16)
		{
			echo <<<HTML
<style type="text/css" title="text/css">
/* <![CDATA[ */
/* GROSS FIX FOR LEGACY WEBKIT BROWSERS */
.scroll table, .scroll table thead { margin-top: -16px; }
.scroll table table, .scroll table table thead { margin-top: 0; }
/* ]]> */
</style>
HTML;
		}
	}
}
?>
<script type="text/javascript" language="javascript">
// <![CDATA[
<?php echo ($Mint->cfg['preferences']['singleColumn']) ? "SI.Mint.singleCol	= true;\r" : ''; ?>
<?php echo ($Mint->cfg['preferences']['collapseVert']) ? "SI.Mint.collapse	= true;\r" : ''; ?>
window.onload	= function() { SI.Mint.staggerPaneLoading(<?php echo ($Mint->cfg['preferences']['staggerPanes']) ? 'true' : 'false'; ?>); SI.Mint.sizePanes(); SI.Mint.onloadScrolls(); };
window.onresize	= function() { SI.Mint.sizePanes(); };
// ]]>
</script>
</head>
<body class="display">
<div id="container">
<?php echo ($Mint->cfg['preferences']['singleColumn']) ? '<div id="single-column" style="width: '.$Mint->cfg['preferences']['singleColumnWidth'].'px;">' : ''; ?>
<?php echo $Mint->display(); ?>
<?php include(MINT_ROOT.'app/includes/foot.php'); ?>
<?php echo ($Mint->cfg['preferences']['singleColumn']) ? "</div>\r" : ''; ?>
</div>
</body>
</html>