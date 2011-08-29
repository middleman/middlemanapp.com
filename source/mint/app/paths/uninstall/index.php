<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Installation Path
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 

/******************************************************************************
 Not installed, shouldn't be here
 ******************************************************************************/
if (!$Mint->cfg['installed'])
{
	return;
}

$Mint->tmp['pageTitle'] = 'Uninstall Mint?';

if ($Mint->isLoggedIn())
{
	if (isset($_POST['confirm']))
	{
		$Mint->uninstall($_POST['confirm']);
		header('Location:.');
		exit();
	}
	else
	{
		include(MINT_ROOT.'app/paths/uninstall/uninstall.php');
		exit();
	}
}
?>