<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Preferences Path
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 

if (isset($_GET['instructions']))
{
	$Mint->tmp['pageTitle'] = 'Install Mint';
	include(MINT_ROOT.'app/paths/install/instructions.php');
	exit();
}

if (isset($_POST['MintPath']) && $_POST['MintPath'] == 'Preferences')
{
	switch($_POST['action'])
	{
		case 'Save':
			$Mint->savePreferences();
			return;
		break;
		
		case 'Install Pepper':
			$Mint->tmp['pageTitle'] = 'Install Pepper';
			$Mint->installPepper($_POST['src']);
			return;
		break;
		
		case 'Uninstall Pepper':
			$Mint->tmp['pageTitle'] = 'Uninstall Pepper';
			$Mint->uninstallPepper($_POST['pepperID']);
			return;
		break;
	}
}

$Mint->tmp['pageTitle'] = 'Preferences';
include(MINT_ROOT.'app/paths/preferences/preferences.php');
exit();
?>