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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include(MINT_ROOT.'app/includes/head.php'); ?>
<script type="text/javascript" language="javascript">
// <![CDATA[
window.onload	= function() { SI.onload(); };
// ]]>
</script>
</head>
<body class="preferences">
<div id="container">
	<div id="header">
		<h1><a href="<?php echo $Mint->cfg['installDir'];?>/" class="refresh">MINT</a></h1>
		<h2>A Fresh Look at Your Site</h2>
		<div class="panes">
			<ul class="pages">
				<li class="first-child"><a href="<?php echo $Mint->cfg['installDir'];?>/">View Mint</a></li>
				<li><a href="<?php echo $Mint->cfg['installDir'];?>/?instructions">Instructions</a></li>
				<li><a href="<?php echo $Mint->cfg['installDir'];?>/?uptodate">Check for Updates</a></li>
				<li><a href="<?php echo $Mint->cfg['installDir'];?>/?uninstall">Uninstall</a></li>
				<li class="last-child"><a href="<?php echo $Mint->cfg['installDir'];?>/?logout">Logout</a></li>
			</ul>
		</div>
	</div>
	
	<?php if (!empty($Mint->errors['list'])) {?>
	<div class="notice"><?php echo $Mint->getFormattedErrors(); ?></div>
	<?php } ?>
	
	<div id="preferences">
		<h1>Preferences</h1>
		
		<form method="post" action="<?php echo $Mint->cfg['installDir'];?>/<?php echo (isset($_GET['errors'])) ? '?errors' : ''?>" onsubmit="return SI.Mint.savePreferences();">
			<input type="image" src="styles/<?php echo $Mint->cfg['preferences']['style']; ?>/images/btn-done.png" alt="Done" id="btn-done-top" class="btn" />
			<input type="hidden" name="MintPath" value="Preferences" />
			<input type="hidden" name="action" value="Save" />
			
			<div class="mint-column">
				
				<?php if (isset($_GET['advanced'])):?>
				
				<input type="hidden" name="advanced" value="1" />
				<h2>Advanced</h2>
				<h5>IP Addresses</h5>
				<fieldset>
					<table>
						<tr>
							<td><label>Don't record activity from the following IP addresses (specify a range of IPs with either <code>65.55.165.*</code> or <code>65.55.165.11-130</code>):</label></td>
						</tr>
						<tr>
							<td><span><textarea id="ignoreIPsMint" name="ignoreIPsMint" rows="6" cols="30"><?php echo preg_replace('/[\s,]+/', "\r\n", $Mint->cfg['preferences']['ignoreIPsMint']); ?></textarea></span></td>
						</tr>
					</table>
				</fieldset>
				
				<?php echo $Mint->databaseTablePreferences()?>
					
				<?php else:?>
				
				<h2>Mint</h2>
				<h5>Configuration</h5>
				<fieldset>
					<table>
						<tr>
							<th scope="row">Site Name</th>
							<td><span><input type="text" id="site_display" name="siteDisplay" value="<?php echo $Mint->cfg['siteDisplay'];?>" /></span></td>
						</tr>
						<tr>
							<th scope="row">Site Domain(s)</th>
							<td><span><input type="text" id="siteDomains" name="siteDomains" value="<?php echo $Mint->cfg['siteDomains']; ?>" /></span></td>
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
					</table>
				</fieldset>
				
				<h5>Display</h5>
				<fieldset>
					<table>
						<tr>
							<th scope="row">Style</th>
							<td><span><select name="style" id="style"><?php echo $Mint->generateStyleOptions(); ?></select></span></td>
						</tr>
						<tr>
							<th scope="row" style="white-space: nowrap;">Maximum of</th>
							<td>
								<table class="snug">
									<tr>
										<td><span class="inline" style="margin-left: 0;"><input type="text" id="rows" name="rows" maxlength="4" value="<?php echo $Mint->cfg['preferences']['rows'];?>" class="cinch" /></span></td>
										<td>rows per pane</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" id="secondary" name="secondary" value="1"<?php echo ($Mint->cfg['preferences']['secondary'])?' checked="checked"':''?> /> Show secondary info</label></td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" id="stagger_panes" name="stagger_panes" value="1"<?php echo ($Mint->cfg['preferences']['staggerPanes'])?' checked="checked"':''?> /> Stagger pane loading</label></td>
						</tr>
						<tr class="hide-from-ie">
							<td></td>
							<td><label><input type="checkbox" id="fix_height" name="fix_height" value="1"<?php echo ($Mint->cfg['preferences']['fixHeight'])?' checked="checked"':''?> /> Fix pane height and use scrollbars</label></td>
						</tr>
						<tr class="hide-from-ie">
							<td></td>
							<td><label><input type="checkbox" id="collapse_vert" name="collapse_vert" value="1"<?php echo ($Mint->cfg['preferences']['collapseVert'])?' checked="checked"':''?> /> Collapse vertical whitespace between panes (approximates pane order)</label></td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" id="stripe_all" name="stripe_all" value="1"<?php echo ($Mint->cfg['preferences']['stripeAll'])?' checked="checked"':''?> /> Alternate rows on all panes</label></td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" id="single_column" name="single_column" value="1"<?php echo ($Mint->cfg['preferences']['singleColumn'])?' checked="checked"':''?> /> Enable single-column mode</label></td>
						</tr>
						<tr>
							<td></td>
							<td>
								<table class="snug">
									<tr>
										<td>Single-column width</td>
										<td><span class="inline"><input type="text" id="single_column_width" name="single_column_width" value="<?php echo $Mint->cfg['preferences']['singleColumnWidth'];?>" class="cinch" /></span></td>
										<td>px</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
				
				<h5>Login</h5>
				<fieldset>
					<table>
						<tr>
							<th scope="row">Email</th>
							<td><span><input type="text" id="email" name="email" value="<?php echo $Mint->cfg['email'];?>" autocomplete="off" /></span></td>
						</tr>
						<tr>
							<th scope="row">Password</th>
							<td><span><input type="password" id="password" name="password" value="<?php echo $Mint->cfg['password'];?>" autocomplete="off" /></span></td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" onclick="SI.Cookie.set('MintIgnore',this.checked);" value="true" class="ignore"<?php if (isset($_COOKIE['MintIgnore']) && $_COOKIE['MintIgnore']=='true') { echo ' checked="checked"';} ?> /> Ignore my visits (uses cookies)</label></td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" id="mode" name="mode" value="client" class="ignore"<?php if ($Mint->cfg['mode'] == 'client') { echo ' checked="checked"';} ?> /> Allow anyone to view Mint</label></td>
						</tr>
					</table>
				</fieldset>
				
				<h5>RSS</h5>
				<fieldset>
					<table class="snug">
						<tr>
							<td>Display </td>
							<td><span class="inline"><input type="text" id="rss_rows" name="rss_rows" maxlength="4" value="<?php echo $Mint->cfg['preferences']['rssRows'];?>" class="cinch" /></span></td>
							<td>items per feed</td>
						</tr>
					</table>
				</fieldset>
				
				<?php endif?>
			</div>
			
			<div class="pane-column">
				<h2>Pane Order</h2>
				<table>
					<tr><td><?php echo $Mint->generatePaneOrderList(); ?></td></tr>
				</table>
				<p>Disabling a pane is not a troubleshooting measure. If you suspect a particular Pepper is causing a problem you should uninstall the Pepper using its Uninstall button.</p>
			</div>
			
			<div class="pepper-column">
				<h2 class="first-child">Pepper</h2>
				
				<?php echo $Mint->preferences(); ?>
				
			</div>
			
			<div class="footer">
				<div><input type="image" src="styles/<?php echo $Mint->cfg['preferences']['style']; ?>/images/btn-done.png" alt="Done" id="btn-done-bottom" class="btn" /></div>
			</div>
		</form>
	</div>
	<?php include(MINT_ROOT.'app/includes/foot.php'); ?>
</div>
</body>
</html>