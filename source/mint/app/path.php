<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Application Path
 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file
header('P3P: CP="NOI NID ADMa OUR IND COM NAV STA LOC"'); // See http://www.p3pwriter.com/LRN_111.asp for definitions
header('Content-Type: text/html; charset=utf-8');

include(MINT_ROOT.'app/lib/mint.php');
include(MINT_ROOT.'app/lib/pepper.php');
include(MINT_ROOT.'config/db.php');

if (!isset($Mint))
{
	echo 'Could not find Mint db.php';
	exit();
}

// Pepper is loaded separately so that the $Mint object already exists in the global space
$Mint->loadPepper();

/******************************************************************************
 Record Path
 
 ******************************************************************************/
if (isset($_GET['js']) || isset($_GET['record']))
{
	include(MINT_ROOT.'app/paths/record/index.php');
	exit();
}

/******************************************************************************
 Ping Path

 ******************************************************************************/
if (isset($_SERVER["HTTP_X_MINT_PING"])) 
{
	$Mint->pinged($_SERVER["HTTP_X_MINT_PING"]);
	exit();
}

/******************************************************************************
 Utility Path (public)
 
 ******************************************************************************/
if (!$Mint->paranoid)
{
	if (isset($_GET['live-debug']))
	{
		$Mint->bakeCookie('MintLiveDebug', !isset($_COOKIE['MintLiveDebug']));
	}
	if (isset($_GET['info']))
	{
		include(MINT_ROOT.'app/paths/util/info.php');
		exit();
	}
	if (isset($_GET['ignore']))
	{
		$Mint->bakeCookie('MintIgnore', 'true', (time() + (3600 * 24 * 365 * 25)));
	}
	if (isset($_GET['gateway']))
	{
		include(MINT_ROOT.'app/paths/util/gateway.php');
		exit();
	}
}

/******************************************************************************
 Update Path

 ******************************************************************************/
if
(
	$Mint->cfg['version'] &&
	$Mint->cfg['version'] < $Mint->version
)
{
	$Mint->update();
}
foreach ($Mint->pepper as $pepperId => $pepper)
{
	if ($Mint->cfg['pepperShaker'][$pepperId]['version'] < $pepper->version)
	{
		$pepper->update();
		$Mint->syncPepper($pepperId);
	}
}

/******************************************************************************
 Installation Path

 ******************************************************************************/
if 
(
	!$Mint->errors['fatal'] && 
	(
		!$Mint->cfg['installed'] || 
		(
			isset($_POST['MintPath']) && 
			$_POST['MintPath'] == 'Install'
		)
	)
)
{
	include(MINT_ROOT.'app/paths/install/index.php');
}

/******************************************************************************
 Widget Path

 Manages own authentication
 ******************************************************************************/
if (isset($_POST['widget']))
{
	echo $Mint->widget();
	exit();
}

/******************************************************************************
 Authorization Path

 Login/logout 
 ******************************************************************************/
if (!$Mint->errors['fatal'])
{
	include(MINT_ROOT.'app/paths/auth/index.php');
}

/******************************************************************************
 Utility Path (private)
 
 Add additions to auth/index.php
 ******************************************************************************/
if (isset($_GET['visits']))
{
	include(MINT_ROOT.'app/paths/util/visits.php');
	exit();
}
if (isset($_GET['moved']))
{
	$Mint->updateAfterMove();
}
if (isset($_GET['import']))
{
	include(MINT_ROOT.'app/paths/util/import.php');
	exit();
}
if (isset($_GET['uptodate']))
{
	$Mint->upToDate();
	exit();
}
if (isset($_GET['recover']))
{
	include(MINT_ROOT.'app/paths/util/recover.php');
	exit();
}

/******************************************************************************
 Uninstall Path

 ******************************************************************************/
if (isset($_GET['uninstall']))
{
	include(MINT_ROOT.'app/paths/uninstall/index.php');
}

/******************************************************************************
 Custom Path

 RSS, embedded and other custom function calls. Must immediately follow 
 Authorization or we'll have a bit of a security hole.
 ******************************************************************************/
if 
(
	isset($_GET['custom']) || 
	(
		isset($_POST['MintPath']) && 
		$_POST['MintPath'] == 'Custom'
	) || 
	isset($_GET['RSS'])
)
{
	include(MINT_ROOT.'app/paths/custom/index.php');
	exit();
}

/******************************************************************************
 Preference Path

 ******************************************************************************/
if 
(
	isset($_GET['preferences']) ||
	(
		isset($_POST['MintPath']) && 
		$_POST['MintPath'] == 'Preferences'
	) ||
	isset($_GET['instructions'])
)
{
	include(MINT_ROOT.'app/paths/preferences/index.php');
}

/******************************************************************************
 Feedback Path

 Positive feedback notices
 ******************************************************************************/
if ($Mint->feedback['feedback'] && empty($Mint->errors['list']))
{
	include(MINT_ROOT.'app/paths/feedback/index.php');
	exit();
}

/******************************************************************************
 Fatal Error Path

 Database and other
 ******************************************************************************/
if ($Mint->errors['fatal'])
{
	include(MINT_ROOT.'app/paths/errors/index.php');
	exit();
}
$Mint->bakeCookie('MintConfigurePepper', '', time() - 365 * 24 * 60 * 60);

/******************************************************************************
 Display (default) Path
 
 ******************************************************************************/
include(MINT_ROOT.'app/paths/display/index.php');
?>