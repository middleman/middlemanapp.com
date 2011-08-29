<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Installation Instructions
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include(MINT_ROOT.'app/includes/head.php'); ?>
</head>
<body class="mini">
<div id="container">
	<div id="header">
		<h1>MINT</h1>
		<h2>A Fresh Look at Your Site</h2>
	</div>
	
	<div class="notice">
		<p><strong>Adding Mint to your site</strong></p>
		<p>Copy and paste the following code between the <code>&lt;head&gt;</code> tags (but after the <code>&lt;title&gt;</code> tag) on any page of your site that you would like Mint to keep an eye on:</p>
		<pre>&lt;script src="<?php echo (empty($Mint->cfg['installDir']))?$Mint->cfg['installFull']:$Mint->cfg['installDir']; ?>/?js" type="text/javascript"&gt;&lt;/script&gt;</pre>
		<p>Once you've done that, revisit Mint often for a fresh look at your site.</p>
		
		<p><strong>For advanced users:</strong></p>
		<p>If you are running PHP as an Apache Module and not already using <code>php_value auto_prepend_file</code> and the output buffer, add the above <code>&lt;script&gt;</code> tag to the <code>$mint</code> variable in <code><?php echo $Mint->cfg['installDir']; ?>/config/auto.php</code> and the following lines to your <code>.htaccess</code> file:</p>
		<pre>AddType application/x-httpd-php .html .htm
php_value auto_prepend_file <?php echo $Mint->detectDocumentRoot().$Mint->cfg['installDir']; ?>/config/auto.php</pre>
		<p>You will also want to create an <code>.htaccess</code> file for the <code><?php echo $Mint->cfg['installDir']; ?>/</code> directory with the following line:</p>
		<pre>php_value auto_prepend_file none</pre>
		<p>This prevents Mint from tracking itself automatically.</p>
		
		<a href="<?php echo $Mint->cfg['installDir']; ?>/"><img src="styles/<?php echo $Mint->cfg['preferences']['style']; ?>/images/btn-done.png" alt="Done" width="62" height="22" /></a>
	</div>
	<?php include(MINT_ROOT.'app/includes/foot.php'); ?>
</div>
</body>
</html>