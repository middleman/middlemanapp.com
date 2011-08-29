<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Configuration
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include(MINT_ROOT.'app/includes/head.php'); ?>
</head>
<body class="mini install">
<div id="container">
	<div id="header">
		<h1>MINT</h1>
		<h2>A Fresh Look at Your Site</h2>
	</div>
	
	<div class="pane">
		<h1>Install Mint</h1>
		
		<div id="pane-preferences-content" class="content">
			<form method="post" action="">
				<input type="hidden" name="MintPath" value="Install" />
				<input type="hidden" name="action" value="Configure" />
				<input type="hidden" name="activationKey" value="<?php echo (isset($_POST['activationKey']))?$_POST['activationKey']:'LOCALHOST'; ?>" />
				
				<h2 class="first-child">Create Login</h2>
				<fieldset>
					<table>
						<tr>
							<th scope="row">Email</th>
							<td><span><input type="text" id="email" name="email" value="" /></span></td>
						</tr>
						<tr>
							<th scope="row">Password</th>
							<td><span><input type="password" id="password" name="password" value="" /></span></td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" onclick="SI.Cookie.set('MintIgnore',this.checked);" value="true" class="ignore" /> Ignore my visits (uses cookies)</label></td>
						</tr>
					</table>
				</fieldset>
					
				<h2>Configuration</h2>
				<fieldset>
					<table>
						<tr>
							<th scope="row">Site Name</th>
							<td><span><input type="text" id="siteDisplay" name="siteDisplay" value="" /></span></td>
						</tr>
						<tr>
							<th scope="row">Site Domain(s)</th>
							<td><span><input type="text" id="siteDomains" name="siteDomains" value="<?php echo $Mint->cfg['installTrim']; ?>" /></span></td>
						</tr>
						<tr>
							<th scope="row">Mint Location</th>
							<td><span><input type="text" id="installFull" name="installFull" value="<?php echo $Mint->cfg['installFull']; ?>" /></span></td>
						</tr>
						<tr>
							<td></td>
							<td>Eg. <code>http://www.site.com/mint</code></td>
						</tr>
						<tr>
							<th scope="row">Local Time</th>
							<td><span><select name="offset" id="offset"><?php echo $Mint->generateOffsetOptions(); ?></select></span></td>
						</tr>
						<tr>
							<td colspan="2" class="btn-row"><input type="image" src="styles/<?php echo $Mint->cfg['preferences']['style']; ?>/images/btn-install.png" alt="Install" class="btn" /></td>
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

<script type="text/javascript" language="javascript">
// <![CDATA[

// Auto-detect offset.
var offset = ((new Date()).getTimezoneOffset())/-60;
var options = document.getElementById('offset').getElementsByTagName('option');
for (var o = 0; o < options.length; o++)
{
	var opt = options[o];
	if (opt.value == offset)
	{
		opt.selected = true;
		break;
	}
}

document.forms[0].onsubmit = function() {
	var errors = new Array();
	for (var i=0; i<this.elements.length; i++) {
		var e = this.elements[i];
		if (e.name && e.name=='siteDisplay' && !e.value.length) {
			errors[errors.length] = 'Please enter your site\'s name.';
			}
		if (e.name && e.name=='siteDomains' && !e.value.length) {
			errors[errors.length] = 'Please enter the domain(s) this Mint installation will track.';
			}
		if (e.name && e.name=='email' && !e.value.length) {
			errors[errors.length] = 'Please enter an email address.';
			}
		else if (e.name && e.name=='email' && !(e.value.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)) {
			errors[errors.length] = 'Please enter a valid email address.';
			}
		if (e.name && e.name=='password' && !e.value.length) {
			errors[errors.length] = 'Please enter a password.';
			}
		}
	
	if (errors.length) {
		alert(errors.join('\r\n'))
		return false;
		}
	}
// ]]>
</script>

</body>
</html>