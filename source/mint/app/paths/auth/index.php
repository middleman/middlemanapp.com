<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Authorization Path
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 

/******************************************************************************
 Logout
 
 ******************************************************************************/
if (isset($_GET['logout']))
{
	$Mint->logout();
	header('Location:.');
	exit();
}

/******************************************************************************
 Login
 
 ******************************************************************************/
if ($Mint->authenticateRSS())
{
	return;
}
if
(
	!$Mint->isLoggedIn() &&
	(
		$Mint->cfg['mode'] == 'default' ||
		(
			$Mint->cfg['mode'] == 'client' && 
			(
				isset($_GET['preferences']) ||
				isset($_GET['uninstall']) ||
				isset($_GET['import']) ||
				isset($_GET['moved']) ||
				isset($_GET['visits'])
			)
		)
	)
)
{
	if (isset($_POST['MintPath']) && $_POST['MintPath'] == 'Auth')
	{
		if ($Mint->authenticate())
		{
		 return;
		}
	}
	
	include(MINT_ROOT.'app/paths/auth/login.php');
	exit();
}
?>