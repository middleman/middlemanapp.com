<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Launcher
 ******************************************************************************/
if (!defined('MINT_ROOT')) { define('MINT_ROOT', ''); }
if (isset($_GET['errors'])) { error_reporting(E_ALL); } else { error_reporting(0); }

define('MINT',true);
include(MINT_ROOT.'app/path.php');
?>