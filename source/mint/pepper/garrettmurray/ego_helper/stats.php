<?php

/******************************************************************************
 Pepper

 Developer: Garrett Murray
 Plug-in Name: Ego Helper

 More info at: http://ego-app.com

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	Foobar is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

 ******************************************************************************/

	define('MINT_ROOT', str_replace('pepper/garrettmurray/ego_helper/stats.php', '', __FILE__));
	define('MINT', TRUE);

	require MINT_ROOT.'app/lib/mint.php';
	require MINT_ROOT.'app/lib/pepper.php';
	require MINT_ROOT.'config/db.php';

	$Mint->loadPepper();

	$EgoHelper =& $Mint->getPepperByClassName('GM_Ego_Helper');

	$debug = isset($_GET['debug']) && !$Mint->paranoid ? TRUE : FALSE;
	$EgoHelper->show_stats($_GET['email'], $_GET['password']);

?>