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
 Already installed, shouldn't be here
 ******************************************************************************/
if ($Mint->cfg['installed'])
{
	return;
}

$Mint->tmp['pageTitle'] = 'Install Mint';

/******************************************************************************
 EULA
 ******************************************************************************/
if (!isset($_POST['action']))
{
	// Present the EULA
	$Mint->tmp['pageTitle'] = 'End-User License Agreement';
	include(MINT_ROOT.'app/paths/install/eula.php');
}
else
{
	$MintAction = $_POST['action'];
	if ($MintAction == 'Accept' && preg_match('/^(127\.0\.0\.1|localhost)(:\d+)?$/i', $Mint->cfg['installTrim']))
	{
		$MintAction = 'Activate';
	}
	switch($MintAction)
	{
		/**********************************************************************
		 Activation
		 
		 REMOVING OR MODIFYING THIS CODE WILL TERMINATE YOUR LICENSE.
		 That's right, I didn't even try to obfuscate the activation code. I 
		 figure this way, if you do decide to remove or modify this bit then 
		 there can be no confusion--you're not being clever, you're just taking 
		 food off this honest, hardworking developer's table.
		 **********************************************************************/
		case 'Accept':
			if (isset($_POST['accept']))
			{
				$Mint->tmp['pageTitle'] = 'Activation';
				include(MINT_ROOT.'app/paths/install/activation.php');
			}
			else
			{
				$Mint->logError('To continue with installation you must accept the Mint End User License Agreement.');
				include(MINT_ROOT.'app/paths/errors/index.php');
			}
		break;
		
		/**********************************************************************
		 Configuration
		 **********************************************************************/
		case 'Activate':
			if ($Mint->_verifyLicense())
			{
				include(MINT_ROOT.'app/paths/install/configuration.php');
			}
			else
			{
				include(MINT_ROOT.'app/paths/errors/index.php');
			}
		break;
		
		/**********************************************************************
		 Instructions
		 **********************************************************************/
		case 'Configure':
			if ($Mint->install())
			{
				include(MINT_ROOT.'app/paths/install/instructions.php');
			}
			else
			{
				include(MINT_ROOT.'app/paths/errors/index.php');
			}
		break;
	}
}
exit();
?>