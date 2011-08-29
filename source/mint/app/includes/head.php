<?php
if (!isset($Mint->tmp['pageTitle']))
{
	$Mint->tmp['pageTitle'] = $Mint->cfg['siteDisplay'];
}
if (!isset($Mint->tmp['headTags']))
{
	$Mint->tmp['headTags'] = '';
}
?>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Mint: <?php echo $Mint->tmp['pageTitle']; ?></title>
<link rel="apple-touch-icon" type="image/png" href="app/images/webclip.png" />
<link rel="shortcut icon" type="image/png" href="app/images/favicon.png" />
<link rel="stylesheet" type="text/css" href="styles/<?php echo $Mint->cfg['preferences']['style']; ?>/style.css" />
<script type="text/javascript" src="app/scripts/si-object-mint.js"></script>
<!--[if IE]>
<link type="text/css" href="app/styles/iepc.css" rel="stylesheet" />
<script type="text/javascript" src="app/scripts/iepc.js"></script>
<![endif]-->
<?php echo $Mint->tmp['headTags']; ?>