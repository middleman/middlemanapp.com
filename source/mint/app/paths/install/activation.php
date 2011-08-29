<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Activation
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<?php include(MINT_ROOT.'app/includes/head.php'); ?>
</head>
<body class="mini install">
<div id="container">
	<div id="header">
		<h1>MINT</h1>
		<h2>A Fresh Look at Your Site</h2>
	</div>
	
	<div class="pane">
		<h1>Activation Required</h1>
		
		<div id="pane-preferences-content" class="content">
			<form action="" method="post" id="activation_form">
				<input type="hidden" name="MintPath" value="Install" />
				<input type="hidden" name="action" value="Activate" />
				
				<fieldset>
					<table>
						<tr>
							<td colspan="2">Your Activation Key can be found in the email titled "Thank you for purchasing Mint" or by logging into the <a href="http://www.haveamint.com/account/">Mint Account Center</a>.</td>
						</tr>
						<tr>
							<td><span><input type="text" id="activation_key" name="activationKey" value="" /></span></td>
							<td class="btn-row"><input type="image" src="styles/<?php echo $Mint->cfg['preferences']['style']; ?>/images/btn-activate.png" alt="Activate" class="btn-r" /></td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
			
		<div class="footer">
			<div>
			</div>
		</div>
	</div>
	<?php include(MINT_ROOT.'app/includes/foot.php'); ?>
</div>
</body>
</html>