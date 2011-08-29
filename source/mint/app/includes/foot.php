<div id="donotremove">
	<?php if (isset($Mint)) { echo $Mint->getFormattedVersion(); } ?> &copy; 2004-<?php echo date("Y"); ?> <a href="http://www.haveamint.com/contact">Shaun Inman</a>. All rights reserved.
	Available at <a href="http://www.haveamint.com/">haveamint.com</a>. <?php if (isset($Mint) && $Mint->cfg['mode'] == 'client') { echo '<span>(Open-mode Enabled)</span>'; } ?>
</div>
<?php
if (!$Mint->paranoid)
{
	if (isset($_GET['benchmark']))
	{
		echo $Mint->getFormattedBenchmark();
	}
	if (isset($_GET['observe']))
	{
		echo '<div class="observe">'.$Mint->observe($Mint).'</div>'; 
	}
}
?>
<script type="text/javascript" language="javascript">
// <![CDATA[
SI.onbeforeload();
// ]]>
</script>